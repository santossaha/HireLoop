<!DOCTYPE html>
<html>
<head>
    <title>New Requirement Created - Review Required</title>
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
        .details {
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Requirement Created</h2>
            <p>A new requirement has been created in your department that requires your review and approval.</p>
        </div>
        
        <p>Dear {{ $department->hod->name }},</p>
        
        <p>We would like to inform you that a new requirement has been created in your department. Please review the details below:</p>
        
        <div class="details">
            <p><strong>Requirement ID:</strong> {{ $requirement->requirement_id }}</p>
            <p><strong>Company:</strong> {{ $company->name }}</p>
            <p><strong>Department:</strong> {{ $department->name }}</p>
            <p><strong>Position Title:</strong> {{ $requirement->title }}</p>
            <p><strong>Created By:</strong> {{ $requirement->createBy->name }}</p>
            <p><strong>Created At:</strong> {{ $requirement->created_at->format('M d, Y h:i A') }}</p>
        </div>
        
        <p>Please review this requirement at your earliest convenience. Your approval is required before proceeding further.</p>
        
        <p>You can access the requirement details through the system to review and take necessary action.</p>
        
        <div class="footer">
            <p>Best regards,<br>
            {{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html> 