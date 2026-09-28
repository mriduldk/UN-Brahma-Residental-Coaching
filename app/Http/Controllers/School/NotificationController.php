<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School\Notification;
use App\Models\School\NotificationLog;
use App\Models\School\NotificationTemplate;
use App\Models\School\SchoolClass;
use App\Models\School\SchoolEnrollment;
use App\Models\School\SchoolStaff;
use App\Models\School\AcademicYear;
use App\Models\Student;
use App\Services\Msg91Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index()
    {
        $orgId = auth()->user()->currentOrganization()->id;
        
        $stats = [
            'total' => Notification::where('organization_id', $orgId)->count(),
            'pending' => Notification::where('organization_id', $orgId)->where('status', 'pending')->count(),
            'completed' => Notification::where('organization_id', $orgId)->where('status', 'completed')->count(),
            'failed' => Notification::where('organization_id', $orgId)->where('status', 'failed')->count(),
        ];

        $recentNotifications = Notification::where('organization_id', $orgId)
            ->with('template')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('school.notifications.index', compact('stats', 'recentNotifications'));
    }

    public function compose()
    {
        $orgId = auth()->user()->currentOrganization()->id;
        $templates = NotificationTemplate::where('organization_id', $orgId)->where('is_active', true)->get();
        $classes = SchoolClass::where('organization_id', $orgId)->orderBy('name')->get();
        
        return view('school.notifications.compose', compact('templates', 'classes'));
    }

    public function send(Request $request)
    {
        $orgId = auth()->user()->currentOrganization()->id;
        
        $request->validate([
            'recipient_type' => 'required|string',
            'channel' => 'required|in:sms,email,whatsapp,push',
            'content' => 'required|string',
            'class_id' => 'required_if:recipient_type,class',
        ]);

        $activeYear = AcademicYear::where('organization_id', $orgId)
            ->where('is_active', true)
            ->first();

        Log::info('Notification Send Initiated', [
            'org_id' => $orgId,
            'recipient_type' => $request->recipient_type,
            'channel' => $request->channel,
            'active_year' => $activeYear?->id
        ]);

        try {
            // 1. Identify Recipients
            $recipients = collect();
            
            if ($request->recipient_type === 'all_students') {
                if ($activeYear) {
                    $enrollments = SchoolEnrollment::with('student')
                        ->where('academic_year_id', $activeYear->id)
                        ->where('status', 'active')
                        ->get();
                    $recipients = $enrollments->pluck('student');
                }
            } elseif ($request->recipient_type === 'class') {
                if (!$activeYear) return back()->with('error', 'Active academic year not found.');
                
                $enrollments = SchoolEnrollment::with('student')
                    ->where('academic_year_id', $activeYear->id)
                    ->where('class_id', $request->class_id)
                    ->where('status', 'active')
                    ->get();
                $recipients = $enrollments->pluck('student');
            } elseif ($request->recipient_type === 'staff') {
                $recipients = SchoolStaff::where('organization_id', $orgId)->where('is_active', true)->get();
            }

            // Filter out recipients without name/contact
            $recipients = $recipients->filter(fn($r) => $r !== null);

            if ($recipients->isEmpty()) {
                Log::warning('Notification Send: No recipients found');
                return back()->withInput()->with('error', 'No recipients found for the selected segment.');
            }

            // 2. Create Notification Record
            $notification = Notification::create([
                'organization_id' => $orgId,
                'template_id' => $request->template_id,
                'title' => $request->title,
                'content' => $request->content,
                'channel' => $request->channel,
                'recipient_type' => $request->recipient_type,
                'recipient_count' => $recipients->count(),
                'status' => 'processing',
            ]);

            // 3. Dispatch Messages
            $msgService = new Msg91Service();
            $sentCount = 0;
            $failedCount = 0;

            foreach ($recipients as $recipient) {
                $contact = ($request->channel === 'email') ? $recipient->email : $recipient->phone_number;
                
                if (!$contact) {
                    NotificationLog::create([
                        'notification_id' => $notification->id,
                        'user_id' => $recipient->user_id ?? null,
                        'recipient_contact' => 'N/A',
                        'status' => 'failed',
                        'error_message' => 'Missing contact info',
                    ]);
                    $failedCount++;
                    continue;
                }

                // Replace Placeholders
                $finalContent = str_replace(
                    ['{name}', '{student_name}', '{full_name}'], 
                    [$recipient->full_name, $recipient->full_name, $recipient->full_name],
                    $request->content
                );

                $success = false;
                $error = null;

                try {
                    if ($request->channel === 'email') {
                        Mail::raw($finalContent, function($message) use ($contact, $request) {
                            $message->to($contact)->subject($request->title ?? 'School Notification');
                        });
                        $success = true;
                    } elseif ($request->channel === 'sms' || $request->channel === 'whatsapp') {
                        $success = $msgService->sendMessage($contact, $finalContent);
                    } else {
                        $success = true; // Push placeholder
                    }
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                    Log::error("Notification Error ($contact): " . $error);
                }

                NotificationLog::create([
                    'notification_id' => $notification->id,
                    'user_id' => $recipient->user_id ?? null,
                    'recipient_contact' => $contact,
                    'status' => $success ? 'sent' : 'failed',
                    'error_message' => $error,
                ]);

                if ($success) $sentCount++;
                else $failedCount++;
            }

            $notification->update([
                'status' => $failedCount === 0 ? 'completed' : ($sentCount > 0 ? 'completed' : 'failed')
            ]);

            return redirect()->route('school.notifications.index')->with('success', "Notification sent successfully to $sentCount recipients.");

        } catch (\Exception $e) {
            Log::error('Critical Notification Failure: ' . $e->getMessage());
            return back()->withInput()->with('error', 'An error occurred while sending the notification: ' . $e->getMessage());
        }
    }

    public function logs()
    {
        $orgId = auth()->user()->currentOrganization()->id;
        $logs = NotificationLog::whereHas('notification', function($q) use ($orgId) {
            $q->where('organization_id', $orgId);
        })->with('notification', 'user')->orderBy('created_at', 'desc')->paginate(20);

        return view('school.notifications.logs', compact('logs'));
    }
}
