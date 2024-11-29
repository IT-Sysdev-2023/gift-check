<?php

namespace App\Exports\IadSpecialReviewed;

use App\Helpers\NumberHelper;
use App\Models\SpecialExternalGcrequestEmpAssign;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SpecialReviewedExportsPerCustomer implements FromView, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $request;
    public function title(): string
    {
        return 'Per Customer';
    }

    public function __construct($requestData)
    {
        $this->request = $requestData;
    }
    public function view(): View
    {
        $date = Date::parse($this->request['date'][0])->toFormattedDateString() . ' - ' .Date::parse($this->request['date'][1])->toFormattedDateString();

        return view('excel.specialreviewedpercustomer', [
            'data' => $this->getSpecialGcReviewedDataPerCustomer(),
            'date' => $date
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $highestColumn = $event->sheet->getDelegate()->getHighestColumn(); // Find the last column

                foreach (range('A', $highestColumn) as $column) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
    public function getSpecialGcReviewedDataPerCustomer()
    {
        $data = SpecialExternalGcrequestEmpAssign::select(
            'spexgc_datereq',
            'spexgcemp_denom',
            'spexgc_num',
            'reqap_date',
            'spcus_acctname',
            'spcus_companyname',
        )
            ->join('special_external_gcrequest', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_emp_assign.spexgcemp_trid')
            ->join('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->where('reqap_approvedtype', '=', 'Special External GC Approved')
            ->whereBetween('spexgc_datereq', $this->request['date'])
            ->orderBy('spexgc_datereq', 'ASC')
            ->get()
            ->groupBy('spexgc_num');


        return  $data->transform(function ($item, $key) {
            return (object) [
                'denom' => NumberHelper::currency($item->sum('spexgcemp_denom')),
                'date' => $item[0]->spexgc_datereq,
                'num' => $key,
                'acct' => $item[0]->spcus_acctname,
            ];
        });
    }
}
