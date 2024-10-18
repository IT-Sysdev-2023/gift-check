<?php

namespace App\Services\Treasury;

class ColumnHelper
{
    public static $approved_buget_request = [
        [
            'title' => 'BR No.',
            'dataIndex' => 'br_no',

        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'br_requested_at',

        ],
        [
            'title' => 'Budget Requested',
            'dataIndex' => 'br_request',

        ],
        [
            'title' => 'Prepared By',
            'dataIndex' => ['prepared_by', 'full_name'],
            'key' => 'prepared_by',

        ],
        [
            'title' => 'Date Approved',
            'dataIndex' => ['abr', 'approved_at'],
            'key' => 'date_approved'

        ],
        [
            'title' => 'Approved By',
            'dataIndex' => ['abr', 'approved_by'],
            'key' => 'approved_by',

        ],
        [
            'title' => 'Actions',
            'dataIndex' => 'action'

        ],
    ];

    public static $cancelled_buget_request = [
        [
            'title' => 'BR No.',
            'dataIndex' => 'br_no',

        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'br_requested_at',

        ],
        [
            'title' => 'Budget Requested',
            'dataIndex' => 'br_request',

        ],
        [
            'title' => 'Prepared By',
            'dataIndex' => ['prepared_by', 'full_name'],
            'key' => 'prepared_by',

        ],
        [
            'title' => 'Date Cancelled',
            'dataIndex' => ['cancelled_request', 'cdreq_at'],
            'key' => 'date_cancelled'

        ],
        [
            'title' => 'Cancelled By',
            // 'dataIndex' => ['cancelled_by', 'user', 'full_name'],
            'dataIndex' => 'approved_by',

        ],
        [
            'title' => 'Action',
            // 'dataIndex' => ['cancelled_by', 'user', 'full_name'],
            'dataIndex' => 'action',

        ],
    ];

    public static $pendingStoreGcRequest = [
        [
            'title' => 'Gc Request No.',
            'dataIndex' => 'sgc_num',

        ],
        [
            'title' => 'Requested By',
            'dataIndex' => ['store', 'store_name'],
            'key' => 'requested_by',

        ],
        [
            'title' => 'Prepared By',
            'dataIndex' => ['user', 'full_name'],
            'key' => 'prepared_by',

        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'sgc_date_request',

        ],
        [
            'title' => 'Request Status',
            'dataIndex' => 'sgc_status',
        ],
        [
            'title' => 'Action',
            'dataIndex' => 'action',

        ],
    ];

    public static $releasedStoreGcRequest = [
        [
            'title' => 'Released No.',
            'dataIndex' => 'agcr_request_relnum',

        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => ['storeGcRequest', 'sgc_date_request'],
            'key' => 'date_requested',

        ],
        [
            'title' => 'Retail Store',
            'dataIndex' => ['storeGcRequest', 'store', 'store_name'],
            'key' => 'retail_store'

        ],
        [
            'title' => 'Released By',
            'dataIndex' => ['user', 'full_name'],
            'key' => 'prepared_by',

        ],
        [
            'title' => 'Date Released',
            'dataIndex' => 'agcr_approved_at',

        ],
        [
            'title' => 'Approved By',
            'dataIndex' => 'agcr_approvedby',

        ],
        [
            'title' => 'Reprint',
            'dataIndex' => 'reprint',

        ],
    ];

    public static $cancelledStoreGcRequest = [
        [
            'title' => 'Req No.',
            'dataIndex' => 'sgc_num',

        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'sgc_date_request',
        ],
        [
            'title' => 'Retail Store',
            'dataIndex' => ['store', 'store_name'],
            'key' => 'retail_store'

        ],
        [
            'title' => 'Prepared By',
            'dataIndex' => ['user', 'full_name'],
            'key' => 'prepared_by',

        ],
        [
            'title' => 'Date Cancelled',
            'dataIndex' => ['cancelledStoreGcRequest', 'csgr_at'],
            'key' => 'date_cancelled'

        ],
        [
            'title' => 'Cancelled By',
            'dataIndex' => ['cancelledStoreGcRequest', 'user', 'full_name'],
            'key' => 'cancelled_by'

        ],
        [
            'title' => 'Actions',
            'dataIndex' => 'action',

        ],
    ];

    public static $approvedProductionRequest = [
        [
            'title' => 'PR No.',
            'dataIndex' => 'pe_num',

        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'pe_date_request',
        ],
        [
            'title' => 'Date Needed',
            'dataIndex' => 'pe_date_needed',

        ],
        [
            'title' => 'Requested By',
            'dataIndex' => ['user', 'full_name'],
            'key' => 'requested_by',

        ],
        [
            'title' => 'Date Approved',
            'dataIndex' => ['approvedProductionRequest', 'ape_approved_at'],
            'key' => 'date_approved'

        ],
        [
            'title' => 'Approved By',
            'dataIndex' => ['approvedProductionRequest', 'ape_approved_by'],
            'key' => 'approved_by'

        ],
        [
            'title' => 'Actions',
            'dataIndex' => 'action',

        ],
    ];
    public static $pendingSpecialGc = [
        [
            'title' => 'RFSEGC #',
            'dataIndex' => 'spexgc_num',

        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'spexgc_datereq',
        ],
        [
            'title' => 'Date Needed',
            'dataIndex' => 'spexgc_dateneed',

        ],
        [
            'title' => 'Total Denomination',
            'dataIndex' => 'totalGcRequestItems',

        ],
        [
            'title' => 'Customer',
            'dataIndex' => ['specialExternalCustomer', 'spcus_acctname'],
            'key' => 'customer'

        ],
        [
            'title' => 'Requested By',
            'dataIndex' => 'user',

        ],
        [
            'title' => 'Action',
            'dataIndex' => 'action',

        ],
    ];

    public static $approvedGcForReviewed = [
        [
            'title' => 'RFSEGC #',
            'dataIndex' => 'spexgc_num',

        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'spexgc_datereq',
        ],
        [
            'title' => 'Date Validity',
            'dataIndex' => 'spexgc_dateneed',

        ],
        [
            'title' => 'Total Denomination',
            'dataIndex' => 'totalGcRequestItems',

        ],
        [
            'title' => 'Customer',
            'dataIndex' => ['specialExternalCustomer', 'spcus_acctname'],
            'key' => 'customer'

        ],
        [
            'title' => 'Requested By',
            'dataIndex' => 'user',

        ],
        [
            'title' => 'Action',
            'dataIndex' => 'action',

        ],
    ];

    public static $gcHolder = [

        [
            'title' => 'Barcode',
            'dataIndex' => 'spexgcemp_barcode',
            'align' => 'center'
        ],
        [
            'title' => 'Denomination',
            'dataIndex' => 'spexgcemp_denom',
            'align' => 'center'
        ],
        [
            'title' => 'Voucher',
            'dataIndex' => 'voucher',
            'align' => 'center'
        ],
        [
            'title' => 'Lastname',
            'dataIndex' => 'spexgcemp_lname',
            'align' => 'center'
        ],
        [
            'title' => 'Firstname',
            'dataIndex' => 'spexgcemp_fname',
            'align' => 'center'
        ],
        [
            'title' => 'Middlename',
            'dataIndex' => 'spexgcemp_mname',
            'align' => 'center'
        ],
        [
            'title' => 'Name Ext.',
            'dataIndex' => 'spexgcemp_extname',
            'align' => 'center'
        ],
        [
            'title' => 'Address',
            'dataIndex' => 'address',
            'align' => 'center'
        ],
    ];
    public static $promoGcReleasing = [

        [
            'title' => 'RFPROM #',
            'dataIndex' => 'req_no',
            'align' => 'center'
        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'date_req',
            'align' => 'center'
        ],
        [
            'title' => 'Date Needed',
            'dataIndex' => 'date_needed',
            'align' => 'center'
        ],
        [
            'title' => 'Total GC',
            'dataIndex' => 'total',
            'align' => 'center'
        ],
        [
            'title' => 'Requested By',
            'dataIndex' => 'user',
            'align' => 'center'
        ],
        [
            'title' => 'Recommended By',
            'dataIndex' => ['approved_request_user', 'user', 'full_name'],
            'key' => 'recommended',
            'align' => 'center'
        ],
        [
            'title' => 'Approved By',
            'dataIndex' => 'approved_by_type',
            'align' => 'center'
        ],
        [
            'title' => 'Status',
            'dataIndex' => 'status',
            'align' => 'center'
        ],
        [
            'title' => 'Action',
            'dataIndex' => 'action',
            'align' => 'center'
        ],
    ];

    public static $institution_gc_sales = [
        [
            'title' => 'Transaction No.',
            'dataIndex' => 'institutrTrnum',

        ],
        [
            'title' => 'Customer',
            'key' => 'customer',

        ],
        [
            'title' => 'Date',
            'dataIndex' => 'date',

        ],
        [
            'title' => 'Time',
            'dataIndex' => 'time',

        ],
        [
            'title' => 'Gc (pcs)',
            'dataIndex' => 'institutTransactionItemCount',

        ],
        [
            'title' => 'Total Denom',
            'key' => 'totalDenom',

        ],
        [
            'title' => 'Payment Type',
            'dataIndex' => 'institutr_paymenttype',

        ],
        [
            'title' => 'Actions',
            'key' => 'action'

        ],
    ];
    public static $eodList = [

        [
            'title' => 'Date',
            'key' => 'ieod_date',

        ],
        [
            'title' => 'EOD Number',
            'dataIndex' => 'ieod_num',

        ],
        [
            'title' => 'EOD By',
            'key' => 'eodBy',

        ],
        [
            'title' => 'Actions',
            'dataIndex' => 'action',
            'width' => '150px'
        ],
    ];

    public static $gcReleasingReport = [

        [
            'title' => 'Transaction #',
            'dataIndex' => 'inspPaymentnum',

        ],
        [
            'title' => 'Customer',
            'dataIndex' => 'customer',

        ],
        [
            'title' => 'Date',
            'dataIndex' => 'date',

        ],
        [
            'title' => 'Time',
            'dataIndex' => 'time',
        ],
        [
            'title' => 'Total Denom',
            'dataIndex' => 'totalAmount',

        ],
        [
            'title' => 'Payment Type',
            'dataIndex' => 'payment',
        ],
    ];
    public static $retailGcReleasing = [

        [
            'title' => 'Gc Request #',
            'dataIndex' => 'sgc_num',

        ],
        [
            'title' => 'Requested By',
            'dataIndex' => 'store_name',

        ],
        [
            'title' => 'Date Needed',
            'dataIndex' => 'sgc_date_needed',

        ],
        [
            'title' => 'Prepared By',
            'dataIndex' => 'user',
        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'sgc_date_request',

        ],
        [
            'title' => 'Request Status',
            'dataIndex' => 'payment',
        ],
    ];

    public static $specialInternal = [

        [
            'title' => 'RFSEGC #',
            'dataIndex' => 'spexgc_num',

        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'spexgc_datereq',

        ],
        [
            'title' => 'Date Needed',
            'dataIndex' => 'spexgc_dateneed',

        ],
        [
            'title' => 'Total Denom',
            'key' => 'denom',
        ],
        [
            'title' => 'Customer',
            'key' => 'customer',

        ],
        [
            'title' => 'Requested By',
            'dataIndex' => 'user',
        ],
        [
            'title' => 'Approved By',
            'key' => 'approved',
        ],
        [
            'title' => 'Reviewed By',
            'key' => 'reviewed',
        ],
        [
            'title' => 'Action',
            'key' => 'action',
        ],
    ];

    public static $specialReleasedGc = [
        [
            'title' => 'RFSEGC #',
            'dataIndex' => 'spexgc_num',

        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'spexgc_datereq',

        ],
        [
            'title' => 'Date Validity',
            'dataIndex' => 'spexgc_dateneed',

        ],
        [
            'title' => 'Requested By',
            'key' => 'requestedBy',
        ],
        [
            'title' => 'Customer',
            'key' => 'customer',

        ],
        [
            'title' => 'Date Released',
            'key' => 'dateReleased',
        ],
        [
            'title' => 'Released By',
            'key' => 'releasedBy',
        ],
        [
            'title' => 'Actions',
            'key' => 'action',
        ],
    ];

    public static $reviewedSpecialExternal = [
        [
            'title' => 'RFSEGC #',
            'dataIndex' => 'spexgc_num',

        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'spexgc_datereq',

        ],
        [
            'title' => 'Date Needed',
            'dataIndex' => 'spexgc_dateneed',

        ],
        [
            'title' => 'Total Denom',
            'key' => 'totalDenom',
        ],
        [
            'title' => 'Customer',
            'key' => 'customer',

        ],
        [
            'title' => 'Requested By',
            'key' => 'requestedBy',
        ],
        [
            'title' => 'Approved By',
            'key' => 'approvedBy',
        ],
        [
            'title' => 'Reviewed By',
            'key' => 'reviewedBy',
        ],
        [
            'title' => 'Actions',
            'key' => 'action',
        ],
    ];

    public static $approvedRequest = [
        [
            'title' => 'RFSEGC #',
            'dataIndex' => 'spexgc_num',

        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'spexgc_datereq',

        ],
        [
            'title' => 'Date Validity',
            'dataIndex' => 'spexgc_dateneed',

        ],
        [
            'title' => 'Customer',
            'key' => 'customer',

        ],
        [
            'title' => 'Date Approved',
            'key' => 'dateApproved',
        ],
        [
            'title' => 'Approved By',
            'key' => 'approvedBy',
        ],
        [
            'title' => 'Actions',
            'key' => 'action',
        ],
    ];
    public static $allocationAdjustment = [
        [
            'title' => 'Location Adjusted',
            'key' => 'store',

        ],
        [
            'title' => 'Gc Type',
            'key' => 'gctype',

        ],
        [
            'title' => 'Adjustment Type',
            'key' => 'adjustType',

        ],
        [
            'title' => 'Date',
            'dataIndex' => 'aadj_datetime',

        ],
        [
            'title' => 'Remarks',
            'dataIndex' => 'aadj_remark',
        ],
        [
            'title' => 'Adjusted By',
            'key' => 'user',
        ],
        [
            'title' => 'Actions',
            'key' => 'action',
        ],
    ];

    public static $cancelledProductionRequest = [
        [
            'title' => 'PR No.',
            'key' => 'pr',

        ],
        [
            'title' => 'Date Requested',
            'key' => 'dateRequested',

        ],
        [
            'title' => 'Date Needed',
            'key' => 'dateNedded',

        ],
        [
            'title' => 'Prepared By',
            'key' => 'preparedBy',

        ],
        [
            'title' => 'Date Cancelled',
            'dataIndex' => 'cpr_at',
        ],
        [
            'title' => 'Cancelled By',
            'key' => 'cancelledBy',
        ],
        [
            'title' => 'Actions',
            'key' => 'action',
        ],
    ];

    public static $customerSetup = [
        [
            'title' => 'Customer name',
            'dataIndex' => 'ins_name',

        ],
        [
            'title' => 'Customer Type',
            'dataIndex' => 'ins_custype',

        ],
        [
            'title' => 'Gc Type',
            'key' => 'gcType',

        ],
        [
            'title' => 'Date Created',
            'dataIndex' => 'ins_date_created',

        ],
        [
            'title' => 'Created By',
            'key' => 'createdBy',
        ],
    ];

    public static $specialExternalSetup = [
        [
            'title' => 'Company name/ Person',
            'dataIndex' => 'spcus_companyname',

        ],
        [
            'title' => 'Account Name',
            'dataIndex' => 'spcus_acctname',

        ],
        [
            'title' => 'Address',
            'dataIndex' => 'spcus_address',
            'ellipsis' => true,

        ],
        [
            'title' => 'Contact Person',
            'dataIndex' => 'spcus_cperson',

        ],
        [
            'title' => 'Contact Number',
            'dataIndex' => 'spcus_cnumber',
        ],
        [
            'title' => 'Created By',
            'key' => 'createdBy',
        ],
        [
            'title' => 'Date Created',
            'dataIndex' => 'spcus_at',
        ],
    ];
}
