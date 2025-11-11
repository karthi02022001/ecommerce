<?php
// app/Http/Controllers/ContactController.php

namespace App\Http\Controllers;

use App\Models\ContactSubmission;
use App\Mail\ContactFormSubmission;
use App\Mail\ContactFormAutoReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Show contact page
     */
    public function index()
    {
        return view('contact.index');
    }

    /**
     * Store contact submission
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:2000',
        ]);

        // Create contact submission
        $submission = ContactSubmission::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'new',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Prepare contact data for email
        $contactData = [
            'name' => $submission->name,
            'email' => $submission->email,
            'phone' => $submission->phone,
            'subject' => $submission->subject,
            'message' => $submission->message,
        ];

        // Send emails
        try {
            // Send notification to admin
            $adminEmail = config('mail.admin_email', env('MAIL_ADMIN_EMAIL'));
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new ContactFormSubmission($contactData));
            }

            // Send auto-reply to customer
            $locale = app()->getLocale();
            Mail::to($submission->email)->queue(new ContactFormAutoReply($submission, $locale));

            Log::info('Contact form submitted', [
                'submission_id' => $submission->id,
                'email' => $submission->email,
                'subject' => $submission->subject,
            ]);
        } catch (\Exception $e) {
            Log::error('Contact form email failed', [
                'submission_id' => $submission->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->back()->with('success', __('Thank you for contacting us! We will get back to you soon.'));
    }
}
