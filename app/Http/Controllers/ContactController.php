<?php

namespace App\Http\Controllers;

use App\Mail\TicketCreatedMail;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    /**
     * Show the contact form.
     */
    public function show()
    {
        return view('contact');
    }

    /**
     * Store a new support ticket from contact form.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:5000',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'ticket_number' => $this->generateTicketNumber(),
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'open',
        ]);

        // Send confirmation email
        try {
            // Send support-related outgoing mail using the dedicated support mailer
            Mail::mailer('support')
                ->to($validated['email'])
                ->send((new TicketCreatedMail($ticket))
                    ->from(env('SUPPORT_MAIL_FROM_ADDRESS', 'support.cropyield@gmail.com'), env('SUPPORT_MAIL_FROM_NAME', env('APP_NAME')))
                );
        } catch (\Exception $e) {
            Log::error('Failed to send ticket confirmation email: ' . $e->getMessage());
        }

        return back()->with('success', 'Thank you! Your support ticket has been created. We\'ll get back to you soon. Ticket #: ' . $ticket->ticket_number);
    }

    /**
     * Generate unique ticket number.
     */
    private function generateTicketNumber(): string
    {
        do {
            $ticket_number = 'TKT-' . date('Y') . '-' . Str::upper(Str::random(8));
        } while (SupportTicket::where('ticket_number', $ticket_number)->exists());

        return $ticket_number;
    }
}
