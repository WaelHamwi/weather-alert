{{-- a blade template to show users that they still in the trial period @extends('layouts.app')
@endsection --}}

@section('content')
<div class="container">
    <div class="card mt-5">
        <div class="card-header">
            Free Trial Period
        </div>
        <div class="card-body">
            <h5 class="card-title">You are still enjoying your free trial!</h5>
            <p class="card-text">You have <strong>{{ $trialDays }}</strong> days left of your free trial.</p>
            <p>After the trial period, you can subscribe to one of our plans.</p>
            <div>
                <h2>proceed to subscribe please choose your plan</h2>
                <form action="{{ route('subscribe.checkout') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan" value="yearly">
                    <button type="submit" class="btn btn-primary">proceed to subscribe Yearly</button>
                </form>
                <form action="{{ route('subscribe.checkout') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan" value="monthly">
                    <button type="submit" class="btn btn-primary">proceed to subscribe Monthly</button>
                </form>
            </div>
        </div>
    </div>
</div>