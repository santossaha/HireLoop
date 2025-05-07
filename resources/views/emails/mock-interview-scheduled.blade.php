<!DOCTYPE html>
<html>
<head>
    <title>Mock Interview Scheduled</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{$type}} Interview Scheduled</h2>
        </div>
        <div class="content">
            <p>Hello {{ $vendor->company_name }},</p>
            
            <p>This is to inform you that a mock interview has been scheduled for one of your candidates.</p>

            <h3>Interview Details:</h3>
            <ul>
                <li><strong>Candidate Name:</strong> {{ $interview->candidate->candidate_name }}</li>
                <li><strong>Requirement ID:</strong> {{ $interview->requirement->requirement_id }}</li>
                <li><strong>Interview Date:</strong> {{ $interview->scheduled_at->format('F d, Y') }}</li>
                <li><strong>Interview Time:</strong> {{ $interview->scheduled_at->format('h:i A') }}</li>
            </ul>

            <p>Please ensure your candidate is prepared for the interview.</p>
            
            <a href="{{ route('candidate-sourcing.show', $interview->requirement->id) }}" class="button">View Interview Details</a>
            
            {{-- <p>Best regards,<br>VMS Team</p> --}}
        </div>
    </div>
</body>
</html> 