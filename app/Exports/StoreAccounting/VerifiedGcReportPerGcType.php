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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VerifiedGcReportPerGcType implements FromCollection, ShouldAutoSize, WithTitle, WithHeadings, WithMapping, WithStyles, WithEvents
{

    public function __construct(protected Collection $data, protected string|int $store, protected &$progress = null, protected $reportId = null, protected ?User $user = null)
    {

    }
    public function collection()
    {
        $transformData = collect();

        $amountSM = 0;
        $amountHF = 0;
        $amountMP = 0;
        $amountFR = 0;
        $amountSod = 0;
        $amountWholeSale = 0;
        $this->data->each(function ($item) {
            if ((float) $item['purchasecred'] > 0) {

                $terminal = explode(",", $item['terminalno']);
                $purchase = explode(",", $item['purchaseamt']);

                collect($terminal)->each(function ($item, $key) use ($purchase) {
                    $explodeTerminal = explode('-', $item);

                     match($explodeTerminal[0]){
                        'SM' => $amountSM = $purchase[$key],
                        'HF' => $amountHF =$purchase[$key],
                        'MP' => $amountMP = $purchase[$key],
                        'FR' => $amountFr = $purchase[$key],
                        'SOD' => $amountSOD = $purchase[$key],
                        'WHOLESALE' => $amountWholeSale = $purchase[$key],
                    };


                    dd($explodeTerminal, $this->data);
                });
            }
        });



        return;
    }
    public function map($data): array
    {
        // $this->broadcastProgress("Generating Barcode Records");
        return [
            (new \DateTime($data['date']))->format('F j, Y'),
            $data['barcode'],
            $data['denomination'],
            $data['purchasecred'],
            Str::headline($data['cus_fname'] . '_' . $data['cus_lname'], ),
            $data['balance'],
            $data['businessunit'],
            $data['terminalno'],
            $data['valid_type'],
            $data['gc_type'],
            $data['vsdate'] . ', ' . $data['vstime'],
        ];
    }

    public function registerEvents(): array
    {

        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet;
                $storeName = Store::where('store_id', $this->store)->value('store_name');

                $sheet->setCellValue('D1', 'ALTURAS GROUP OF COMPANIES');
                $sheet->setCellValue('D2', 'CUSTOMER FINANCIAL SERVICES CORP');
                $sheet->setCellValue('D3', 'MONTHLY REPORT ON GIFT CHECK (Per GC Type & BU)');
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
        return 'By Gc Type & BU';
    }

    public function headings(): array
    {
        return [
            'DATE',
            'GC Type',
            'AMOUNT',
            'SM',
            'HF',
            'MP',
            'FR',
            'SOD',
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
