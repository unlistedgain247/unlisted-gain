<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#f5f6fa; padding:24px; margin:0;">
    <div style="max-width:480px; margin:0 auto; background:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #eaeaea;">
        @include('emails.partials.header')
        <div style="padding:32px;">
            <p style="color:#7c8698; font-size:12px; letter-spacing:1px; text-transform:uppercase; margin:0 0 4px;">UnlistedGain</p>
            <h2 style="margin:0 0 16px; color:#222;">{{ $purpose === 'login' ? 'Sign in to your account' : 'Verify your email' }}</h2>
            <p style="color:#444; font-size:15px; margin:0 0 8px;">
                Use the code below to {{ $purpose === 'login' ? 'sign in to' : 'complete your sign up on' }} UnlistedGain. This code expires in 10 minutes.
            </p>
            <div style="font-size:32px; font-weight:bold; letter-spacing:8px; text-align:center; background:#f0fbf3; color:#1e8e3e; padding:16px; border-radius:8px; margin:24px 0;">
                {{ $code }}
            </div>
            <p style="color:#888; font-size:13px; margin:0;">If you didn't request this, you can safely ignore this email.</p>
        </div>
        @include('emails.partials.footer')
    </div>
</body>
</html>
