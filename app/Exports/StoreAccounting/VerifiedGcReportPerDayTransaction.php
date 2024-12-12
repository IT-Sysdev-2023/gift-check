<?php

namespace App\Exports\StoreAccounting;

use App\Events\AccountingReportEvent;
use App\Models\Store;
use App\Models\StoreLocalServer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VerifiedGcReportPerDayTransaction implements FromCollection, ShouldAutoSize, WithTitle, WithHeadings, WithMapping, WithStyles, WithEvents
{

    public function __construct(protected array $requirements, protected &$progress = null, protected $reportId = null, protected ?User $user = null)
    {

    }
    public function collection()
    {
        $db = self::getServerDatabase($this->requirements['selectedStore']);

        return $this->getVerifiedData($db, $this->requirements);
    }
    public function map($data): array
    {
        // $this->broadcastProgress("Generating Barcode Records");
        return [
            (new \DateTime($data['date']))->format('F j, Y'),
            $data['totverifiedgc'],
            $data['redeem'],
            $data['balance'],
        ];
    }

    private static function getTextfile($db, $barcode)
    {
        return $db->table('store_eod_textfile_transactions')
            ->select('seodtt_bu', 'seodtt_terminalno', 'seodtt_credpuramt')
            ->where('seodtt_barcode', $barcode)->get();
    }

    private function getVerifiedData($db, $requirements)
    {

        $query1 = $db->table('store_verification')->selectRaw("
            DATE(store_verification.vs_date) as datever,
            IFNULL(SUM(store_verification.vs_tf_denomination),00.0) as totverifiedgc,
            IFNULL(SUM(store_verification.vs_tf_balance),00.0) as balance,
            IFNULL(SUM(store_verification.vs_tf_purchasecredit),00.0) as redeem
        ")
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->where(
                fn($q) =>
                $q->whereYear('vs_date', $requirements['year'])
                    ->orWhereYear('vs_reverifydate', $requirements['year'])
            )
            ->where('vs_store', $requirements['selectedStore'])
            ->groupBy('vs_date')
            ->cursor();

        $transformedData = collect();

        $query1->each(function ($q) use (&$transformedData) {
            $transformedData->push([
                'date' => $q->datever,
                'totverifiedgc' => $q->totverifiedgc,
                'balance' => $q->balance,
                'redeem' => $q->redeem
            ]);
        });

        $query2 = $db->table('store_verification')->selectRaw("
                    DATE(store_verification.vs_reverifydate) as datever,
                    IFNULL(SUM(store_verification.vs_tf_denomination),00.0) as sumver,
                    IFNULL(SUM(store_verification.vs_tf_balance),00.0) as balance,
                    IFNULL(SUM(store_verification.vs_tf_purchasecredit),00.0) as redeem
            ")
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->whereYear('vs_reverifydate', $requirements['year'])
            ->where('vs_store', $requirements['selectedStore'])
            ->groupBy('vs_date','vs_reverifydate')
            ->cursor();

        $query2->each(function ($q) use (&$transformedData) {

            $transformedData->map(function ($value) use ($q, &$transformedData) {
                if ($q->datever == $value['date']) {
                    $value['totverifiedgc'] = (float) $q->sumver + (float) $value['totverifiedgc'];
                    $value['balance'] = (float) $q->balance + (float) $value['balance'];
                    $value['redeem'] = (float) $q->redeem + (float) $value['redeem'];
                }
                return $value;
            });
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

    public function registerEvents(): array
    {

        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet;
                $storeName = Store::where('store_id', $this->requirements['selectedStore'])->value('store_name');
                $sheet->setCellValue('D1', 'ALTURAS GROUP OF COMPANIES');
                $sheet->setCellValue('D2', 'CUSTOMER FINANCIAL SERVICES CORP');
                $sheet->setCellValue('D3', 'MONTHLY REPORT ON GIFT CHECK (GC)');
                $sheet->getStyle('D1:D3')->getFont()->setBold(true);
                $sheet->getStyle('D1:D3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('D5', 'BUSINESS UNIT:' . $storeName);
                $sheet->getStyle('D5')->getFont()->setBold(true);
            }

        ];
    }
    // public function countRecords()
    // {
    //     return $this->query()->count();
    // }
    public function title(): string
    {
        return 'By Month Summary Per Day';
    }

    public function headings(): array
    {
        return [
            'DATE',
            'TOTAL GC VERIFIED',
            'TOTAL GC AMOUNT REDEEM',
            'BALANCES',
            // 'TOTAL GC PURCHASE BASED ON POS',
            // 'VARIANCE',
            // 'REMARKS'
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
