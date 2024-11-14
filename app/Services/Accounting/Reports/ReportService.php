<?php

namespace App\Services\Accounting\Reports;

use App\Models\SpecialExternalGcrequestEmpAssign;
use Request;

class ReportService
{
    public function specialGcReport(Request $request){

        $q = SpecialExternalGcrequestEmpAssign::where([
            ['approved_request.reqap_approvedtype', 'Special External GC Approved'],
            ['special_external_gcrequest_emp_assign.spexgc_status', '<>', 'inactive']])
        ->whereBetween('approved_request.reqap_date', [$request->from, $request->to])
        ->orderBy('special_external_gcrequest.spexgc_datereq')
        ->join('special_external_gcrequest', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_emp_assign.spexgcemp_trid')
        ->join('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
        ->join('users as reqby', 'reqby.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
        ->join('special_external_customer', 'special_external_customer.spcus_id', '=',  'special_external_gcrequest.spexgc_company')
        ;
        // $table = 'special_external_gcrequest_emp_assign';
        // $select = "IFNULL(SUM(special_external_gcrequest_emp_assign.spexgcemp_denom),0.00) as totdenom,
        //             IFNULL(COUNT(special_external_gcrequest_emp_assign.spexgcemp_barcode),0.00) as totcnt,
        //     special_external_gcrequest.spexgc_num,    
        //     DATE_FORMAT(special_external_gcrequest.spexgc_datereq, '%m/%d/%Y') as datereq,
        //     DATE_FORMAT(approved_request.reqap_date, '%m/%d/%Y') as daterel,
        //     CONCAT(reqby.firstname,' ',reqby.lastname) as trby,
        //      special_external_customer.spcus_acctname,
        //     special_external_customer.spcus_companyname";
        // $where = "approved_request.reqap_approvedtype = 'Special External GC Approved'
        //     AND
        //         special_external_gcrequest_emp_assign.spexgc_status != 'inactive' 
        //     AND
        //         (DATE_FORMAT(approved_request.reqap_date,'%Y-%m-%d') >= '{$startDate}'
        //     AND
        //         DATE_FORMAT(approved_request.reqap_date,'%Y-%m-%d') <= '{$endDate}')
        //     GROUP BY
        //         special_external_gcrequest.spexgc_num
        //     ORDER BY
        //         special_external_gcrequest.spexgc_datereq
        //     ASC";
        // $join = 'INNER JOIN
        //         special_external_gcrequest
        //     ON
        //         special_external_gcrequest.spexgc_id = special_external_gcrequest_emp_assign.spexgcemp_trid
        //     INNER JOIN
        //         approved_request
        //     ON
        //         approved_request.reqap_trid = special_external_gcrequest.spexgc_id
        //     INNER JOIN
        //         users as reqby
        //     ON
        //         reqby.user_id = special_external_gcrequest.spexgc_reqby
        //     INNER JOIN
        //         special_external_customer
        //     ON
        //         special_external_customer.spcus_id = special_external_gcrequest.spexgc_company';
        // $limit = '';
    
        // $datacus1 = getAllData($link,$table,$select,$where,$join,$limit);
    }
    public function generatePdf(){

    }
}