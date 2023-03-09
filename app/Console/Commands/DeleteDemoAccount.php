<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteDemoAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DeleteDemo:Account';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this command is used to delete demo account after created of 3 days';

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
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            // $currentdate=Date('Y-m-d');
            // $currentdatesec=strtotime($currentdate.'-3 Days');
            // dd(Date('Y-m-d',$currentdatesec));


            $demoaccount = Customer::select('id', 'customer_type', 'active_date', 'plan_expiry_date', 'created_at')
                ->where('customer_type', 3)->get();
            foreach ($demoaccount as $key => $demodata) {
                $accountexpiredate= $demodata['plan_expiry_date'];
                $deletelimittimesec = strtotime($accountexpiredate . '+3 days');
                $currentdate = Date('Y-m-d');
                $currentdatesec = strtotime($currentdate);
                // dd($deletelimittimesec,$currentdatesec);
                if ($currentdatesec > $deletelimittimesec) {
                    DB::table('customers')->where('id',$demodata['id'])->update(['deleted_at'=>Carbon::now()->toDateTime()]);
                }
            }

         return "success";   
        } catch (\Exception $e) {
            // dd($e);
            Log::channel('democron')->error(
                'Failed to run demo account delete',
                ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]
            );
        }
    }
}
