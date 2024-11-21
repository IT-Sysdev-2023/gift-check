<?php

namespace App\Jobs;

use App\DashboardRoutesTrait;
use App\Events\TreasuryReportEvent;
use App\Models\Store;
use App\Models\User;
use App\Services\Documents\ExportHandler;
use App\Services\Treasury\Reports\ReportHelper;
use App\Services\Treasury\Reports\ReportsHandler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class GcReport extends ReportsHandler implements ShouldQueue
{
    use Queueable, DashboardRoutesTrait;
    /**
     * Create a new job instance.
     */

    private object $request;
    private User|null $user;
    public function __construct($request)
    {
        parent::__construct();
        $this->request = (object) $request;
        $this->user = Auth::user();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $storeData = [];

        //All Stores
        if ($this->request->store === 'all') {
            $store = Store::selectStore()->cursor();

            $percentage = 1;

            $this->progress['progress']['totalRow'] = count($store);
            foreach ($store as $item) {
                $this->progress['progress']['currentRow'] = $percentage++;
                TreasuryReportEvent::dispatch($this->user, $this->progress);

                $storeData[] = $this->handleRecords( $item->value);
            }

        } else {
            $storeData[] = $this->handleRecords( $this->request->store);
            $this->progress['progress']['totalRow'] = 1;
            $this->progress['progress']['currentRow'] = 1;
            TreasuryReportEvent::dispatch($this->user, $this->progress);
        }

        $pdf = Pdf::loadView('pdf.treasuryReport', ['data' => ['stores' => $storeData]]);
        $transDateHeader = ReportHelper::transactionDateLabel($this->isDateRange, $this->transactionDate);

        (new ExportHandler())
            ->setFolder('Reports/' . $this->roleDashboardRoutes[$this->user->usertype] . '/')
            ->setFilename('Gc Report-' . $this->user->user_id, $transDateHeader)
            ->exportToPdf($pdf->output())
            ->deleteFileIn(now()->addDays(2));
    }
    private function handleRecords(string $store)
    {

        $record = collect();
        $footerData = collect();

        $reportType = collect($this->request->reportType);

        $this->setStore($store)->setDateOfTransactions($this->request);

        if (!is_null($this->transactionDate) && $this->hasRecords($this->request, $this->user)) {

            if ($reportType->contains('gcSales')) {
                $record->put('gcSales', $this->gcSales($this->user));
                $footerData->put('gcSalesFooter', $this->footer($this->user));
            }

            if ($reportType->contains('refund')) {
                $footerData->put('refundFooter', $this->refund());
            }
            if ($reportType->contains('gcRevalidation')) {
                $footerData->put('revalidationFooter', $this->gcRevalidation());
            }
        } else {
            return [
                'error' => 'No Transactions'
            ];
        }

        return [
            'header' => $this->pdfHeaderDate($this->request, $this->user),
            'data' => [...$record],
            'footer' => [...$footerData],
        ];



    }
}
