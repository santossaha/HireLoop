<!DOCTYPE html>
<html>
<head>
    <title>New Resume Uploaded</title>
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
            <h2>New Resume Uploaded for Review</h2>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>A new resume has been uploaded for the following requirement:</p>
            
            <h3>Requirement Details:</h3>
            <ul>
                <li><strong>Job Description:</strong> {{ $requirement->job_description }}</li>
               
                <li><strong>Department:</strong> {{ $requirement->department->name }}</li>
            </ul>

            <h3>Candidate Details:</h3>
            <ul>
                <li><strong>Name:</strong> {{ $candidate->candidate_name }}</li>
                <li><strong>Email:</strong> {{ $candidate->email }}</li>
                <li><strong>Phone:</strong> {{ $candidate->phone }}</li>
                <li><strong>Candidate Budget:</strong> ${{ number_format($candidate->budget, 2) }}</li>
            </ul>

            <p><strong>Uploaded by:</strong> {{ $uploadedBy->name }}</p>
            
            <p>Please review the candidate's resume and take appropriate action.</p>
            
            <a href="{{ route('requirements.show', $requirement->id) }}" class="button">View Candidate Details</a>
        </div>
    </div>
</body>
</html> 