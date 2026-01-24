<?php

namespace App\Http\Controllers;

use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'to' => 'required|string|max:20',
            'message' => 'required|string|max:160',
            'type' => 'required|in:notification,promotional,transactional'
        ]);

        try {
            // Log SMS attempt
            $smsLog = SmsLog::create([
                'user_id' => Auth::id(),
                'to' => $request->to,
                'message' => $request->message,
                'type' => $request->type,
                'status' => 'pending',
                'sent_at' => now()
            ]);

            // Send SMS (integrate with SMS service like Twilio, Vonage, etc.)
            $response = $this->sendSmsViaProvider($request->to, $request->message);

            if ($response['success']) {
                $smsLog->update([
                    'status' => 'sent',
                    'provider_response' => json_encode($response['data']),
                    'delivered_at' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'sms_id' => $smsLog->id
                ]);
            } else {
                $smsLog->update([
                    'status' => 'failed',
                    'error_message' => $response['error']
                ]);

                return response()->json(['error' => 'Failed to send SMS'], 500);
            }

        } catch (\Exception $e) {
            Log::error('SMS sending failed', [
                'to' => $request->to,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to send SMS'], 500);
        }
    }

    public function sendBulk(Request $request)
    {
        $request->validate([
            'recipients' => 'required|array',
            'recipients.*' => 'string|max:20',
            'message' => 'required|string|max:160',
            'type' => 'required|in:notification,promotional,transactional'
        ]);

        $sent = 0;
        $failed = 0;

        foreach ($request->recipients as $recipient) {
            try {
                $smsLog = SmsLog::create([
                    'user_id' => Auth::id(),
                    'to' => $recipient,
                    'message' => $request->message,
                    'type' => $request->type,
                    'status' => 'pending',
                    'sent_at' => now()
                ]);

                $response = $this->sendSmsViaProvider($recipient, $request->message);

                if ($response['success']) {
                    $smsLog->update([
                        'status' => 'sent',
                        'provider_response' => json_encode($response['data']),
                        'delivered_at' => now()
                    ]);
                    $sent++;
                } else {
                    $smsLog->update([
                        'status' => 'failed',
                        'error_message' => $response['error']
                    ]);
                    $failed++;
                }

            } catch (\Exception $e) {
                Log::error('Bulk SMS failed', [
                    'recipient' => $recipient,
                    'error' => $e->getMessage()
                ]);
                $failed++;
            }
        }

        return response()->json([
            'success' => true,
            'sent' => $sent,
            'failed' => $failed,
            'total' => count($request->recipients)
        ]);
    }

    public function sendVerification(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20'
        ]);

        $user = Auth::user();
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store verification code
        $user->update([
            'phone_verification_code' => $code,
            'phone_verification_expires' => now()->addMinutes(10)
        ]);

        $message = "Your verification code is: {$code}. Valid for 10 minutes.";

        try {
            $response = $this->sendSmsViaProvider($request->phone, $message);

            if ($response['success']) {
                SmsLog::create([
                    'user_id' => $user->id,
                    'to' => $request->phone,
                    'message' => $message,
                    'type' => 'verification',
                    'status' => 'sent',
                    'sent_at' => now(),
                    'delivered_at' => now()
                ]);

                return response()->json(['success' => true, 'message' => 'Verification code sent']);
            } else {
                return response()->json(['error' => 'Failed to send verification code'], 500);
            }

        } catch (\Exception $e) {
            Log::error('SMS verification failed', [
                'user_id' => $user->id,
                'phone' => $request->phone,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to send verification code'], 500);
        }
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();

        if (!$user->phone_verification_code || !$user->phone_verification_expires) {
            return response()->json(['error' => 'No verification code sent'], 400);
        }

        if (now()->gt($user->phone_verification_expires)) {
            return response()->json(['error' => 'Verification code expired'], 400);
        }

        if ($user->phone_verification_code !== $request->code) {
            return response()->json(['error' => 'Invalid verification code'], 400);
        }

        // Clear verification code
        $user->update([
            'phone_verification_code' => null,
            'phone_verification_expires' => null,
            'phone_verified_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Phone verified successfully']);
    }

    public function getSmsHistory()
    {
        $smsLogs = SmsLog::where('user_id', Auth::id())
            ->orderBy('sent_at', 'desc')
            ->paginate(20);

        return response()->json(['sms_logs' => $smsLogs]);
    }

    public function getSmsStats()
    {
        $user = Auth::id();

        $stats = [
            'total' => SmsLog::where('user_id', $user)->count(),
            'sent' => SmsLog::where('user_id', $user)->where('status', 'sent')->count(),
            'failed' => SmsLog::where('user_id', $user)->where('status', 'failed')->count(),
            'today' => SmsLog::where('user_id', $user)->whereDate('sent_at', today())->count(),
            'this_month' => SmsLog::where('user_id', $user)->whereMonth('sent_at', now()->month)->count()
        ];

        return response()->json(['stats' => $stats]);
    }

    private function sendSmsViaProvider($to, $message)
    {
        // Integration with SMS provider (Twilio, Vonage, etc.)
        // This is a placeholder implementation
        
        try {
            // Example with Twilio:
            // $twilio = new \Twilio\Rest\Client(config('services.twilio.sid'), config('services.twilio.token'));
            // $message = $twilio->messages->create($to, [
            //     'from' => config('services.twilio.from'),
            //     'body' => $message
            // ]);
            
            // For demo purposes, return success
            return [
                'success' => true,
                'data' => ['message_id' => 'msg_' . uniqid(), 'status' => 'sent']
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function webhook(Request $request)
    {
        // Handle SMS provider webhook (delivery reports, etc.)
        Log::info('SMS webhook received', $request->all());

        // Update SMS log based on webhook data
        if ($request->has('MessageSid')) {
            $smsLog = SmsLog::where('provider_message_id', $request->MessageSid)->first();
            if ($smsLog) {
                $status = $request->MessageStatus ?? 'unknown';
                $smsLog->update([
                    'status' => $status,
                    'delivered_at' => $status === 'delivered' ? now() : null,
                    'provider_response' => json_encode($request->all())
                ]);
            }
        }

        return response()->json(['status' => 'received']);
    }
}
