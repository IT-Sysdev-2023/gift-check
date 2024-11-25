<?php

namespace App\Jobs\Accounting;

use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\User;
use App\Services\Accounting\Reports\ReportGenerator;
use App\Services\Documents\ExportHandler;
use App\Services\Treasury\Reports\ReportHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use App\Helpers\NumberHelper;

class SpgcApprovedReport extends ReportGenerator implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    private User|null $user;
    public function __construct(protected array $request)
    {
        parent::__construct();
        $this->user = Auth::user();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $pdf = Pdf::loadView('pdf.accountingSpgcApprovedReport', ['data' => $this->handleRecords($this->request['date'])]);

        (new ExportHandler())
            ->setFolder('Reports')
            ->setSubfolderAsUsertype($this->user->usertype)
            ->setFileName('SPGC Approved Report-' . $this->user->user_id, $this->request['date'][0] . 'To' . $this->request['date'][1])
            // ->exportToExcel($this->dataForExcel($request->date));
            ->exportToPdf($pdf->output());

    }

    private function handleRecords($date)
    {
        $record = collect();

        $this->setTransactionDate($date)->setTotalRecord();
        $record->put('perCustomer', $this->perCustomerRecord($this->user));
        $record->put('perBarcode', $this->perBarcode($this->user));

        $header = collect([
            'reportCreated' => now()->toFormattedDateString(),
        ]);

        $transDateHeader = ReportHelper::transactionDateLabel(true, $date);

        $header->put('transactionDate', $transDateHeader);

        return [
            'header' => $header,
            'records' => $record,
        ];
    }


}
