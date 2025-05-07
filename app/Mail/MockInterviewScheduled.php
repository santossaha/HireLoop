<?php

namespace App\Mail;

use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MockInterviewScheduled extends Mailable
{
    use Queueable, SerializesModels;

    public $interview;
    public $vendor;
    public $type;

    /**
     * Create a new message instance.
     */
    public function __construct(Interview $interview)
    {
        $this->interview = $interview;
        $this->vendor = $interview->vendor;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $type = ucfirst($this->interview->type);
        $this->type = $type;
        return $this->subject($type . ' Interview Scheduled - ' . $this->interview->candidate->candidate_name)
                    ->view('emails.mock-interview-scheduled');
    }
} 