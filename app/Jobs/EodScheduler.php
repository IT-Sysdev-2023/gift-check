<?php

namespace App\Jobs;

use App\Models\InstitutEod;
use App\Models\InstitutPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
class EodScheduler implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    private $user;
    public function __construct()
    {
        $this->user = Auth::user();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $insti = InstitutPayment::select('insp_id')->where('institut_eodid', '0')->get();
      
        if ($insti->isNotEmpty()) {
            DB::transaction(function ()  {
                $eod_num = InstitutEod::orderByDesc('ieod_id')->value('ieod_num');
                $incre = $eod_num ? $eod_num + 1 : 1;

                $insertedRecord = InstitutEod::create([
                    'ieod_date' => now(), 
				    'ieod_by' => $this->user->user_id,
				    'ieod_num' => $incre
                ]);

                InstitutPayment::select('insp_id')->where('institut_eodid', '0')
                    ->update(['institut_eodid' => $insertedRecord->ieod_id]);
            });
        }
    }
}
