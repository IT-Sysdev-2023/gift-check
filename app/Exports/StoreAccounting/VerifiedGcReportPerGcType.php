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
        $arr_perdate = [];
        $amounts = [
            'SM' => 0,
            'HF' => 0,
            'MP' => 0,
            'FR' => 0,
            'SOD' => 0,
            'WHOLESALE' => 0,
        ];

        $purchased = [
            'special' => 0,
            'regular' => 0,
            'bng' => 0,
            'promo' => 0
        ];

        $special = [
            'SM' => 0,
            'HF' => 0,
            'MP' => 0,
            'FR' => 0,
            'SOD' => 0,
            'WHOLESALE' => 0
        ];
        $regular = [
            'SM' => 0,
            'HF' => 0,
            'MP' => 0,
            'FR' => 0,
            'SOD' => 0,
            'WHOLESALE' => 0
        ];
        $bng = [
            'SM' => 0,
            'HF' => 0,
            'MP' => 0,
            'FR' => 0,
            'SOD' => 0,
            'WHOLESALE' => 0
        ];
        $promo = [
            'SM' => 0,
            'HF' => 0,
            'MP' => 0,
            'FR' => 0,
            'SOD' => 0,
            'WHOLESALE' => 0
        ];
        $cntr = 0;
        $datedisplay = '';

        $this->data->each(function ($item) use (&$amounts, &$special, &$bng, &$promo, &$regular, &$cntr, &$purchased, &$arr_perdate, $datedisplay) {

            if ((float) $item['purchasecred'] > 0) {

                $terminal = explode(",", $item['terminalno']);
                $purchase = explode(",", $item['purchaseamt']);

                collect($terminal)->each(function ($item, $key) use ($purchase, &$amounts) {
                    $explodeTerminal = explode('-', $item)[0] ?? null;

                    if ($amounts[$explodeTerminal]) {
                        $amounts[$explodeTerminal] += (float) $purchase[$key];
                    }
                });
            }
            if ($item['date'] != $datedisplay) {

                if ($cntr === 1) {

                    $datedisplay = $item['date'];

                    $gcTypeMapping = [
                        'SPECIAL EXTERNAL' => ['target' => 'special', 'array' => &$special],
                        'REGULAR' => ['target' => 'regular', 'array' => &$regular],
                        'BEAM AND GO' => ['target' => 'bng', 'array' => &$bng],
                        'PROMOTIONAL GC' => ['target' => 'promo', 'array' => &$promo],
                    ];

                    $mapping = $gcTypeMapping[$item['gc_type']];

                    foreach ($amounts as $key => $value) {
                        $mapping['array'][$key] += $value; // Update corresponding array
                    }

                    $purchased[$mapping['target']] += $item['purchasecred']; // Update purchasecred
                } else {

                    $arr_perdate[] = array(
                        'arr_perdate' => $datedisplay,
                        'regular' => $purchased['regular'],
                        'special' => $purchased['special'],
                        'bng' => $purchased['bng'],
                        'promo' => $purchased['promo'],
                        'terminalreg' => $regular,
                        'terminalspec' => $special,
                        'terminalbng' => $bng,
                        'terminalpromo' => $promo
                    );

                    $regular = collect($regular)->map(fn($val, $key) => 0)->toArray();
                    $special = collect($special)->map(fn($val, $key) => 0)->toArray();
                    $bng = collect($bng)->map(fn($val, $key) => 0)->toArray();
                    $promo = collect($promo)->map(fn($val, $key) => 0)->toArray();

                    $purchased = collect($purchased)->map(fn($val, $key) => 0)->toArray();

                    $datedisplay = $item['date'];

                    $gcTypeMapping = [
                        'SPECIAL EXTERNAL' => ['target' => 'special', 'array' => &$special],
                        'REGULAR' => ['target' => 'regular', 'array' => &$regular],
                    ];

                    $mapping = $gcTypeMapping[$item['gc_type']];

                    foreach ($amounts as $key => $value) {
                        $mapping['array'][$key] += $value; // Update corresponding array
                    }

                    $purchased[$mapping['target']] += $item['purchasecred'];
                }
            } else {
                $gcTypeMapping = [
                    'SPECIAL EXTERNAL' => ['target' => 'special', 'array' => &$special],
                    'REGULAR' => ['target' => 'regular', 'array' => &$regular],
                    'BEAM AND GO' => ['target' => 'bng', 'array' => &$bng],
                    'PROMOTIONAL GC' => ['target' => 'promo', 'array' => &$promo],
                ];

                $mapping = $gcTypeMapping[$item['gc_type']];

                foreach ($amounts as $key => $value) {
                    $mapping['array'][$key] += $value; // Update corresponding array
                }

                $purchased[$mapping['target']] += $item['purchasecred']; // Update purchasecred
            }

            $amounts = collect($amounts)->map(fn($val, $key) => 0)->toArray();
            $cntr++;

            if($this->data->count() === $cntr){
                $arr_perdate[] =  [
                    'arr_perdate'   =>  $datedisplay,
                    'regular'       =>  $purchased['regular'],
                    'special'       =>  $purchased['special'],
                    'bng'           =>  $purchased['bng'],
                    'promo'         =>  $purchased['promo'],
                    'terminalreg'   =>  $regular,
                    'terminalspec'  =>  $special,
                    'terminalbng'   =>  $bng,
                    'terminalpromo' =>  $promo
                ]; 
            }
        });

        dd($arr_perdate);
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
