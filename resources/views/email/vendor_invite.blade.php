<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vendor Registration Invitation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f9fc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border: 1px solid #e1e1e1;
            padding: 30px;
            border-radius: 8px;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
        }
        .header h2 {
            color: #333333;
        }
        .content {
            font-size: 16px;
            color: #555555;
            line-height: 1.6;
        }
        .button {
            text-align: center;
            margin-top: 30px;
        }
        .btn {
            background-color: #007BFF;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .footer {
            font-size: 13px;
            text-align: center;
            color: #999999;
            margin-top: 30px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>You're Invited to Register as a Vendor</h2>
    </div>
    <div class="content">
        <p>Dear,</p>

        <p>Please click the button below to begin the registration process:</p>

        <div class="button">
            <a href="{{url('vendor-invite/'.$data)}}" class="btn">Register Now</a>
        </div>

        <p>Best regards</p>
    </div>

</div>
</body>
</html>