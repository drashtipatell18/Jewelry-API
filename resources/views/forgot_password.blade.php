<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h1>Reset Password</h1>
    <p>Hello {{ $user->name }},</p>
    <p>You requested to reset your password. Click the link below to reset your password:</p>
    <a href="{{ url('/api/password/reset/' . $token) }}">Reset Password</a>
    <p>If you did not request a password reset, no further action is required.</p>
    <p>Thanks,</p>
    <p>Your Company Name</p>
</body>
</html>
