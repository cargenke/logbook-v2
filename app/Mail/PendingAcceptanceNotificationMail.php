<?php

namespace App\Mail;

use App\Enums\LogBookStatusEnum;
use App\Exports\PendingAcceptanceNotificationExport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as ExcelExport;

class PendingAcceptanceNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $date;

    /**
     * Create a new message instance.
     */
    public function __construct(protected LogBookStatusEnum $status)
    {
        $this->status = $status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'All Logbooks With Status - '.$this->status->label(),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.logbook-status-notification',
            with: [
                'status' => $this->status->label(),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => ExcelExport::raw(new PendingAcceptanceNotificationExport($this->status), Excel::XLSX), 'Pending Acceptance Notification-'.$this->status->value.'.xlsx'),
        ];
    }
}
