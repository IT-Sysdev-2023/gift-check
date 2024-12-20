<?php

namespace App\Exports\StoreAccounting;

use App\Events\AccountingReportEvent;
use App\Events\StoreAccountReportEvent;
use App\Models\Store;
use App\Models\StoreLocalServer;
use App\Models\User;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
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

class VerifiedGcReportPerGcType implements FromCollection, ShouldAutoSize, WithTitle, WithHeadings, WithMapping, WithStyles, WithEvents, WithCustomStartCell, WithColumnFormatting
{

    public function __construct(protected Collection $data, protected string|int|null $store = null, protected &$progress = null, protected $reportId = null, protected ?User $user = null)
    {

    }
    public function collection()
    {
        $arr_perdate = collect();
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

        $datedisplay = '';

        $this->data->groupBy('date')->each(function ($items, $date) use (&$special, &$bng, &$promo, &$regular, &$cntr, &$purchased, &$arr_perdate, &$datedisplay) {

            $gcTypeMapping = [
                'SPECIAL EXTERNAL' => ['target' => 'special', 'array' => &$special],
                'REGULAR' => ['target' => 'regular', 'array' => &$regular],
                'BEAM AND GO' => ['target' => 'bng', 'array' => &$bng],
                'PROMOTIONAL GC' => ['target' => 'promo', 'array' => &$promo],
            ];
            foreach ($items as $item) {
                if ((float) $item['purchasecred'] > 0) {
                    $terminal = explode(",", $item['terminalno']);
                    $purchase = explode(",", $item['purchaseamt']);

                    collect($terminal)->each(function ($i, $key) use ($purchase, &$purchased, $gcTypeMapping, $item) {
                        $explodeTerminal = Str::trim(explode('-', $i)[0]) ?? null;

                        $mapping = $gcTypeMapping[$item['gc_type']];

                        $mapping['array'][$explodeTerminal] += (float) $purchase[$key];
                        $purchased[$mapping['target']] += $item['purchasecred']; // Update purchasecred
                    });
                }
            }
            $arr_perdate->push([
                'arr_perdate' => $date,
                'regular' => $purchased['regular'],
                'special' => $purchased['special'],
                'bng' => $purchased['bng'],
                'promo' => $purchased['promo'],
                'terminalreg' => $regular,
                'terminalspec' => $special,
                'terminalbng' => $bng,
                'terminalpromo' => $promo,
            ]);

            // Reset arrays for the next date

            $purchased = array_fill_keys(array_keys($purchased), 0);
            $regular = array_fill_keys(array_keys($regular), 0);
            $special = array_fill_keys(array_keys($special), 0);
            $bng = array_fill_keys(array_keys($bng), 0);
            $promo = array_fill_keys(array_keys($promo), 0);
        });

        return $arr_perdate;
    }

    public function map($data): array
    {
        $this->broadcast("Gc Report Per Gc Type");
        return [
            [
                $data['arr_perdate'],
                'REGULAR',
                $data['regular'],
                $data['terminalreg']['SM'],
                $data['terminalreg']['HF'],
                $data['terminalreg']['MP'],
                $data['terminalreg']['FR'],
                $data['terminalreg']['WHOLESALE']
            ],
            [
                '',
                'SPECIAL EXTERNAL',
                $data['special'],
                $data['terminalspec']['SM'],
                $data['terminalspec']['HF'],
                $data['terminalspec']['MP'],
                $data['terminalspec']['FR'],
                $data['terminalspec']['WHOLESALE'],

            ],
            [
                '',
                'BEAM AND GO',
                $data['bng'],
                $data['terminalbng']['SM'],
                $data['terminalbng']['HF'],
                $data['terminalbng']['MP'],
                $data['terminalbng']['FR'],
                $data['terminalbng']['WHOLESALE'],
            ],
            [
                '',
                'PROMOTIONAL GC',
                $data['promo'],
                $data['terminalpromo']['SM'],
                $data['terminalpromo']['HF'],
                $data['terminalpromo']['MP'],
                $data['terminalpromo']['FR'],
                $data['terminalpromo']['WHOLESALE'],
            ]
        ];
    }
    public function startCell(): string
    {
        return 'A7';
    }
    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
        ];
    }
    public function registerEvents(): array
    {

        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet;
                $storeName = Store::where('store_id', $this->store)->value('store_name');

                $sheet->setCellValue('C1', 'ALTURAS GROUP OF COMPANIES');
                $sheet->setCellValue('C2', 'CUSTOMER FINANCIAL SERVICES CORP');
                $sheet->setCellValue('B3', 'MONTHLY REPORT ON GIFT CHECK (Per GC Type & BU)');
                $sheet->setCellValue('C5', 'BUSINESS UNIT:' . $storeName);

                $sheet->mergeCells('C1:E1');
                $sheet->mergeCells('C2:E2');
                $sheet->mergeCells('B3:F3');
                $sheet->mergeCells('C5:E5');
            
                $sheet->getStyle('B1:C5')->getFont()->setBold(true);
                $sheet->getStyle('B1:C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

        ];
    }
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

    private function broadcast(string $info, bool $isDone = false, $id = null)
    {
        $this->progress['info'] = $info;
        $this->progress['progress']['currentRow']++;
        $this->progress['isDone'] = $isDone;

        StoreAccountReportEvent::dispatch($this->user, $this->progress, $id ?? $this->reportId);
    }
}
