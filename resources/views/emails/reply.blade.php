<!DOCTYPE html>
<html>
<head>
    <title>Reply from Admin</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            padding: 30px;
            margin: 0;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .header {
            background: #007BFF;
            padding: 15px;
            border-radius: 8px 8px 0 0;
            color: #ffffff;
            font-size: 22px;
            font-weight: bold;
        }
        .content {
            padding: 20px;
            text-align: left;
        }
        h2 {
            color: #333;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
        }
        .reply-box {
            background: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #007BFF;
            margin: 20px 0;
            font-style: italic;
        }
        .cta-button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            background: #007BFF;
            color: #ffffff;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s;
        }
        .cta-button:hover {
            background: #0056b3;
        }
        .footer {
            margin-top: 25px;
            font-size: 14px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">📩 Reply from {{ env('MAIL_FROM_NAME') }}</div>
        
        <div class="content">
            <h2>Hello, {{ $name }} 👋</h2>
            <p>Thank you for reaching out to us. Below is our response to your inquiry:</p>

            <div class="reply-box">
                <p><strong>Reply:</strong> {{ $replyMessage }}</p>
            </div>

            <p>If you need further assistance, feel free to contact us again.</p>

            <a href="mailto:{{ env('MAIL_FROM_ADDRESS') }}" class="cta-button">Reply to Us</a>

            <p class="footer">Best regards, <br> {{ env('MAIL_FROM_NAME') }}</p>
        </div>
    </div>
</body>
</html>
