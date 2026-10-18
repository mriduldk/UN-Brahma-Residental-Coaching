<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdmitCardDownloadLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_application_id',
        'application_code',
        'user_id',
        'user_name',
        'user_type',
        'ip_address',
        'user_agent',
        'download_type',
        'organization_id',
        'downloaded_at',
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
    ];

    /**
     * The application whose admit card was downloaded.
     */
    public function application()
    {
        return $this->belongsTo(StudentApplication::class, 'student_application_id');
    }

    /**
     * The authenticated user who initiated the download (if any).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The organization associated with this admit card generation.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    /**
     * Record a single admit card download or print action.
     */
    public static function logDownload($application, string $downloadType = 'single_pdf', ?Request $request = null): ?self
    {
        try {
            $request = $request ?? request();
            $user = auth()->user();

            $ip = $request->header('x-forwarded-for')
                ? trim(explode(',', $request->header('x-forwarded-for'))[0])
                : $request->ip();

            $userType = 'guest';
            $userName = 'Anonymous / Guest';
            $userId = null;

            if ($user) {
                $userId = $user->id;
                $userName = $user->name ?? ('User #' . $user->id);
                $role = strtolower((string) ($user->role ?? ''));

                if ($role === 'student' || ($user->relationLoaded('student') ? $user->student : $user->student()->exists())) {
                    $userType = 'student';
                } else {
                    $userType = 'admin';
                }
            }

            $appId = is_object($application) ? $application->id : $application;
            $appCode = is_object($application) ? ($application->application_code ?? null) : null;
            $orgId = is_object($application) && isset($application->application_form?->organization_id)
                ? $application->application_form->organization_id
                : (is_object($application) && isset($application->application_form?->institution?->organization_id)
                    ? $application->application_form->institution->organization_id
                    : null);

            return self::create([
                'student_application_id' => $appId,
                'application_code'       => $appCode,
                'user_id'                => $userId,
                'user_name'              => $userName,
                'user_type'              => $userType,
                'ip_address'             => $ip ?? '127.0.0.1',
                'user_agent'             => substr((string) $request->userAgent(), 0, 500),
                'download_type'          => $downloadType,
                'organization_id'        => $orgId,
                'downloaded_at'          => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to log admit card download: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Record bulk download action for multiple applications and return created log metadata.
     */
    public static function logBulkDownload(iterable $applications, string $downloadType = 'bulk_pdf', ?Request $request = null): array
    {
        $logsMap = [];
        try {
            $request = $request ?? request();
            $user = auth()->user();

            $ip = $request->header('x-forwarded-for')
                ? trim(explode(',', $request->header('x-forwarded-for'))[0])
                : $request->ip();

            $userType = 'guest';
            $userName = 'Anonymous / Guest';
            $userId = null;

            if ($user) {
                $userId = $user->id;
                $userName = $user->name ?? ('User #' . $user->id);
                $role = strtolower((string) ($user->role ?? ''));

                if ($role === 'student' || ($user->relationLoaded('student') ? $user->student : $user->student()->exists())) {
                    $userType = 'student';
                } else {
                    $userType = 'admin';
                }
            }

            $now = now();
            $userAgent = substr((string) $request->userAgent(), 0, 500);

            foreach ($applications as $app) {
                $appId = is_object($app) ? $app->id : $app;
                $appCode = is_object($app) ? ($app->application_code ?? null) : null;
                $orgId = is_object($app) && isset($app->application_form?->organization_id)
                    ? $app->application_form->organization_id
                    : (is_object($app) && isset($app->application_form?->institution?->organization_id)
                        ? $app->application_form->institution->organization_id
                        : null);

                $log = self::create([
                    'student_application_id' => $appId,
                    'application_code'       => $appCode,
                    'user_id'                => $userId,
                    'user_name'              => $userName,
                    'user_type'              => $userType,
                    'ip_address'             => $ip ?? '127.0.0.1',
                    'user_agent'             => $userAgent,
                    'download_type'          => $downloadType,
                    'organization_id'        => $orgId,
                    'downloaded_at'          => $now,
                ]);

                if ($log && $appId) {
                    $logsMap[$appId] = [
                        'id' => $log->id,
                        'downloaded_at' => $now,
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to log bulk admit card downloads: ' . $e->getMessage());
        }

        return $logsMap;
    }
}
