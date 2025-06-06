<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $ip = $request->ip();
        $key = 'contact-form:' . $ip;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            Log::warning('Contact form rate limit exceeded', [
                'ip' => $ip,
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|min:1|max:50',
            'lastname' => 'required|string|min:1|max:50',
            'email' => 'required|email:rfc,dns|max:255',
            'phone' => 'required|string|regex:/^[\+]?[0-9\s\-\(\)]{7,20}$/',
            'message' => 'nullable|string|max:1000',
        ], [
            'firstname.required' => 'First name is required',
            'firstname.min' => 'First name must be at least 1 character',
            'firstname.max' => 'First name must not exceed 50 characters',
            'lastname.required' => 'Last name is required',
            'lastname.min' => 'Last name must be at least 1 character',
            'lastname.max' => 'Last name must not exceed 50 characters',
            'email.required' => 'Email is required',
            'email.email' => 'Please provide a valid email address',
            'phone.required' => 'Phone number is required',
            'phone.regex' => 'Please provide a valid phone number',
            'message.max' => 'Message must not exceed 1000 characters',
        ]);

        if ($validator->fails()) {
            RateLimiter::hit($key, 60);

            Log::info('Contact form validation failed', [
                'ip' => $ip,
                'errors' => $validator->errors()->toArray(),
                'data' => $request->only(['firstname', 'lastname', 'email', 'phone']),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            $contact = Contact::create([
                'firstname' => strip_tags($request->firstname),
                'lastname' => strip_tags($request->lastname),
                'email' => filter_var($request->email, FILTER_SANITIZE_EMAIL),
                'phone' => preg_replace('/[^0-9\+\-\(\)\s]/', '', $request->phone),
                'message' => $request->message ? strip_tags($request->message) : null,
                'ip_address' => $ip,
                'user_agent' => $request->userAgent(),
                'status' => Contact::STATUS_NEW,
            ]);

            $this->sendNotificationEmail($contact);

            RateLimiter::hit($key, 60);

            Log::info('Contact form submitted successfully', [
                'contact_id' => $contact->id,
                'ip' => $ip,
                'email' => $contact->email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contact form submitted successfully',
            ], 201);

        } catch (\Exception $e) {
            Log::error('Contact form submission failed', [
                'ip' => $ip,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
            ], 500);
        }
    }

    private function sendNotificationEmail(Contact $contact): void
    {
        try {
            $adminEmail = config('mail.admin_email', env('ADMIN_EMAIL'));
            
            if (!$adminEmail) {
                Log::warning('Admin email not configured for contact notifications');
                return;
            }

            Mail::raw(
                "New Contact Form Submission\n\n" .
                "Name: {$contact->firstname} {$contact->lastname}\n" .
                "Email: {$contact->email}\n" .
                "Phone: {$contact->phone}\n" .
                "Message: " . ($contact->message ?: 'No message provided') . "\n\n" .
                "Submitted at: {$contact->created_at}\n" .
                "IP Address: {$contact->ip_address}",
                function ($message) use ($contact, $adminEmail) {
                    $message->to($adminEmail)
                           ->subject('New Contact Form Submission - ' . $contact->firstname . ' ' . $contact->lastname)
                           ->from(config('mail.from.address'), config('mail.from.name'));
                }
            );

            Log::info('Contact notification email sent', [
                'contact_id' => $contact->id,
                'admin_email' => $adminEmail,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send contact notification email', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
