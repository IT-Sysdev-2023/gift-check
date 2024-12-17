<?php

namespace App\Exports\StoreAccounting;

use App\Events\AccountingReportEvent;
use App\Models\Store;
use App\Models\StoreLocalServer;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VerifiedGcReportPerDay implements FromCollection, ShouldAutoSize, WithTitle, WithHeadings, WithMapping, WithStyles
{

    public function __construct(protected Collection $data, protected &$progress = null, protected $reportId = null, protected ?User $user = null)
    {

    }
    public function collection()
    {
    
        return $this->data;
    }
    public function map($data): array
    {
        // $this->broadcastProgress("Generating Barcode Records");
        return [
            (new \DateTime($data['date']))->format('F j, Y'),
            $data['barcode'],
            $data['denomination'],
            $data['purchasecred'],
            Str::headline($data['cus_fname'] . '_' . $data['cus_lname'],),
            $data['balance'],
            $data['businessunit'],
            $data['terminalno'],
            $data['valid_type'],
            $data['gc_type'],
            $data['vsdate'] . ', ' . $data['vstime'],
        ];
    }

    private function getReverifiedData($server, $requirements)
    {
        $data = $server->table('store_verification')->selectRaw("
        DATE(store_verification.vs_reverifydate) as datever,
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
            ->whereYear('vs_reverifydate', $requirements['year'])
            ->where('vs_store', $requirements['selectedStore'])
            ->orderBy('vs_id')
            ->limit(10)
            ->get();
        $transformedData = collect();

        $data->each(function ($q) use ($server, &$transformedData) {
            $balance = 0;
            $purchasecred = 0;

            $gctype = match ($q->vs_gctype) {
                1 => 'REGULAR',
                3 => 'SPECIAL EXTERNAL',
                6 => 'BEAM AND GO'
            };



            $bus = "";
            $tnum = "";
            $puramt = "";

            $bdata = self::getTextfile($server, $q->vs_barcode);

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
                'valid_type' => 'REVERIFIED',
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
    private static function getServerDatabase($store)
    {
        $lserver = StoreLocalServer::where('stlocser_storeid', $store)
            ->value('stlocser_ip');

        $parts = collect(explode('.', $lserver));
        $result = $parts->slice(2)->implode('.');

        return DB::connection('mariadb-' . $result);
    }
    
    // public function countRecords()
    // {
    //     return $this->query()->count();
    // }
    public function title(): string
    {
        return 'Per Day';
    }

    public function headings(): array
    {
        return [
            'DATE',
            'BARCODE',
            'DENOMINATION',
            'AMOUNT REDEEM',
            'CUSTOMER NAME',
            'BALANCE',
            'BUSINESS UNIT',
            'TERMINAL #',
            'VALIDATION',
            'GC TYPE',
            'DATE GENERATED',
        ];
    }
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    // private function broadcastProgress(string $info)
    // {
    //     $this->progress['info'] = $info;
    //     $this->progress['progress']['currentRow']++;
    //     AccountingReportEvent::dispatch($this->user, $this->progress, $this->reportId);
    // }
}
