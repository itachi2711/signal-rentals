<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/22/2021
 * Time: 5:28 PM
 */

namespace App\Console\Commands;

use App\Events\LeaseNextPeriod;
use App\Events\OverdueInvoiceChecked;
use Illuminate\Console\Command;

class PenaltyCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'penalty:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculates penalties on overdue invoices';

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
        event(new OverdueInvoiceChecked());
    }
}
