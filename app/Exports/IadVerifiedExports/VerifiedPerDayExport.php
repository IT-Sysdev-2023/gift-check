<?php

namespace App\Exports\IadVerifiedExports;

use App\Models\Store;
use App\Models\StoreEodTextfileTransaction;
use App\Models\StoreLocalServer;
use App\Models\StoreVerification;
use App\Traits\VerifiedExportsTraits\VerifiedTraits;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithTitle;

class VerifiedPerDayExport implements FromCollection, WithHeadings, WithStyles, WithTitle
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
        return 'Verified Per Day';
    }

    public function headings(): array
    {
        return [
            'DATE',
            'BARCODE',
            'DENOMINATION',
            'AMOUNT REDEEM',
            'BALANCE',
            'CUSTOMER NAME',
            'BUSINESS UNIT',
            'TERMINAL #',
            'VALIDATION',
            'GC TYPE',
            'DATE',
            'TIME',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $data = $this->getMonthYearVerifiedGc($this->requestData);

        $rowcount = count($data) + 1;

        $colcount = count($data[0]);

        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colcount);

        $range = 'A1:' . $lastColumn . $rowcount;

        $sheet->getStyle(1)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

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
        ]);

        for ($col = 1; $col <= $colcount; $col++) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }
        return $sheet;
    }


    public function collection()
    {
        $request = collect($this->requestData);

        if ($request['datatype'] === 'vgc') {

            if ($this->checkIfExists()) {
                dd(1);
                $storeLocServer = $this->getStoreLocalServer();

                if (is_null($storeLocServer)) {

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Server not found',
                    ]);
                } else {
                }
            } else {
                return $this->getMonthYearVerifiedGc($this->requestData);
            }
        }
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
