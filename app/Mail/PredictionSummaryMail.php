<?php

namespace App\Mail;

use App\Models\Prediction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PredictionSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Prediction $prediction) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🌱 Your Crop Yield Prediction — '.$this->prediction->crop->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.prediction_summary',
        );
    }
}
