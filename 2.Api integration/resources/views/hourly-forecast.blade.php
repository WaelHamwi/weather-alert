<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hourly Forecast</title>
</head>
<body>
    <h1>Hourly Forecast</h1>

    @if(isset($error))
        <p style="color: red;">{{ $error }}</p>
    @elseif(isset($forecast))
        <h2>Hourly Forecast for {{ $location }}</h2>
        @foreach($forecast['list'] as $hour)
            <p>
                Time: {{ $hour['dt_txt'] }}<br>
                Temperature: {{ $hour['main']['temp'] }}°C<br>
                Condition: {{ $hour['weather'][0]['description'] }}
            </p>
        @endforeach
    @endif
</body>
</html>
