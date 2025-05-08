<!DOCTYPE html>
<html>
<head>
    <title>Interview Feedback</title>
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
        .rating {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            color: white;
            font-weight: bold;
        }
        .excellent { background-color: #28a745; }
        .good { background-color: #17a2b8; }
        .average { background-color: #ffc107; }
        .bad { background-color: #dc3545; }
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
            <h2>Interview Feedback Report</h2>
        </div>
        <div class="content">
            <p>Hello {{ $vendor->company_name }},</p>
            
            <p>We are writing to inform you about the feedback for the interview of your candidate.</p>

            <h3>Interview Details:</h3>
            <ul>
                <li><strong>Candidate Name:</strong> {{ $interview->candidate->candidate_name }}</li>
                <li><strong>Requirement ID:</strong> {{ $interview->requirement->requirement_id }}</li>
                <li><strong>Interview Type:</strong> {{ ucfirst($interview->type) }}</li>
                <li><strong>Interview Date:</strong> {{ $interview->scheduled_at->format('F d, Y') }}</li>
            </ul>

            <h3>Feedback Results:</h3>
            <ul>
                <li><strong>Result:</strong> 
                    <span class="rating {{ $interview->result === 'pass' ? 'excellent' : 'bad' }}">
                        {{ ucfirst($interview->result) }}
                    </span>
                </li>
                <li><strong>Communication Rating:</strong> 
                    <span class="rating {{ $interview->communication_rating }}">
                        {{ ucfirst($interview->communication_rating) }}
                    </span>
                </li>
                <li><strong>Technical Rating:</strong> 
                    <span class="rating {{ $interview->technical_rating }}">
                        {{ ucfirst($interview->technical_rating) }}
                    </span>
                </li>
                <li><strong>Client Interview Ready:</strong> 
                    {{ $interview->client_interview_ready ? 'Yes' : 'No' }}
                </li>
            </ul>

            <h3>Detailed Feedback:</h3>
            <p>{{ $interview->feedback }}</p>

            @if($interview->last_approved_budget)
            <p><strong>Last Approved Budget:</strong> {{ number_format($interview->last_approved_budget, 2) }}</p>
            @endif
            
            {{-- <a href="{{ route('interviews.show', $interview->id) }}" class="button">View Full Interview Details</a> --}}
            
            {{-- <p>Best regards,<br>VMS Team</p> --}}
        </div>
    </div>
</body>
</html> 