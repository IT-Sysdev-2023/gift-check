<?php

namespace App\Exports\IadSpecialReviewed;

use App\Helpers\NumberHelper;
use App\Models\SpecialExternalGcrequestEmpAssign;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithTitle;

class SpecialReviewedExportsPerBarcode implements FromView, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;
    protected $request;

    public function title(): string
    {
        return 'Per Barcode';
    }

    public function __construct($requestData)
    {
        $this->request = $requestData;
    }
    public function view(): View
    {
        return view('excel.specialreviewed', [
            'data' => $this->getSpecialGcReviewedDataPerBarcode(),
        ]);
    }
    private function getSpecialGcReviewedDataPerBarcode()
    {
        $data =  SpecialExternalGcrequestEmpAssign::select(
            'spexgcemp_trid',
            'spexgcemp_denom',
            'spexgcemp_fname',
            'spexgcemp_lname',
            'spexgcemp_mname',
            'spexgcemp_barcode',

        )
            ->with(
                'specialExternalGcrequest:spexgc_id,spexgc_num,spexgc_datereq,spexgc_num',
                'specialExternalGcrequest.approvedRequest:reqap_trid,reqap_date'
            )
            ->whereHas('specialExternalGcrequest.approvedRequest', function ($query) {
                $query->where('reqap_approvedtype', 'Special External GC Approved')
                    ->whereBetween('reqap_date', $this->request['date']);
            })
            ->orderBy('spexgcemp_barcode')
            ->get();

        $data->transform(function ($item) {

            $item->custname = Str::ucfirst($item->spexgcemp_fname) . ' ' .
                Str::ucfirst($item->spexgcemp_mname) . ' ' .
                Str::ucfirst($item->spexgcemp_lname);
            $item->transdate = Date::parse($item->specialExternalGcrequest->spexgc_datereq)->toFormattedDateString();
            $item->dateApproved = Date::parse($item->specialExternalGcrequest->approvedRequest->reqap_date)->toFormattedDateString();
            $item->num = $item->specialExternalGcrequest->spexgc_num;
            $item->denom = NumberHelper::currency($item->spexgcemp_denom);

            return $item;
        });

        return $data;
    }
}
