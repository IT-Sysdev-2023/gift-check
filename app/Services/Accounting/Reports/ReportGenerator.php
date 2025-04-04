<?php

namespace App\Services\Accounting\Reports;

use App\Events\AccountingReportEvent;
use App\Models\SpecialExternalGcrequestEmpAssign;

use App\Models\User;
use App\Services\Progress;
use Illuminate\Support\Facades\Date;
use App\Helpers\NumberHelper;
use Illuminate\Support\Facades\Log;

class ReportGenerator extends Progress
{
    protected $date;
    protected $format;
    protected bool $type;

    public function __construct()
    {
        parent::__construct();
        $this->progress['name'] = "Pdf Accounting Report";

    }
    protected function setTransactionDate($date)
    {
        $this->date = $date;
        return $this;
    }

    protected function setFormat(string $format)
    {
        $this->format = $format;
        return $this;
    }
    protected function setType(bool $type)
    {
        $this->type = $type;
        return $this;
    }
    protected function setTotalRecord()
    {
        $barcode = $this->type ? $this->getApprovedBarcodeQuery() : $this->getReleasedBarcodeQuery();
        $released = $this->type ? $this->getApprovedPerCustomerQuery() : $this->getReleasedPerCustomerQuery();

        $this->progress['progress']['totalRow'] = $barcode->count() + $released->count();
    }
    protected function perCustomerRecord(User $user)
    {
        $data = $this->type ? $this->getApprovedPerCustomerQuery()
            : $this->getReleasedPerCustomerQuery();

        return $data->map(function ($item) use ($user) {
            //Dispatch
            $this->broadcastProgress($user, "Generating Customer Records");

            $item->datereq = Date::parse($item->datereq)->toFormattedDateString();
            $item->daterel = Date::parse($item->daterel)->toFormattedDateString();
            $item->totalDenomInt = $item->totDenom;
            $item->totDenom = NumberHelper::currency($item->totDenom);
            return $item;
        });

    }

    protected function perBarcode(User $user)
    {
        $data = $this->type ? $this->getApprovedBarcodeQuery()
            : $this->getReleasedBarcodeQuery();

        $count = 1;

        return $data->map(function ($item) use (&$count, $user) {
            //Dispatch
            $this->broadcastProgress($user, "Generating Barcodes Records");

            $item->count = $count++;
            $item->datereq = Date::parse($item->datereq)->toFormattedDateString();
            $item->daterel = Date::parse($item->daterel)->toFormattedDateString();
            $item->spexgcemp_denom = NumberHelper::currency($item->spexgcemp_denom);
            $item->fullName = $item->spexgcemp_lname . ', ' . $item->spexgcemp_fname;
            return $item;
        });
    }

    private function getReleasedBarcodeQuery()
    {
        return SpecialExternalGcrequestEmpAssign::select(
            'special_external_gcrequest_emp_assign.spexgcemp_denom',
            'special_external_gcrequest_emp_assign.spexgcemp_fname',
            'special_external_gcrequest_emp_assign.spexgcemp_lname',
            'special_external_gcrequest_emp_assign.spexgcemp_mname',
            'special_external_gcrequest_emp_assign.voucher',
            'special_external_gcrequest_emp_assign.spexgcemp_extname',
            'special_external_gcrequest_emp_assign.spexgcemp_barcode',

            'special_external_gcrequest.spexgc_num',
            'special_external_gcrequest.spexgc_datereq as datereq',
            'approved_request.reqap_date as daterel'
        )
            ->joinDataBarTables()
            ->specialReleased($this->date)
            ->orderBy('special_external_gcrequest_emp_assign.spexgcemp_barcode')
            ->cursor();
    }
    private function getReleasedPerCustomerQuery()
    {
        return SpecialExternalGcrequestEmpAssign::selectRaw("
        COALESCE(SUM(special_external_gcrequest_emp_assign.spexgcemp_denom), 0) AS totDenom,
        COALESCE(COUNT(special_external_gcrequest_emp_assign.spexgcemp_barcode), 0) AS totcnt,
        special_external_gcrequest.spexgc_num,
        special_external_gcrequest.spexgc_datereq as datereq,
        approved_request.reqap_date as daterel,
        special_external_customer.spcus_acctname
")
            ->joinDataAndGetOnTables()
            ->specialReleased($this->date)
            ->groupBy(
                'special_external_gcrequest.spexgc_datereq',
                'special_external_gcrequest.spexgc_num',
                'approved_request.reqap_date',
                'special_external_customer.spcus_acctname',
            )
            ->orderBy('special_external_gcrequest.spexgc_datereq')
            ->cursor();
    }
    private function getApprovedBarcodeQuery()
    {
        return SpecialExternalGcrequestEmpAssign::select(
            'special_external_gcrequest_emp_assign.spexgcemp_denom',
            'special_external_gcrequest_emp_assign.spexgcemp_fname',
            'special_external_gcrequest_emp_assign.spexgcemp_lname',
            'special_external_gcrequest_emp_assign.spexgcemp_mname',
            'special_external_gcrequest_emp_assign.voucher',
            'special_external_gcrequest_emp_assign.spexgcemp_extname',
            'special_external_gcrequest_emp_assign.spexgcemp_barcode',

            'special_external_gcrequest.spexgc_num',
            'special_external_gcrequest.spexgc_datereq as datereq',
            'approved_request.reqap_date as daterel'
        )
            ->joinDataBarTables()
            ->specialApproved($this->date)
            ->orderBy('special_external_gcrequest_emp_assign.spexgcemp_barcode')
            ->cursor();
    }
    private function getApprovedPerCustomerQuery()
    {
        return SpecialExternalGcrequestEmpAssign::selectRaw("
        COALESCE(SUM(special_external_gcrequest_emp_assign.spexgcemp_denom), 0) AS totDenom,
        COALESCE(COUNT(special_external_gcrequest_emp_assign.spexgcemp_barcode), 0) AS totcnt,
        special_external_gcrequest.spexgc_num,
        special_external_gcrequest.spexgc_datereq as datereq,
        approved_request.reqap_date as daterel,
        special_external_customer.spcus_acctname
")
            ->joinDataAndGetOnTables()
            ->specialApproved($this->date)
            ->groupBy(
                'special_external_gcrequest.spexgc_datereq',
                'special_external_gcrequest.spexgc_num',
                'approved_request.reqap_date',
                'special_external_customer.spcus_acctname',
            )
            ->orderBy('special_external_gcrequest.spexgc_datereq')
            ->cursor();
    }
}