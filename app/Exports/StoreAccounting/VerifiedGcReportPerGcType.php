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
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class VerifiedGcReportPerGcType implements FromCollection, ShouldAutoSize, WithTitle, WithHeadings, WithMapping, WithStyles, WithEvents, WithCustomStartCell
{

    public function __construct(protected Collection $data, protected string|int $store, protected &$progress = null, protected $reportId = null, protected ?User $user = null)
    {

    }
    public function collection()
    {
        $transformData = collect();
        $arr_perdate = collect();
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

        $this->data->each(function ($item) use (&$amounts, &$special, &$bng, &$promo, &$regular, &$cntr, &$purchased, &$arr_perdate, &$datedisplay) {

            if ((float) $item['purchasecred'] > 0) {

                $terminal = explode(",", $item['terminalno']);
                $purchase = explode(",", $item['purchaseamt']);

                // dd($purchase);
                collect($terminal)->each(function ($item, $key) use ($purchase, &$amounts) {
                    $explodeTerminal = explode('-', $item)[0] ?? null;

                    $amounts[$explodeTerminal] += (float) $purchase[$key];
                });
            }

            $gcTypeMapping = [
                'SPECIAL EXTERNAL' => ['target' => 'special', 'array' => &$special],
                'REGULAR' => ['target' => 'regular', 'array' => &$regular],
                'BEAM AND GO' => ['target' => 'bng', 'array' => &$bng],
                'PROMOTIONAL GC' => ['target' => 'promo', 'array' => &$promo],
            ];

            if (($cntr === 1) || $item['date'] === $datedisplay) {
                $datedisplay = $item['date'];

                $mapping = $gcTypeMapping[$item['gc_type']];

                foreach ($amounts as $key => $value) {
                    $mapping['array'][$key] += $value; // Update corresponding array
                }
                $purchased[$mapping['target']] += $item['purchasecred']; // Update purchasecred

            } else {

                $arr_perdate->push([
                    'arr_perdate' => $datedisplay,
                    'regular' => $purchased['regular'],
                    'special' => $purchased['special'],
                    'bng' => $purchased['bng'],
                    'promo' => $purchased['promo'],
                    'terminalreg' => $regular,
                    'terminalspec' => $special,
                    'terminalbng' => $bng,
                    'terminalpromo' => $promo
                ]);

                //Reset to Zero
                $this->resetArrays($regular, $special, $bng, $promo, $purchased);

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

            $amounts = array_fill_keys(array_keys($amounts), 0);

            $cntr++;
            if ($this->data->count() === $cntr) {
                $arr_perdate->push([
                    'arr_perdate' => $datedisplay,
                    'regular' => $purchased['regular'],
                    'special' => $purchased['special'],
                    'bng' => $purchased['bng'],
                    'promo' => $purchased['promo'],
                    'terminalreg' => $regular,
                    'terminalspec' => $special,
                    'terminalbng' => $bng,
                    'terminalpromo' => $promo
                ]);
            }
        });

        return $arr_perdate->filter(fn($i) => $i['arr_perdate'] !== '');
    }
    public function resetArrays(&$regular, &$special, &$bng, &$promo, &$purchased)
    {
        foreach ([$regular, $special, $bng, $promo, $purchased] as &$array) {
            $array = array_fill_keys(array_keys($array), 0);
        }
    }

    public function map($data): array
    {
        dd($data);
        // $this->broadcastProgress("Generating Barcode Records");
        return [
            $data['arr_perdate'],
            [
                'REGULAR',
                'SPECIAL EXTERNAL',
                'BEAM AND GO'
            ]
            // [
            //     $data['regular'],
            //     $data['special'],
            //     $data['bng'],
            //     $data['promo'],
            // ],
            // [
            //     $data['terminalreg']['SM'],
            //     $data['terminalspec']['SM'],
            //     $data['terminalbng']['SM'],
            //     $data['terminalpromo']['SM'],
            // ],
            // [
            //     $data['terminalreg']['HF'],
            //     $data['terminalspec']['HF'],
            //     $data['terminalbng']['HF'],
            //     $data['terminalpromo']['HF'],
            // ],
            // [
            //     $data['terminalreg']['MP'],
            //     $data['terminalspec']['MP'],
            //     $data['terminalbng']['MP'],
            //     $data['terminalpromo']['MP'],
            // ],
            // [
            //     $data['terminalreg']['FR'],
            //     $data['terminalspec']['FR'],
            //     $data['terminalbng']['FR'],
            //     $data['terminalpromo']['FR'],
            // ],
            // [
            //     $data['terminalreg']['WHOLESALE'],
            //     $data['terminalspec']['WHOLESALE'],
            //     $data['terminalbng']['WHOLESALE'],
            //     $data['terminalpromo']['WHOLESALE'],
            // ],
        ];
    }
    public function startCell(): string
    {
        return 'A7';
    }
    public function registerEvents(): array
    {

        return [
            // BeforeSheet::class => function (BeforeSheet $event) {
            //     $sheet = $event->sheet;
            //     $storeName = Store::where('store_id', $this->store)->value('store_name');

            //     $sheet->setCellValue('D1', 'ALTURAS GROUP OF COMPANIES');
            //     $sheet->setCellValue('D2', 'CUSTOMER FINANCIAL SERVICES CORP');
            //     $sheet->setCellValue('D3', 'MONTHLY REPORT ON GIFT CHECK (Per GC Type & BU)');
            //     $sheet->getStyle('D1:D3')->getFont()->setBold(true);
            //     $sheet->getStyle('D1:D3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            //     $sheet->setCellValue('D5', 'BUSINESS UNIT:' . $storeName);
            //     $sheet->getStyle('D5')->getFont()->setBold(true);
            // }

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
            'WHOLESALE',
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
