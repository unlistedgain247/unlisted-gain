<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UnlistedGain – Temporarily Unavailable</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f0f4f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            padding: 48px 40px;
            max-width: 480px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        @keyframes pulse-ring {
            0%   { box-shadow: 0 0 0 0   rgba(251,191,36,.45); }
            70%  { box-shadow: 0 0 0 14px rgba(251,191,36,0);  }
            100% { box-shadow: 0 0 0 0   rgba(251,191,36,0);   }
        }
        .icon {
            width: 64px;
            height: 64px;
            background: #fff4e5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 28px;
            animation: pulse-ring 2s ease-out infinite;
        }
        .icon span {
            display: inline-block;
            transform: rotate(-70deg);
        }
        h1 { font-size: 22px; color: #1a1a2e; margin-bottom: 12px; font-weight: 700; }
        p  { font-size: 15px; color: #6b7280; line-height: 1.6; margin-bottom: 28px; }
        a.btn {
            display: inline-block;
            background: #87b942;
            color: #fff;
            padding: 12px 32px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: background .2s;
        }
        a.btn:hover { background: #6e9735; }
        .brand { margin-bottom: 24px; }
        .brand img { height: 40px; }
        .code { font-size: 12px; color: #9ca3af; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="brand">
            <img src="/assets/img/unlisted-head.jpeg" alt="UnlistedGain" onerror="this.style.display='none'">
        </div>
        <div class="icon"><span>🔧</span></div>
        <h1>We'll Be Right Back</h1>
        <p>
            We're performing maintenance on our servers.<br>
            Please try again in a few minutes.
        </p>
        <a href="/" class="btn">Try Again</a>
        <p class="code">Error 503 – Service Temporarily Unavailable</p>
    </div>
</body>
</html>
