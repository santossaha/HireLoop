<?php

namespace App\Constants;

class NotificationMessages
{
    // Requirement Related Messages
    const NEW_REQUIREMENT_TITLE = 'New Requirement: :company - :department (#:requirement_id)';
    const NEW_REQUIREMENT_MESSAGE_VENDOR = 'New requirement has been added by the sources.';
    const NEW_REQUIREMENT_MESSAGE_ADMIN = ':company_name applied for the requirement ID :requirement_id';

    // Mock Interview Related Messages
    const MOCK_INTERVIEW_SCHEDULED = [
        'title' => 'Mock Interview Scheduled - :requirement_id',
        'message' => 'Mock Interview: Candidate :candidate_name for requirement :requirement_id has been selected for Mock round on :interview_datetime, please be available.'
    ];

    const MOCK_INTERVIEW_PASSED = [
        'title' => 'Mock Interview Passed - :requirement_id',
        'message' => 'Mock Interview: Candidate :candidate_name for requirement :requirement_id has PASSED the Mock round. Further client information will be provided in short time from the officials.'
    ];

    const MOCK_INTERVIEW_REJECTED = [
        'title' => 'Mock Interview Rejected - :requirement_id',
        'message' => 'Mock Interview - Better Luck Next Time! Candidate :candidate_name for requirement :requirement_id has been REJECTED in the Mock round. Kindly check the feedback for the same in to the system.'
    ];

    // Client Interview Related Messages
    const CLIENT_INTERVIEW_SCHEDULED = [
        'title' => 'Client Interview Scheduled - :requirement_id',
        'message' => 'Client Interview: Candidate :candidate_name for requirement :requirement_id has been scheduled for Client interview round on :interview_datetime, please be available.'
    ];

    const CLIENT_INTERVIEW_PASSED = [
        'title' => 'Client Interview Passed - :requirement_id',
        'message' => 'Client Interview: Candidate :candidate_name for requirement :requirement_id has PASSED the Client round. Further information will be provided in short time from the officials.'
    ];

    const CLIENT_INTERVIEW_REJECTED = [
        'title' => 'Client Interview Rejected - :requirement_id',
        'message' => 'Client Interview - Better Luck Next Time! Candidate :candidate_name for requirement :requirement_id has been REJECTED in the Client round. Kindly check the feedback for the same in to the system.'
    ];

    // Resume Related Messages
    const RESUME_UPLOAD_TITLE = 'Resume Uploaded: :company - :department (#:requirement_id)';
    const RESUME_UPLOAD_MESSAGE = 'A new resume has been uploaded for the requirement.';

    /**
     * Replace placeholders in a message with actual values
     *
     * @param string $message The message template
     * @param array $replacements Key-value pairs of replacements
     * @return string
     */
    public static function format($message, array $replacements)
    {
        return strtr($message, $replacements);
    }
} 