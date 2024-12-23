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

        $this->broadcast("GC report Per Day!");
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

    private function broadcast(string $info, bool $isDone = false, $id = null)
    {
        $this->progress['info'] = $info;
        $this->progress['progress']['currentRow']++;
        $this->progress['isDone'] = $isDone;

        StoreAccountReportEvent::dispatch($this->user, $this->progress, $id ?? $this->reportId);
    }
}
