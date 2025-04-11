<?php

namespace App\Exports\AdminEodExcelReport;

use App\Models\TransactionStore;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class EodReportExcel implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    protected string $store;
    protected array|string $dateRange;

    public function __construct(array $request)
    {
        $this->store = $request['store'] ?? '';
        $this->dateRange = $request['date'] ?? [];
    }

    public function headings(): array
    {
        $storeName = $this->getStoreMapping($this->store);
        return [
            ['ALTURAS GROUP OF COMPANIES'],
            ['CUSTOMER FINANCIAL SERVICES CORP'],
            ['SELECTED STORE: ' . $storeName],
            [],
            [
                'Transaction Number',
                'Transaction Store',
                'Transaction Date',
                'Transaction Cashier',
            ],
        ];
    }
    public function getStoreMapping(string $storeId)
    {
        $storeMapping = [
            '1' => 'Alturas Mall',
            '2' => 'Alturas Talibon',
            '3' => 'Island City Mall',
            '4' => 'Plaza Marcela',
            '5' => 'Alturas Tubigon',
            '6' => 'Colonade Colon',
            '7' => 'Colonade Mandaue',
            '8' => 'Alta Citta',
            '9' => 'Farmers Market',
            '10' => 'Ubay Distribution Center',
            '11' => 'Screenville',
            '12' => 'Asc Tech',
        ];
        return $storeMapping[$storeId] ?? '';
    }

    public function array(): array
    {
        $results = $this->query();

        return $results->map(function ($item) {
            return [
                $item['trans_number'] ?? '',
                $item['store_name'] ?? '',
                $item['trans_datetime'] ?? '',
                $item['ss_username'] ?? '',
            ];
        })->toArray();
    }

    public function query()
    {
        $query = TransactionStore::join('stores', 'store_id', '=', 'trans_store')
            ->join('store_staff', 'ss_id', '=', 'trans_cashier')
            ->where('trans_yreport', '!=', '0')
            ->where('trans_store', $this->store)
            ->where('trans_eos', '!=', '');

        if (is_array($this->dateRange) && count($this->dateRange) === 2) {
            $query->whereBetween('trans_datetime', [$this->dateRange[0], $this->dateRange[1]]);
        } else {
            $query->whereDate('trans_datetime', $this->dateRange);
        }
        return $query->get();
    }

    public function styles(Worksheet $sheet): array
    {
        // Merge and style header rows
        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('A2:D2');
        $sheet->mergeCells('A3:D3');

        // Center align and bold the header rows
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(14);

        // Style column headers (row 5)
        $sheet->getStyle('A5:D5')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center']
        ]);

        // Center align all data rows
        $sheet->getStyle('A6:D' . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal('center');

        return [];
    }
}
