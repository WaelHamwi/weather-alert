<!DOCTYPE html>
<html>

<head>
    <title>Weather Update</title>
</head>

<body>
    <p>Dear {{ $userName }},</p>
    <p>Here is the latest weather update for your location:</p>
    <pre>{{ $weatherData }}</pre>
    <p>Thank you for using our service!</p>
</body>

</html>