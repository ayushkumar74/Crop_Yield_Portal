<?php

namespace App\Http\Controllers;

use App\Mail\TicketCreatedMail;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

        // Create ticket first to obtain an auto-increment id, then assign a deterministic ticket number based on that id
        // Use a temporary, unique placeholder for ticket_number so DB NOT NULL constraint is satisfied.
        $temporaryTicketNumber = 'TEMP-'.time().'-'.Str::upper(Str::random(6));

        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'ticket_number' => $temporaryTicketNumber,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'open',
        ]);

        // Generate production-style deterministic ticket number using the DB id
        // New format: CYP-SUP-YYYY-000001 (sequential, deterministic, production-style)
        $ticket_number = sprintf('CYP-SUP-%s-%06d', date('Y'), $ticket->id);
        $ticket->ticket_number = $ticket_number;
        $ticket->save();

        // Ticket created; avoid verbose logging in production

        // Increment admin-facing new-ticket counter (in-cache) for quick admin alerting
        try {
            Cache::forever('admin_new_tickets', Cache::get('admin_new_tickets', 0) + 1);
        } catch (\Exception $e) {
            Log::warning('Could not update admin_new_tickets cache: '.$e->getMessage());
        }

        // Send confirmation email
        try {
            // Send support-related outgoing mail using the dedicated support mailer
            Mail::mailer('support')
                ->to($validated['email'])
                ->send((new TicketCreatedMail($ticket))
                    ->from(env('SUPPORT_MAIL_FROM_ADDRESS', 'support.cropyield@gmail.com'), env('SUPPORT_MAIL_FROM_NAME', env('APP_NAME')))
                );
        } catch (\Exception $e) {
            Log::error('Failed to send ticket confirmation email: '.$e->getMessage());
        }

        $flash = "✅ Ticket submitted successfully\n".
             "Ticket ID: {$ticket->ticket_number}\n".
             "Our support team will review your request shortly.\n".
             'A confirmation email has been sent.';

        return back()->with('success', $flash);
    }

    /**
     * Generate unique ticket number.
     */
    private function generateTicketNumber(): string
    {
        do {
            $ticket_number = 'TKT-'.date('Y').'-'.Str::upper(Str::random(8));
        } while (SupportTicket::where('ticket_number', $ticket_number)->exists());

        return $ticket_number;
    }
}
