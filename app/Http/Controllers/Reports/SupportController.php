<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SupportContactMail;

class SupportController extends Controller
{
    public function contact()
    {
        return view('reports.support.contact', [
            'title' => 'تواصل معنا',
            'backRoute' => route('reports.index'),
            'backLabel' => 'العودة للتقارير'
        ]);
    }

    public function sendContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
            'report_type' => 'nullable|string|max:100',
        ]);

        try {
            // Send email to support
            Mail::to('support@example.com')->send(new SupportContactMail($request->all()));
            
            return redirect()->route('reports.support.contact')
                ->with('success', 'تم إرسال رسالتك بنجاح! سنتواصل معك قريبًا.');
                
        } catch (\Exception $e) {
            return redirect()->route('reports.support.contact')
                ->with('error', 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.')
                ->withInput();
        }
    }

    public function help()
    {
        return view('reports.support.help', [
            'title' => 'مركز المساعدة',
            'backRoute' => route('reports.index'),
            'backLabel' => 'العودة للتقارير'
        ]);
    }
}
