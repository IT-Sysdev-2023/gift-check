<?php

namespace App\Jobs\Accounting;

use App\Events\AccountingReportEvent;
use App\Exports\Accounting\SpgcReleasedMultiExport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Exports\Accounting\SpgcApprovedMultiExport;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\User;
use App\Services\Accounting\Reports\ReportGenerator;
use App\Services\Documents\ExportHandler;
use App\Services\Treasury\Reports\ReportHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use App\Helpers\NumberHelper;
use Illuminate\Support\Facades\Log;

class SpgcReleasedReport extends ReportGenerator implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */


    const RELEASED_TYPE = false;
    private User|null $user;
    public function __construct(protected array $request)
    {
        parent::__construct();
        $this->progress['name'] = "Pdf SPGC Released Report";
        $this->user = Auth::user();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $doc = $this->generateDocument($this->request['format'], $this->request['date']);

        (new ExportHandler())
            ->setFolder('Reports')
            ->setSubfolderAsUsertype($this->user->usertype)
            ->setFileName('SPGC Released Report-' . $this->user->user_id, $this->request['date'][0] . ' to ' . $this->request['date'][1])
            ->exportDocument($this->request['format'], $doc, function ($docu, $document) {

                //this is to broadcast the generation is about to finish
                if ($docu === 'excel') {
                    $document->progress['isDone'] = true;
                    AccountingReportEvent::dispatch($this->user, $document->progress, $document->reportId);
                } else {
                    $this->broadcastProgress($this->user, "Done", true);
                }
            });
    }

    private function handleRecords($date)
    {
        //initialize
        $this->setTransactionDate($date)
            ->setType(self::RELEASED_TYPE)
            ->setFormat($this->request['format'])
            ->setTotalRecord();

        $record = collect();
        $record->put('perCustomer', value: $this->perCustomerRecord($this->user));
        $record->put('perBarcode', $this->perBarcode($this->user));

        $header = collect([
            'reportCreated' => now()->toFormattedDateString(),
            'subtitle' => 'Special External GC Report'
        ]);

        $header->put('transactionDate', ReportHelper::transactionDateLabel(true, $date));

        return [
            'header' => $header,
            'records' => $record,
            'total' => [
                'noOfGc' => NumberHelper::toLocaleString(collect($record['perCustomer'])->sum('totcnt')) . ' pcs',
                'gcAmount' => NumberHelper::currency(collect($record['perCustomer'])->sum('totalDenomInt'))
            ]
        ];
    }

    private function generateDocument(string $format, array $date)
    {
        return $format === 'pdf'
            ? Pdf::loadView('pdf.accountingSpgcApprovedReport', ['data' => $this->handleRecords($date)])->output()
            : new SpgcReleasedMultiExport($date, $this->user);
    }
}
