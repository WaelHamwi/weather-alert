<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Weather</title>
</head>

<body>
    <h1>Current Weather</h1>

    @if(isset($error))
    <p style="color: red;">{{ $error }}</p>
    @elseif(isset($weather))
    <h2>Weather for {{ $location->name }}</h2>
    <p>Temperature: {{ $weather['main']['temp'] }}°C</p>
    <p>Condition: {{ $weather['weather'][0]['description'] }}</p>
    @endif
</body>

</html>