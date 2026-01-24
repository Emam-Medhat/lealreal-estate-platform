<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'template_id' => 'nullable|exists:email_templates,id',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240'
        ]);

        try {
            $template = null;
            if ($request->template_id) {
                $template = EmailTemplate::find($request->template_id);
            }

            $emailContent = $template ? $this->processTemplate($template, $request->content) : $request->content;

            Mail::raw($emailContent, function ($message) use ($request) {
                $message->to($request->to)
                    ->subject($request->subject)
                    ->from(Auth::user()->email, Auth::user()->name);

                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $attachment) {
                        $message->attach($attachment->getRealPath(), [
                            'as' => $attachment->getClientOriginalName(),
                            'mime' => $attachment->getMimeType(),
                        ]);
                    }
                }
            });

            // Log email
            Log::info('Email sent', [
                'from' => Auth::user()->email,
                'to' => $request->to,
                'subject' => $request->subject,
                'template_id' => $request->template_id
            ]);

            return response()->json(['success' => true, 'message' => 'Email sent successfully']);

        } catch (\Exception $e) {
            Log::error('Email sending failed', [
                'error' => $e->getMessage(),
                'to' => $request->to,
                'subject' => $request->subject
            ]);

            return response()->json(['error' => 'Failed to send email'], 500);
        }
    }

    public function sendBulk(Request $request)
    {
        $request->validate([
            'recipients' => 'required|array',
            'recipients.*' => 'email',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'template_id' => 'nullable|exists:email_templates,id'
        ]);

        $sent = 0;
        $failed = 0;

        foreach ($request->recipients as $recipient) {
            try {
                $template = null;
                if ($request->template_id) {
                    $template = EmailTemplate::find($request->template_id);
                }

                $emailContent = $template ? $this->processTemplate($template, $request->content) : $request->content;

                Mail::raw($emailContent, function ($message) use ($recipient, $request) {
                    $message->to($recipient)
                        ->subject($request->subject)
                        ->from(Auth::user()->email, Auth::user()->name);
                });

                $sent++;

            } catch (\Exception $e) {
                Log::error('Bulk email failed', [
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

    public function getTemplates()
    {
        $templates = EmailTemplate::where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json(['templates' => $templates]);
    }

    public function createTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        $template = EmailTemplate::create([
            'name' => $request->name,
            'subject' => $request->subject,
            'content' => $request->content,
            'variables' => $request->variables ?? [],
            'is_active' => $request->is_active ?? true,
            'created_by' => Auth::id()
        ]);

        return response()->json(['success' => true, 'template' => $template]);
    }

    public function updateTemplate(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        $template->update($request->all());

        return response()->json(['success' => true, 'template' => $template]);
    }

    public function deleteTemplate($id)
    {
        $template = EmailTemplate::findOrFail($id);
        $template->delete();

        return response()->json(['success' => true]);
    }

    public function previewTemplate(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:email_templates,id',
            'data' => 'nullable|array'
        ]);

        $template = EmailTemplate::findOrFail($request->template_id);
        $preview = $this->processTemplate($template, $template->content, $request->data ?? []);

        return response()->json(['preview' => $preview]);
    }

    private function processTemplate($template, $content, $data = [])
    {
        // Replace template variables
        $variables = array_merge([
            'user_name' => Auth::user()->name,
            'user_email' => Auth::user()->email,
            'current_date' => now()->format('Y-m-d'),
            'current_time' => now()->format('H:i:s')
        ], $data);

        foreach ($variables as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }

        return $content;
    }

    public function sendNotificationEmail(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'notification_id' => 'required|exists:user_notifications,id'
        ]);

        $user = User::findOrFail($request->user_id);
        $notification = $user->notifications()->findOrFail($request->notification_id);

        try {
            Mail::raw($notification->message, function ($message) use ($user, $notification) {
                $message->to($user->email)
                    ->subject($notification->title)
                    ->from('noreply@realestate.com', 'Real Estate Platform');
            });

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Notification email failed', [
                'user_id' => $user->id,
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to send notification email'], 500);
        }
    }
}
