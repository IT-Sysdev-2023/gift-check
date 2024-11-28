<?php

namespace App\Exports\IadSpecialReviewed;

use App\Models\SpecialExternalGcrequestEmpAssign;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Date;


class SpecialReviewedExportsPerCustomer implements FromView
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
        return view('excel.specialreviewed', [
            'data' => $this->getSpecialGcReviewedDataPerCustomer(),
        ]);
    }
    public function getSpecialGcReviewedDataPerCustomer()
    {

        $data = SpecialExternalGcrequestEmpAssign::with(
            'specialExternalGcrequest:spexgc_id,spexgc_num,spexgc_datereq,spexgc_num,spexgc_company,spexgc_reqby',
            'specialExternalGcrequest.approvedRequest:reqap_trid,reqap_date',
            'specialExternalGcrequest.user:user_id,firstname,lastname',
            'specialExternalGcrequest.specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname',
        )->whereHas('specialExternalGcrequest.approvedRequest', function ($query) {
            $query->where('reqap_approvedtype', 'Special External GC Approved')
                ->whereBetween('spexgc_datereq', $this->request['date']);
        })
            ->get();

        dd($data->toArray());
    }
}
