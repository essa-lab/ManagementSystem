<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 30px;
            text-align: center;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://d316mbs0kouq0m.cloudfront.net/images/download.png" alt="{{ __('messages.company_logo') }}" class="logo">

        {{-- <p>Dear <strong>{{ department_head_name }}</strong>,</p> --}}
        <p>The following report contains a list of patrons who have overdue resources for this academic season.</p>
        <p>You can review the full report by clicking the button below:</p>
        
        
        <div class="footer">
            <p>{{ __('messages.all_rights_reserved', ['company' => config('app.name'), 'date' => date('Y')]) }}</p>
            {{-- <p>{{__('messages.if_you_are_having_trouble')}}</p> --}}
        </div>
    </div>
</body>
</html>