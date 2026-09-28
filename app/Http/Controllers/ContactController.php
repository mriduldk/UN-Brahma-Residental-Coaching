<?php

namespace App\Http\Controllers;

use App\Mail\InquiryMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Handle the contact inquiry form submission.
     */
    public function sendInquiry(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        try {
            Mail::to('info.onlinesetu@gmail.com')->send(new InquiryMail($validated));
            
            return response()->json([
                'success' => true,
                'message' => 'Your inquiry has been sent successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send inquiry. Please try again later or contact us directly via phone.'
            ], 500);
        }
    }
}
