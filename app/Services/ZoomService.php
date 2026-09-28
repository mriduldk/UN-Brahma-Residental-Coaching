<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Http;
use App\Models\School\OrganizationConfiguration;

class ZoomService
{
    protected $baseUrl = 'https://api.zoom.us/v2';

    public function createMeeting($data, $orgId = null)
    {
        // Fallback to manual link if provided
        if (!empty($data['manual_meeting_url'])) {
            return [
                'meeting_id' => $data['manual_meeting_id'] ?? 'MANUAL-' . rand(1000, 9999),
                'join_url' => $data['manual_meeting_url'],
                'start_url' => $data['manual_meeting_url'],
                'password' => $data['manual_password'] ?? '',
            ];
        }

        $accountId = env('ZOOM_ACCOUNT_ID');
        $clientId = env('ZOOM_CLIENT_ID');
        $clientSecret = env('ZOOM_CLIENT_SECRET');

        if (!$accountId || !$clientId || !$clientSecret) {
            throw new \Exception('Zoom API credentials not configured in environment.');
        }

        $accessToken = $this->getAccessToken($accountId, $clientId, $clientSecret);

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUrl}/users/me/meetings", [
                'topic' => $data['topic'] ?? 'Live Class',
                'type' => 2, // Scheduled meeting
                'start_time' => date('Y-m-d\TH:i:s', strtotime($data['start_time'])),
                'duration' => $data['duration'] ?? 40,
                'timezone' => 'Asia/Kolkata',
                'password' => rand(100000, 999999),
                'settings' => [
                    'host_video' => true,
                    'participant_video' => true,
                    'join_before_host' => false,
                    'mute_upon_entry' => true,
                    'waiting_room' => true,
                ],
            ]);

        if ($response->failed()) {
            Log::error('Zoom API Error: ' . $response->body());
            throw new \Exception('Zoom API Error: ' . ($response->json()['message'] ?? 'Unknown error'));
        }

        $meeting = $response->json();

        return [
            'meeting_id' => $meeting['id'],
            'join_url' => $meeting['join_url'],
            'start_url' => $meeting['start_url'],
            'password' => $meeting['password'] ?? '',
        ];
    }

    public function updateMeeting($meetingId, $data)
    {
        // Skip for manual meetings
        if (!$meetingId || str_contains($meetingId, 'MANUAL')) {
            return true;
        }

        $accountId = env('ZOOM_ACCOUNT_ID');
        $clientId = env('ZOOM_CLIENT_ID');
        $clientSecret = env('ZOOM_CLIENT_SECRET');

        if (!$accountId || !$clientId || !$clientSecret) {
            return false;
        }

        try {
            $accessToken = $this->getAccessToken($accountId, $clientId, $clientSecret);

            $response = Http::withToken($accessToken)
                ->patch("{$this->baseUrl}/meetings/{$meetingId}", [
                    'topic' => $data['topic'] ?? 'Live Class',
                    'start_time' => date('Y-m-d\TH:i:s', strtotime($data['start_time'])),
                    'duration' => $data['duration'] ?? 40,
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Zoom Update Error: ' . $e->getMessage());
            return false;
        }
    }

    protected function getAccessToken($accountId, $clientId, $clientSecret)
    {
        $response = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => $accountId,
            ]);

        if ($response->failed()) {
            Log::error('Zoom Auth Error: ' . $response->body());
            throw new \Exception('Failed to authenticate with Zoom.');
        }

        return $response->json()['access_token'];
    }
}
