<!DOCTYPE html>
<html>

<head>
    <title>Your Account Credentials</title>
</head>

<body>
    <h2>Welcome to Our System!</h2>
    <p>Your account has been successfully created.</p>

    <p><strong>Email:</strong> {{ $email }}</p>
    <p><strong>Password:</strong> {{ $password }}</p>

    <p>
        You can now log in using these credentials:<br>
        <a href="{{ $loginUrl }}"
            style="display:inline-block;padding:10px 20px;background:#4CAF50;color:white;text-decoration:none;border-radius:5px;">
            Login Now
        </a>
    </p>

    <p>If the button doesn't work, copy and paste this URL into your browser:</p>
    <p>{{ $loginUrl }}</p>
</body>

</html>
