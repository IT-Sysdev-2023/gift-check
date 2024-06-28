<?php

namespace App\Services\Finance;

use App\Models\SpecialExternalGcrequestEmpAssign;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ApprovedReleasedReportService
{

    public static function approvedReleasedQueryCus(Request $requestQuery)
    {
        return  SpecialExternalGcrequestEmpAssign::joinDataAndGetOnTables()
            ->selectFilter()
            ->OrderByFilter()
            ->where('approved_request.reqap_approvedtype', empty($requestQuery['approvedType'])  ? null : $requestQuery['approvedType'])
            ->whereBetween('approved_request.reqap_date', [empty($requestQuery['dateRange']) ? [null, null] : $requestQuery['dateRange']])
            ->paginate(10)->withQueryString();
    }
    public static function approvedReleasedQueryBar(Request $requestQuery)
    {
        return SpecialExternalGcrequestEmpAssign::joinDataBarTables()
        ->selectBarFilter()
        ->where('approved_request.reqap_approvedtype',empty($requestQuery['approvedType'])  ? null : $requestQuery['approvedType'])
        ->orderBy('special_external_gcrequest_emp_assign.spexgcemp_barcode')
        ->whereBetween('approved_request.reqap_date', [empty($requestQuery['dateRange']) ? [null, null] : $requestQuery['dateRange']])
        ->paginate(10)
        ->withQueryString();
    }
    public static function approvedReleasedGenerate($requestQuery)
    {
        return  SpecialExternalGcrequestEmpAssign::joinDataAndGetOnTables()
        ->selectFilter()
        ->OrderByFilter()
        ->where('approved_request.reqap_approvedtype', empty($requestQuery['approvedType'])  ? null : $requestQuery['approvedType'])
        ->whereBetween('approved_request.reqap_date', [empty($requestQuery['dateRange']) ? [null, null] : $requestQuery['dateRange']])
        ->get();
    }
    public static function approvedReleasedBarGenerate($requestQuery)
    {
        return SpecialExternalGcrequestEmpAssign::joinDataBarTables()
        ->selectBarFilter()
        ->where('approved_request.reqap_approvedtype', empty($requestQuery['approvedType'])  ? null : $requestQuery['approvedType'])
        ->orderBy('special_external_gcrequest_emp_assign.spexgcemp_barcode')
        ->whereBetween('approved_request.reqap_date', [empty($requestQuery['dateRange']) ? [null, null] : $requestQuery['dateRange']])
        ->get();
    }

}
