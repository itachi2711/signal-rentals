<?php

namespace App\Console\Commands;

use App\Events\LeaseNextPeriod;
use App\Models\Landlord;
use App\Models\Tenant;
use App\Traits\CommunicationMessage;
use Illuminate\Console\Command;

class LeaseNext extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lease:next';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculates lease charges as per defined period';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     */
    public function handle()
    {
        event(new LeaseNextPeriod());
    }
}
