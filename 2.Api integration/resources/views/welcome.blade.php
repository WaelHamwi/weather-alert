<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/3.2.4/tailwind.min.css">
    <style>
        body {
            background-color: #f7f9fc;
            color: #333;
        }
        .hero {
            background: url('https://via.placeholder.com/1500x500') no-repeat center center;
            background-size: cover;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }
        .hero h1 {
            font-size: 3rem;
            font-weight: bold;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .card h2 {
            margin-top: 0;
            font-size: 1.5rem;
        }
        .card a {
            color: #3490dc;
            text-decoration: none;
        }
        .card a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header class="hero">
        <div>
            <h1>Welcome to the Weather App</h1>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <h2>Explore Weather Features</h2>
            <p>Get the current weather or hourly forecast for your location.</p>
            <a href="{{ route('weather.current') }}" class="text-lg font-semibold">Check Current Weather</a>
            <br>
            <a href="{{ route('weather.forecast') }}" class="text-lg font-semibold">Check Hourly Forecast</a>
        </div>
    </div>

    <footer class="container text-center py-6">
        <p>&copy; 2024 Weather App. All rights reserved.</p>
    </footer>
</body>
</html>
