<!DOCTYPE html>
<html>
<head>
    <title>Test Pricing</title>
</head>
<body>
    <h1>Subscription Plans</h1>
    <p>Found {{ $plans->count() }} plans</p>
    @foreach($plans as $plan)
        <div>
            <h2>{{ $plan->name }}</h2>
            <p>{{ $plan->description }}</p>
            <p>Price: ${{ $plan->price }}</p>
        </div>
    @endforeach
</body>
</html>
