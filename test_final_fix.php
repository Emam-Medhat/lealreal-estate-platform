<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Test the appointments controller directly
    echo "--- Testing Appointments Controller ---\n";
    
    // Create a test user if not exists
    $user = \App\Models\User::where('email', 'test@example.com')->first();
    if (!$user) {
        $user = \App\Models\User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'account_status' => 'active',
            'username' => 'testuser',
        ]);
        echo "Created test user: " . $user->full_name . " (ID: " . $user->id . ")\n";
    } else {
        echo "Using existing user: " . $user->full_name . " (ID: " . $user->id . ")\n";
    }
    
    // Log in the user
    \Illuminate\Support\Facades\Auth::login($user);
    
    // Test the appointment controller
    $controller = new \App\Http\Controllers\AppointmentController();
    
    // Create a mock request
    $request = \Illuminate\Http\Request::create('/messages/appointments', 'GET');
    
    // Call the index method
    $response = $controller->index($request);
    
    echo "Appointments controller response: " . get_class($response) . "\n";
    
    if ($response instanceof \Illuminate\View\View) {
        echo "SUCCESS: Appointments controller returns view!\n";
        echo "View name: " . $response->getName() . "\n";
    } else {
        echo "Appointments controller returned: " . get_class($response) . "\n";
    }
    
    echo "\n--- Testing Crowdfunding Page ---\n";
    
    // Create a request to the crowdfunding route
    $request = \Illuminate\Http\Request::create('/investor/crowdfunding', 'GET');
    
    // Handle the request
    $response = $app->handle($request);
    
    echo "Crowdfunding response status: " . $response->getStatusCode() . "\n";
    
    if ($response->getStatusCode() === 200) {
        echo "SUCCESS: Crowdfunding page works!\n";
        echo "Page rendered successfully with all database tables present.\n";
    } else {
        echo "Error: " . $response->getStatusCode() . "\n";
        echo "Response preview: " . substr($response->getContent(), 0, 200) . "...\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
