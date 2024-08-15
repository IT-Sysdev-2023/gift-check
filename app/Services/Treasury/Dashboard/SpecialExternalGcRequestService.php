<?php

namespace App\Services\Treasury\Dashboard;

use App\Http\Resources\SpecialExternalGcRequestResource;
use App\Models\SpecialExternalGcrequest;
use App\Models\StoreGcrequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

class SpecialExternalGcRequestService
{


    public static function approvedGc(): Collection //<a href="#/special-external-request-approved/">
    {
        $record = SpecialExternalGcrequest::joinSpecialExternalCustomer()
            ->select('approved_request.*', 'special_external_gcrequest.*')
            ->leftJoin('approved_request', function (JoinClause $join) {
                $join->on('special_external_gcrequest.spexgc_id', 'approved_request.reqap_trid')
                    ->where('approved_request.reqap_approvedtype', 'Special External GC Approved');
            })
            ->select('spexgc_id', 'spexgc_num', 'spexgc_datereq', 'spexgc_dateneed', 'reqap_approvedby', 'reqap_date', 'spcus_acctname', 'spcus_companyname')
            ->spexgcStatus('approved')->get();

        return $record;
    }

    public static function reviewedGc(): Collection //special-external-gc-reviewed
    {
        $data = SpecialExternalGcrequest::joinSpecialExternalCustomer()
            ->with([
                'user:user_id,firstname,lastname',
                'approvedRequest' => function (Builder $builder) {
                    $builder->selectColumn()->approvedType('Special External GC Approved');
                }
            ])
            // ->withWhereHas(
            //     'approvedRequest',
            //     function ($query) {
            //         $query->approvedType('Special External GC Approved');
            //     }
            // )
            ->select('spexgc_id', 'spexgc_company', 'spexgc_reqby', 'spexgc_num', 'spexgc_dateneed', 'spexgc_datereq', 'spcus_acctname', 'spcus_companyname')
            ->spexgcStatus('approved')
            ->spexgcReviewed('reviewed')
            ->spexgcReleased('')
            ->spexgcPromo('0')
            ->oldest('spexgc_id')
            ->limit(10)
            ->get();

        return $data;
    }


    public static function releasedGc(): Collection //released-special-external-request
    {
        $record = SpecialExternalGcrequest::releasedGc()->get();
        // return SpecialExternalGcRequestResource::collection($record);
        return $record;
    }

    public static function cancelledGcRequest(): Collection //cancelled-gc-request.php
    {
        return StoreGcrequest::cancelledGcRequest()->get();
    }


}
