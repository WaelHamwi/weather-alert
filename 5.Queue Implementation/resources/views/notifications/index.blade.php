<h1>Notifications</h1>
@foreach ($notifications as $notification)
<div class="notification">
    <p>{{ $notification->data['message'] }}</p>
    <a href="{{ route('notifications.read', ['id' => $notification->id]) }}" class="btn btn-primary">Mark as Read</a>
</div>
@endforeach