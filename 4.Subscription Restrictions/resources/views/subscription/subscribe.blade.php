<!DOCTYPE html>
<html>
<head>
    <title>Subscribe</title>
</head>
<body>
    <h1>Subscribe to Our Service</h1>
    <form action="{{ route('subscribe.checkout') }}" method="GET">
        <label for="plan">Choose a plan:</label>
        <select id="plan" name="plan">
            <option value="monthly">Monthly - $10</option>
            <option value="yearly">Yearly - $100</option>
        </select>
        <button type="submit">Subscribe</button>
    </form>
</body>
</html>
