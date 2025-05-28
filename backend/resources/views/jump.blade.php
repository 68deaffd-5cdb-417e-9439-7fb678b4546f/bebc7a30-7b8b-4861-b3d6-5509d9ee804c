{{-- resources/views/proceed.blade.php --}}
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Proceed Confirmation</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f9f9f9;
        }

        .container {
            text-align: center;
        }

        .message {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        button {
            font-size: 2rem;
            padding: 1rem 3rem;
            cursor: pointer;
            border: none;
            background-color: #007BFF;
            color: white;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .token {
            width: 400px;
            text-wrap: auto;
            line-break: anywhere;
        }
    </style>
</head>
<body>
<div class="container">
    <form method="POST" action="http://127.0.0.1/api/session/create">
        @csrf
        <div class="message">
            Do you want to proceed as user <strong>{{ $nickname }}</strong>?
        </div>

        <pre class="token">{{ $token }}</pre>

        @foreach ($hiddenFields as $name => $value)
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endforeach

        <button type="submit">Authorize</button>
    </form>
</div>
</body>
</html><?php
