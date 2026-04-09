<?php

namespace App\Events;

use App\Models\Company;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyActivityLogged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $company;
    public $activity;
    public $causer;

    public function __construct(Company $company, string $activity, $causer = null)
    {
        $this->company = $company;
        $this->activity = $activity;
        $this->causer = $causer;
    }
}
