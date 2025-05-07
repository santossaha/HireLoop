<?php

namespace App\Mail;

use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterviewFeedback extends Mailable
{
    use Queueable, SerializesModels;

    public $interview;
    public $vendor;

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
        $result = ucfirst($this->interview->result);
        return $this->subject($type . ' Interview Feedback - ' . $result . ' - ' . $this->interview->candidate->candidate_name)
                    ->view('emails.interview-feedback');
    }
} 