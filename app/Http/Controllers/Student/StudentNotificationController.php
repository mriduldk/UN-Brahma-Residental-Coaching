<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\School\NotificationLog;
use Illuminate\Http\Request;

class StudentNotificationController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Fetch all sent notifications for this student
        $notifications = NotificationLog::where('user_id', $userId)
            ->where('status', 'sent')
            ->with('notification')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('student.notifications.index', compact('notifications'));
    }
}
