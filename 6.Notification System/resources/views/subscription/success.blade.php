<!DOCTYPE html>
<html>
<head>
    <title>Subscription Success</title>
</head>
<body>
    <h1>Subscription Successful!</h1>
    <p>Thank you for subscribing to our {{ $plan }} plan.</p>
    <a href="{{ route('customer.portal') }}">Manage Subscription</a>
</body>
</html>
