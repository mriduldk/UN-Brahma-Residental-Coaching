<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Msg91Service
{
    protected $authKey;
    protected $templateId;

    public function __construct()
    {
        $this->authKey = config('services.msg91.auth_key');
        $this->templateId = config('services.msg91.template_id');
    }

    /**
     * Send OTP to a mobile number.
     *
     * @param string $mobile
     * @return bool|array
     */
    public function sendOtp($mobile)
    {
        try {
            $response = Http::withHeaders([
                'authkey' => $this->authKey,
                'Content-Type' => 'application/json',
            ])->post('https://control.msg91.com/api/v5/otp', [
                        'template_id' => $this->templateId,
                        'mobile' => $mobile,
                    ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('MSG91 Send OTP Error: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('MSG91 Send OTP Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify OTP for a mobile number.
     *
     * @param string $mobile
     * @param string $otp
     * @return bool
     */
    public function verifyOtp($mobile, $otp)
    {
        try {
            $response = Http::withHeaders([
                'authkey' => $this->authKey,
            ])->get('https://control.msg91.com/api/v5/otp/verify', [
                        'mobile' => $mobile,
                        'otp' => $otp,
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                return isset($data['type']) && $data['type'] === 'success';
            }

            Log::error('MSG91 Verify OTP Error: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('MSG91 Verify OTP Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Retry OTP (Voice fallback or Resend).
     *
     * @param string $mobile
     * @param string $retryType (text|voice)
     * @return bool
     */
    /**
     * Verify Access Token from MSG91 OTP Widget.
     *
     * @param string $token
     * @return bool|array
     */
    public function verifyAccessToken($token)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://control.msg91.com/api/v5/widget/verifyAccessToken', [
                        'authkey' => $this->authKey,
                        'access-token' => $token,
                    ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('MSG91 Verify Access Token Error: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('MSG91 Verify Access Token Exception: ' . $e->getMessage());
            return false;
        }
    }
    /**
     * Send SMS/WhatsApp using MSG91 Flow API.
     *
     * @param string $mobile
     * @param string $templateId
     * @param array $params (Placeholder values)
     * @return bool|array
     */
    public function sendFlow($mobile, $templateId, $params = [])
    {
        try {
            $response = Http::withHeaders([
                'authkey' => $this->authKey,
                'Content-Type' => 'application/json',
            ])->post('https://control.msg91.com/api/v5/flow', [
                        'template_id' => $templateId,
                        'short_url' => '1',
                        'recipients' => [
                            [
                                'mobiles' => $mobile,
                                ...$params
                            ]
                        ]
                    ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('MSG91 Flow API Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generic send message (for simple text).
     */
    public function sendMessage($mobile, $message)
    {
        Log::info("Simulating MSG91 SMS to $mobile: $message");
        return true;
    }
}
