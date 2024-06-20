<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SpecialExternalGcrequestEmpAssign extends Model
{
    use HasFactory;
    protected $table = 'special_external_gcrequest_emp_assign';

    protected $primaryKey = 'spexgcemp_id';

    public static function scopeSelectFilter($builder)
    {
        $builder->select(
            DB::raw("IFNULL(SUM(special_external_gcrequest_emp_assign.spexgcemp_denom), 0.00) as totdenom"),
            DB::raw("IFNULL(COUNT(special_external_gcrequest_emp_assign.spexgcemp_barcode), 0.00) as totcnt"),
            'special_external_gcrequest.spexgc_num',
            DB::raw("DATE_FORMAT(special_external_gcrequest.spexgc_datereq, '%m/%d/%Y') as datereq"),
            DB::raw("DATE_FORMAT(approved_request.reqap_date, '%m/%d/%Y') as daterel"),
            DB::raw("CONCAT(reqby.firstname, ' ', reqby.lastname) as trby"),
            'special_external_customer.spcus_acctname',
            'special_external_customer.spcus_companyname'
        );
    }
    public static function scopeOrderByFilter($builder)
    {
        $builder->groupBy(
            'special_external_gcrequest.spexgc_num',
            'special_external_gcrequest.spexgc_datereq',
            'approved_request.reqap_date',
            'reqby.firstname',
            'reqby.lastname',
            'special_external_customer.spcus_acctname',
            'special_external_customer.spcus_companyname'
        );
    }
    public static function scopeJoinDataAndGetOnTables($builder)
    {
        $builder->join('special_external_gcrequest', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_emp_assign.spexgcemp_trid')
            ->join('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
            ->join('users as reqby', 'reqby.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company');
    }
    public static function scopeJoinDataBarTables($builder)
    {
        $builder->join('special_external_gcrequest', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_emp_assign.spexgcemp_trid')
            ->join('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id');
    }
    public static function scopeSelectBarFilter($builder)
    {
        $builder->select(
            'special_external_gcrequest_emp_assign.spexgcemp_denom',
            'special_external_gcrequest_emp_assign.spexgcemp_fname',
            'special_external_gcrequest_emp_assign.spexgcemp_lname',
            'special_external_gcrequest_emp_assign.spexgcemp_mname',
            'special_external_gcrequest_emp_assign.spexgcemp_extname',
            'special_external_gcrequest_emp_assign.spexgcemp_barcode',
            'special_external_gcrequest_emp_assign.voucher',
            'special_external_gcrequest.spexgc_num',
            DB::raw("DATE_FORMAT(special_external_gcrequest.spexgc_datereq, '%m/%d/%Y') as datereq"),
            DB::raw("DATE_FORMAT(approved_request.reqap_date, '%m/%d/%Y') as daterel"),
            DB::raw("CONCAT(special_external_gcrequest_emp_assign.spexgcemp_fname, ' ', special_external_gcrequest_emp_assign.spexgcemp_lname) as full_name")
        );
    }
    public static function scopeFilter($builder, $filter)
    {
        return $builder->when($filter['dateRange'] ?? null, function ($query, $date) {
            $query->whereBetween('approved_request.reqap_date', [$date[0], $date[1]]);
        })->when($filter['search'] ?? null, function ($query, $search) {
            $query->whereAny([
                'bledger_no',
                'bledger_type',
                'bledger_datetime',
                'bdebit_amt',
                'bcredit_amt',
            ], 'LIKE', '%' . $search . '%');
        });
    }
}
