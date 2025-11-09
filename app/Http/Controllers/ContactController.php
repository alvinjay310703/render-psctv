<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        // Validate the form data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'service' => 'required|string',
            'message' => 'required|string',
            'g-recaptcha-response' => 'required'
        ]);

        // Verify reCAPTCHA
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret'),
            'response' => $request->input('g-recaptcha-response')
        ]);

        $result = $response->json();

        if (!$result['success']) {
            return back()->withErrors(['captcha' => 'reCAPTCHA verification failed.']);
        }

        // Process your form data (send email, save to database, etc.)
        // Your form processing logic here

        return back()->with('success', 'Thank you! Your message has been sent successfully.');
    }
}