<?php

namespace App\Exports\IadVerifiedExports;

use App\Helpers\NumberHelper;
use App\Models\Store;
use App\Models\StoreLocalServer;
use App\Models\StoreVerification;
use App\Traits\VerifiedExportsTraits\VerifiedTraits;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VerifiedSummaryExports implements FromCollection, WithTitle, WithEvents, WithStyles, WithHeadings
{

    /**
     * @return \Illuminate\Support\Collection
     */

    use Exportable;

    use VerifiedTraits;

    protected $requestData;

    public function __construct($requestData)
    {
        $this->requestData = $requestData;
    }
    public function title(): string
    {
        return 'By Month Summary per day';
    }
    public function headings(): array
    {
        return [
            'DATE',
            'TOTAL GC VERIFIED',
            'TOTAL GC AMOUNT REDEEM',
            'BALANCE',
            'TOTAL GC PURCHASE BASED ON POS',
            'VARIANCE',
            'REMARKS',
        ];
    }

    public function collection()
    {

        $data = $this->getDataStoreVerifivation();

        return collect($data);
    }
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet;
                $sheet->setCellValue('A1', 'ALTURAS GROUP OF COMPANIES');
                $sheet->setCellValue('A2', 'CUSTOMER FINANCIAL SERVICES CORP');
                $sheet->setCellValue('A3', 'MONTHLY REPORT ON GIFT CHECK (GC)');
                $sheet->setCellValue('A4', 'PERIOD COVER: JANUARY 1 - 31, 2019');
                $sheet->setCellValue('A6', 'BUSINESS UNITS: ALTURAS MALL');
                $sheet->setCellValue('A7', '');
            },
        ];
    }

    private function getDataStoreVerifivation()
    {
        // dd();

        $request = $this->requestData;

        $database = [];

        $storeLocServer = $this->getStoreLocalServer();

        if ($request['datatype'] === 'vgc') {

            if ($this->checkIfExists()) {
                // dd();
                $database = $this->getLocalServerData($storeLocServer)->table('store_verification');
            } else {
                $database = new StoreVerification();
            }
        }

        $vsdata = $database->select(
            'vs_date',
            'vs_cn',
            'vs_tf_denomination',
            'vs_tf_balance',
            'vs_tf_purchasecredit',
            'vs_store'
        )->when(str_contains($request['date'], '-'), function ($q) use ($request) {
            $q->whereLike('vs_date', '%' . $request['date'] . '%')
                ->orWhereLike('vs_reverifydate', '%' . $request['date'] . '%');
        }, function ($q) use ($request) {
            $q->whereYear('vs_date', $request['date'])
                ->orWhere('vs_reverifydate', fn($q) => $q->whereYear('vs_reverifydate', $request['date']));
        })->get()
            ->where('vs_store', $request['store'])
            ->groupBy('vs_date');

        $revdata =  $database->when(str_contains($request['date'], '-'), function ($q) use ($request) {
            $q->whereLike('vs_reverifydate', '%' . $request['date'] . '%');
        }, function ($q) use ($request) {
            $q->whereYear('vs_reverifydate', $request['date']);
        })
            ->where('vs_store', $request['store'])
            ->get()
            ->groupBy('vs_date')
            ->map(function ($group) {
                return [
                    'daterev' => $group->value('vs_reverifydate'),
                    'totalrev' => $group->sum('vs_tf_denomination'),
                    'balance' => $group->sum('vs_tf_balance'),
                    'redeem' => $group->sum('vs_tf_purchasecredit'),
                ];
            });

        $array = [];

        $vsdata->transform(function ($item, $key) use (&$array) {

            $array[] = [
                'date' => $key,
                'totalver' => $this->numberHelperFormat($item->sum('vs_tf_denomination')),
                'redeem' => $this->numberHelperFormat($item->sum('vs_tf_purchasecredit')),
                'balance' => $this->numberHelperFormat($item->sum('vs_tf_balance')),
            ];

            return $array;
        });

        $appendArray = collect($array)->transform(function ($item) use ($revdata) {

            $parseDate = Date::parse($item['date'])->format('Y-m-d');

            $isExists = $revdata->where(function ($value) use (&$parseDate) {
                $dateRev = Date::parse($value['daterev'])->format('Y-m-d');
                return $dateRev === $parseDate;
            });


            if ($isExists->isNotEmpty()) {

                $total =  $this->numberHelperFormat($isExists->sum('totalrev') + (float) $item['totalver']);
                $totalbal = $this->numberHelperFormat($isExists->sum('balance') + (float) $item['balance']);
                $totalred = $this->numberHelperFormat($isExists->sum('redeem') + (float) $item['redeem']);

                $item['totalver'] = $total;
                $item['redeem'] = $totalred;
                $item['balance'] = $totalbal;
            }
            return $item;
        });

        $appendArray->transform(function ($item) {
            return (object)[
                'date' => Date::parse($item['date'])->toFormattedDateString(),
                'totalver' => NumberHelper::format($item['totalver']),
                'redeem' => NumberHelper::format($item['redeem']),
                'balance' => NumberHelper::format($item['balance']),
            ];
        });

        return collect($appendArray);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(8)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

        $data = $this->getDataStoreVerifivation();

        $rowcount = $data->count();

        $colcount = count($this->headings());

        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colcount);



        $range = 'A8:' . $lastColumn  . $rowcount + 8;



        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'size' => 9,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Center horizontally
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, // Center vertically
            ],
        ]);

        for ($col = 1; $col <= $colcount; $col++) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }


        return [
            'A1' => [
                'font' => [
                    'bold' => true,
                    'name' => 'Fira Code',
                    'size' => 8,
                ],
            ],
            'A2' => [
                'font' => [
                    'bold' => true,
                    'name' => 'Fira Code',
                    'size' => 8,
                ],
            ],
            'A3' => [
                'font' => [
                    'bold' => true,
                    'name' => 'Fira Code',
                    'size' => 8,
                ],
            ],
            'A4' => [
                'font' => [
                    'bold' => true,
                    'name' => 'Fira Code',
                    'size' => 8,
                ],
            ],
            'A6' => [
                'font' => [
                    'bold' => true,
                    'name' => 'Fira Code',
                    'size' => 8,
                ],
            ],
        ];
    }
    private function numberHelperFormat($number)
    {
        return number_format($number, 1, '.', '');
    }


    public function checkIfExists()
    {
        return Store::where('store_id', $this->requestData['store'])->where('has_local', 1)->exists();
    }

    public function getStoreLocalServer()
    {
        return StoreLocalServer::select(
            'stlocser_ip',
            'stlocser_username',
            'stlocser_password',
            'stlocser_db'
        )->where('stlocser_storeid', $this->requestData['store'])->first();
    }
}
