<?php

namespace App\Notifications;

use App\Constants\NotificationMessages;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class InterviewNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $interview;
    protected $type;
    protected $candidate;
    protected $requirement;

    public function __construct($interview, $type, $candidate, $requirement)
    {
        $this->interview = $interview;
        $this->type = $type;
        $this->candidate = $candidate;
        $this->requirement = $requirement;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    protected function getNotificationData()
    {
        $replacements = [
            ':candidate_name' => $this->candidate->name,
            ':requirement_id' => $this->requirement->requirement_id,
            ':interview_datetime' => $this->interview->datetime->format('d M Y h:i A')
        ];

        switch ($this->type) {
            case 'mock_scheduled':
                $template = NotificationMessages::MOCK_INTERVIEW_SCHEDULED;
                break;
            case 'mock_passed':
                $template = NotificationMessages::MOCK_INTERVIEW_PASSED;
                break;
            case 'mock_rejected':
                $template = NotificationMessages::MOCK_INTERVIEW_REJECTED;
                break;
            case 'client_scheduled':
                $template = NotificationMessages::CLIENT_INTERVIEW_SCHEDULED;
                break;
            case 'client_passed':
                $template = NotificationMessages::CLIENT_INTERVIEW_PASSED;
                break;
            case 'client_rejected':
                $template = NotificationMessages::CLIENT_INTERVIEW_REJECTED;
                break;
            default:
                throw new \InvalidArgumentException("Invalid interview notification type: {$this->type}");
        }

        return [
            'title' => NotificationMessages::format($template['title'], $replacements),
            'message' => NotificationMessages::format($template['message'], $replacements)
        ];
    }

    public function toArray($notifiable)
    {
        $data = $this->getNotificationData();
        return [
            'interview_id' => $this->interview->id,
            'requirement_id' => $this->requirement->id,
            'title' => $data['title'],
            'message' => $data['message'],
            'type' => $this->type,
            'created_at' => now(),
            'redirect_url' => route('interviews.show', $this->interview->id)
        ];
    }

    public function toBroadcast($notifiable)
    {
        $data = $this->getNotificationData();
        return new BroadcastMessage([
            'interview_id' => $this->interview->id,
            'requirement_id' => $this->requirement->id,
            'title' => $data['title'],
            'message' => $data['message'],
            'type' => $this->type,
            'created_at' => now(),
            'redirect_url' => route('interviews.show', $this->interview->id)
        ]);
    }
} 