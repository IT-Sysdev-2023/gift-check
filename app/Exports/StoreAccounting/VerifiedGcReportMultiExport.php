<?php

namespace App\Exports\StoreAccounting;

use App\Events\StoreAccountReportEvent;
use App\Models\Store;
use App\Models\StoreLocalServer;
use App\Models\StoreVerification;
use App\Models\User;
use App\Services\Progress;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class VerifiedGcReportMultiExport extends Progress implements WithMultipleSheets
{
    use Exportable;

    private $data;
    public function __construct(protected array $requirements, protected User $user, protected $db)
    {
        parent::__construct($user);

        $label = isset($requirements['month']) ? 'Monthly' : 'Yearly';
        $this->progress['name'] = "Excel Verified Gc Report ($label)";
        // $this->isLocal ? DB::table('store_verification')  :
        $server = $this->getServerDatabase($db);

        $this->data = $this->getVerifiedData($server, $this->requirements);

        // //Count Total Rows for Broadcasting
        $this->progress['progress']['totalRow'] += (new VerifiedGcReportPerDay($this->data))->collection()->count();
        $this->progress['progress']['totalRow'] += (new VerifiedGcReportPerDayTransaction($this->requirements))->collection()->count();
        $this->progress['progress']['totalRow'] += (new VerifiedGcReportPerGcType($this->data))->collection()->count();
    }

    public function sheets(): array
    {
        return [
            // Per Day
            new VerifiedGcReportPerDay($this->data, $this->progress, $this->reportId, $this->user),

            //By Month Summary Per Day
            new VerifiedGcReportPerDayTransaction($this->requirements, $this->progress, $this->reportId, $this->user, $this->db),

            //By Gc Type & BU
            new VerifiedGcReportPerGcType($this->data, $this->requirements['selectedStore'], $this->progress, $this->reportId, $this->user),
        ];
    }

    private function getVerifiedData($db, $requirements)
    {

        $data = $db->table('store_verification')->selectRaw("
        DATE(store_verification.vs_date) as datever,
        DATE(store_verification.vs_reverifydate) as daterev,
        store_verification.vs_barcode,
        store_verification.vs_tf_denomination,
        store_verification.vs_tf_purchasecredit,
        customers.cus_fname,
        customers.cus_lname,
        customers.cus_mname,
        customers.cus_namext,
        store_verification.vs_tf_balance,
        store_verification.vs_gctype,
        store_verification.vs_date,
        store_verification.vs_time")
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->whereYear('vs_date', $requirements['year'])
            ->when(isset($requirements['month']), fn($q) => $q->whereMonth('vs_date', $requirements['month']))
            ->where('vs_store', $requirements['selectedStore'])
            ->cursor();

        $transformedData = collect();

        $data->each(function ($q) use ($db, &$transformedData) {
            $balance = 0;
            $purchasecred = 0;
            $vsdate = '';
            $vstime = '';
            $bus = "";
            $tnum = "";
            $puramt = "";

            $gctype = match ($q->vs_gctype) {
                1 => 'REGULAR',
                3 => 'SPECIAL EXTERNAL',
                6 => 'BEAM AND GO',
                4 => 'PROMO',
            };

            if ($q->daterev != '') {
                $balance = $q->vs_tf_denomination;
            } else {

                $bdata = self::getTextfile($db, $q->vs_barcode);

                if ($bdata->isNotEmpty()) {
                    if ($bdata->count() == 1) {
                        $puramt = $bdata->pluck('seodtt_credpuramt')->implode('');
                        $bus = $bdata->pluck('seodtt_bu')->implode('');
                        $tnum = $bdata->pluck('seodtt_terminalno')->implode('');
                    } else {
                        $puramt = $bdata->pluck('seodtt_credpuramt')->implode(', ');
                        $bus = $bdata->pluck('seodtt_bu')->implode(', ');
                        $tnum = $bdata->pluck('seodtt_terminalno')->implode(', ');
                    }
                }
                $purchasecred = $q->vs_tf_purchasecredit;
                $balance = $q->vs_tf_balance;
                $vsdate = $q->vs_date;
                $vstime = $q->vs_time;
            }

            $transformedData->push([
                'date' => $q->datever,
                'barcode' => $q->vs_barcode,
                'denomination' => $q->vs_tf_denomination,
                'purchasecred' => $purchasecred,
                'cus_fname' => $q->cus_fname,
                'cus_lname' => $q->cus_lname,
                'cus_mname' => $q->cus_mname,
                'cus_namext' => $q->cus_namext,
                'balance' => $balance,
                'valid_type' => 'VERIFIED',
                'gc_type' => $gctype,
                'businessunit' => $bus,
                'terminalno' => $tnum,
                'vsdate' => $vsdate,
                'vstime' => $vstime,
                'purchaseamt' => $puramt
            ]);
        });

        return $transformedData;
    }
    private static function getTextfile($db, $barcode)
    {
        return $db->table('store_eod_textfile_transactions')
            ->select('seodtt_bu', 'seodtt_terminalno', 'seodtt_credpuramt')
            ->where('seodtt_barcode', $barcode)->get();
    }
    private function getServerDatabase($connection)
    {
        // $lserver = StoreLocalServer::where('stlocser_storeid', $store)
        //     ->value('stlocser_ip');
        //     Log::info($islocal);
        // $parts = collect(explode('.', $lserver));
        // $result = $islocal == 1 ? '' : '-' . $parts->slice(2)->implode('.');

        return DB::connection($connection);



    }
}
