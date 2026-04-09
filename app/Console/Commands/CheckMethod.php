<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Financial\FinancialController;
use ReflectionClass;

class CheckMethod extends Command
{
    protected $signature = 'check:method';
    protected $description = 'Check if method exists';

    public function handle()
    {
        $controller = new FinancialController();
        $exists = method_exists($controller, 'showInvoice');
        $this->info('Method showInvoice exists: ' . ($exists ? 'Yes' : 'No'));

        if ($exists) {
            $reflector = new ReflectionClass($controller);
            $method = $reflector->getMethod('showInvoice');
            $this->info('Defined in: ' . $method->getFileName() . ':' . $method->getStartLine());
        } else {
            $reflector = new ReflectionClass($controller);
            $this->info('Class file: ' . $reflector->getFileName());
            $this->info('Methods: ' . implode(', ', get_class_methods($controller)));
        }
    }
}
