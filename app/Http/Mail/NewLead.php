<?php

namespace App\Http\Mail;

use App\Models\Lead;
use App\Models\LeadAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class NewLead extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Lead $lead,
        public LeadAssignment $leadAssignment
    ) {}

    public const keyToHeaders = [
        // 'first_name' => 'First Name',
        // 'last_name' => 'Last Name',
        'name' => "Name",
        'insurance_type' => 'Insurance Quote Type',
        'birthdate' => 'Birthdate',
        'province_territory' => 'Province/Territory',
        'sex' => 'Sex',
        'desired_amount' => 'Desired Amount ($)',
        'length_coverage' => 'Length Coverage',
        'mortgage_amortization' => 'Mortgage Amortization',
        'length_payment' => 'Length Payment',
        'health_class' => 'Health Class',
        'tobacco_use' => 'Tobacco Use',
        'journey' => 'Journey',
        'mobile_number' => 'Mobile Number',
        'email' => 'Email',
        'created_at' => 'Requested on',
        'status' => 'Status',
    ];
    public const excludedFields = [
        'id',
        'updated_at'
    ];

    public static function formatters($key, $value)
    {
        if ($key === 'mortgage_amortization' || $key === 'length_coverage' || $key === 'length_payment') {
            return $value . ' years';
        } elseif ($key === 'created_at') {
            return Carbon::parse($value)->format('F j, Y g:i A');
        } elseif ($key === 'birthdate') {
            return Carbon::parse($value)->format('F j, Y');
        } else {
            return $value;
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Lead: ' . $this->lead->name(),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.lead-notification',
            with: [
                'keyToHeaders' => self::keyToHeaders,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
