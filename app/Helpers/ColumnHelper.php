<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class ColumnHelper
{
    public static $budget_ledger_columns = [
        [
            'title' => 'Legder No.',
            'dataIndex' => 'ledger_no',
            'key' => 'ledger'

        ],
        [
            'title' => 'Date',
            'dataIndex' => 'date',

        ],
        [
            'title' => 'Transaction',
            'dataIndex' => 'transaction',

        ],
        [
            'title' => 'Debit',
            'dataIndex' => 'debit',

        ],
        [
            'title' => 'Credit',
            'dataIndex' => 'credit',

        ],
        [
            'title' => 'Info.',
            'key' => 'info',

        ],
    ];

    public static $eod_columns = [
        [
            'title' => 'Barcode ',
            'dataIndex' => 'vs_barcode',

        ],
        [
            'title' => 'Denomination',
            'dataIndex' => 'vs_tf_denomination',

        ],
        [
            'title' => 'Store',
            'dataIndex' => 'store_name',

        ],
        [
            'title' => 'Gc Type',
            'dataIndex' => 'gctype',

        ],
        [
            'title' => 'Date',
            'dataIndex' => 'vs_date',

        ],
        [
            'title' => 'Time',
            'dataIndex' => 'vs_time',

        ],
        [
            'title' => 'Verify by',
            'dataIndex' => 'fullname',

        ],
        [
            'title' => 'Customer',
            'dataIndex' => 'cus_fname',

        ],
        [
            'title' => 'Status',
            'key' => 'status',

        ],
    ];
    public static $get_denom_columns = [
        [
            'title' => 'Denomination',
            'dataIndex' => 'denomination',

        ],
        [
            'title' => 'Scanned Gc',
            'dataIndex' => 'countDen',
            'key' => 'denom_id'

        ],
    ];
    public static $approved_request_columns = [
        [
            'title' => 'RFPROM',
            'dataIndex' => 'pgcreq_reqnum',

        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'reqdate',

        ],
        [
            'title' => 'Date Needed',
            'dataIndex' => 'dateneed',

        ],
        [
            'title' => 'Total Gc',
            'dataIndex' => 'pgcreq_total',

        ],
        [
            'title' => 'Retail Group',
            'dataIndex' => 'pgcreq_group',

        ],
        [
            'title' => 'Requested By',
            'dataIndex' => 'reqby',

        ],
        [
            'title' => 'Recommended By',
            'dataIndex' => 'recby',

        ],
        [
            'title' => 'Approved By',
            'dataIndex' => 'appby',

        ],
        [
            'title' => 'Action',
            'key' => 'view',
            'align' =>  'center'

        ],
    ];
    public static $cancelled_production_columns = [
        [
            'title' => 'Pr No',
            'dataIndex' => 'pe_num',

        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'req_date',

        ],
        [
            'title' => 'Prepared By',
            'dataIndex' => 'prepby',

        ],
        [
            'title' => 'Date Cancelled',
            'dataIndex' => 'can_at',

        ],
        [
            'title' => 'Cancelled By',
            'dataIndex' => 'canby',

        ],
        [
            'title' => 'Action',
            'key' => 'view',
            'align' => 'center',

        ],
    ];
    public static $approved_details_column = [

        [
            'title' => 'Denomination',
            'dataIndex' => 'denomination',
            'key' => 'denom_id'

        ],
        [
            'title' => 'Quantity',
            'dataIndex' => 'pe_items_quantity',
            'key' => 'denom_id'

        ],
        [
            'title' => 'Unit',
            'dataIndex' => 'uom',
            'key' => 'denom_id'

        ],
        [
            'title' => 'Barcode Start',
            'dataIndex' => 'bstart',
            'key' => 'denom_id'

        ],
        [
            'title' => 'Barcode End',
            'dataIndex' => 'bend',
            'key' => 'denom_id'

        ],
        [
            'title' => 'Subtotal',
            'dataIndex' => 'fsubt',
            'key' => 'denom_id'

        ],
    ];
    public static $production_approved_column = [
        [
            'title' => 'Pr No.',
            'dataIndex' => 'pe_num',

        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'pe_date_request_tran',

        ],
        [
            'title' => 'Requested By',
            'dataIndex' => 'reqby',

        ],
        [
            'title' => 'Date Approved',
            'dataIndex' => 'ape_approved_at_tran',

        ],
        [
            'title' => 'Approved By',
            'dataIndex' => 'ape_approved_by',

        ],
        [
            'title' => 'Action',
            'key' => 'view',
            'align' => 'center'

        ],
    ];
    public static $payment_viewing_column = [
        [
            'title' => 'Payment No.',
            'dataIndex' => 'insp_paymentnum',

        ],
        [
            'title' => 'Spgc #',
            'dataIndex' => 'insp_trid',

        ],
        [
            'title' => 'Payment Date',
            'dataIndex' => 'institut_date',

        ],
        [
            'title' => 'Customer',
            'dataIndex' => 'spcus_companyname',

        ],
        [
            'title' => 'Amount Paid',
            'dataIndex' => 'institut_amountrec',

        ],
        [
            'title' => 'Status',
            'dataIndex' => 'spexgc_payment_stat',
            'align' => 'center',

        ],
        [
            'title' => 'Action',
            'key' => 'view',
            'align' => 'center',
        ],
    ];
    public static $payment_gc_columns = [
        [
            'title' => 'RFSEGC#',
            'dataIndex' => 'spexgc_num',
            'align' => 'center'

        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'date',

        ],
        [
            'title' => 'Date Validity',
            'dataIndex' => 'validity',

        ],
        [
            'title' => 'Requested By',
            'dataIndex' => 'reqby',

        ],
        [
            'title' => 'Customer',
            'dataIndex' => 'spcus_acctname',

        ],
        [
            'title' => 'Amount',
            'dataIndex' => 'spexgc_balance',

        ],
        [
            'title' => 'Status',
            'key' => 'status',
            'align' => 'center'

        ],
        [
            'title' => 'Setup',
            'key' => 'setup',
            'align' => 'center',

        ],
    ];
    public static $approved_budget_request = [
        [
            'title' => 'Br No',
            'dataIndex' => 'br_no',

        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'requestDate',

        ],
        [
            'title' => 'Budget Requested',
            'dataIndex' => 'budgetReq',

        ],
        [
            'title' => 'Prepared By',
            'dataIndex' => 'fullname',

        ],
        [
            'title' => 'Date Approved',
            'dataIndex' => 'approvedAt',

        ],
        [
            'title' => 'Checked By',
            'dataIndex' => 'checkedby',

        ],
        [
            'title' => 'View',
            'key' => 'viewing',
            'align' => 'center'

        ],
    ];
    public static $received_gc_index_columns = [
        [
            'title' => 'Receiving #',
            'dataIndex' => 'recnumber',

        ],
        [
            'title' => 'Date Received',
            'dataIndex' => 'date',

        ],
        [
            'title' => 'E Requisition #',
            'dataIndex' => 'requisno',

        ],
        [
            'title' => 'Supplier Name',
            'dataIndex' => 'companyname',

        ],
        [
            'title' => 'Received By',
            'dataIndex' => 'fullname',

        ],
        [
            'title' => 'Received Type',
            'dataIndex' => 'csrr_receivetype',

        ],
        [
            'title' => 'Action',
            'key' => 'action',
            'align' => 'center'

        ],
    ];
    public static $review_gc_columns = [
        [
            'title' => 'RFSEGC',
            'dataIndex' => 'spexgc_num',

        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'reqdate',

        ],
        [
            'title' => 'Customer',
            'dataIndex' => 'spcus_companyname',

        ],
        [
            'title' => 'Date Approved',
            'dataIndex' => 'appdate',

        ],
        [
            'title' => 'Approved By',
            'dataIndex' => 'reqap_approvedby',

        ],
        [
            'title' => 'Date Reviewed',
            'dataIndex' => 'revdate',

        ],
        [
            'title' => 'Reviewed By',
            'dataIndex' => 'fullname',

        ],
        [
            'title' => 'Details',
            'key' => 'details',
            'align' => 'center'

        ],
    ];
    public static $retail_group_pending_colums = [
        [
            'title' => 'RFPROM #',
            'dataIndex' => 'pgcreq_reqnum',
        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'req',
        ],
        [
            'title' => 'Date Needed',
            'dataIndex' => 'needed',
        ],
        [
            'title' => 'Total GC',
            'dataIndex' => 'pgcreq_total',
        ],
        [
            'title' => 'Requested by',
            'dataIndex' => 'fullname',
        ],
        [
            'title' => 'Status',
            'key' => 'status',
            'width' => '10%',
            'align' => 'center'
        ],
        [
            'title' => 'Action',
            'key' => 'setup',
            'width' => '12%',
            'align' => 'center'
        ],

    ];
    public static $pending_budget_request_columns = [
        [
            'title' => 'BR No.',
            'dataIndex' => 'br_no',

        ],
        [
            'title' => 'Date Request',
            'dataIndex' => 'req_at',

        ],
        [
            'title' => 'Budget Request',
            'dataIndex' => 'br_request',

        ],
        [
            'title' => 'Date Needed',
            'dataIndex' => 'needed',

        ],
        [
            'title' => 'Prepare By',
            'dataIndex' => 'fullname',

        ],
        [
            'title' => 'Action',
            'key' => 'setup',
            'width' => '20%',
            'align' => 'center'

        ],
    ];

    public static $special_gc_request_holder = [
        [
            'title' => 'RFSEGC',
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
            'dataIndex' => 'total',

        ],
        [
            'title' => 'Customer',
            'dataIndex' => 'spcus_companyname',

        ],
        [
            'title' => 'Requested By',
            'dataIndex' => 'full_name',

        ],
        [
            'title' => 'Action',
            'key' => 'setup',
            'align' => 'center'

        ],
    ];
    public static $denomination_column = [
        [
            'title' => 'Denomination',
            'dataIndex' => 'denomination',

        ],
        [
            'title' => 'Quantity',
            'key' => 'qty',
            'align' => 'center'
        ],
        [
            'title' => 'Validated',
            'key' => 'valid',
            'align' => 'center'
        ],
    ];
    public static $approved_gc_request = [
        [
            'title' => 'Released No.',
            'dataIndex' => 'agcr_request_relnum',
            'width' => '10%',
            'align' => 'center'

        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'spgc_date_request',
            'align' => 'center'
        ],
        [
            'title' => 'Retail Store',
            'dataIndex' => 'storename',
            'align' => 'center'
        ],
        [
            'title' => 'Released by',
            'dataIndex' => 'fullname',
            'align' => 'center'
        ],
        [
            'title' => 'Date Released',
            'dataIndex' => 'agcr_date',
            'align' => 'center'
        ],
        [
            'title' => 'Approved By',
            'dataIndex' => 'valid',
            'align' => 'center'
        ],
        [
            'title' => 'Status',
            'key' => 'status',
            'align' => 'center'
        ],
    ];

    public static $receiving_columns = [
        [
            'title' => 'Fad Rec #',
            'dataIndex' => 'rec_no',

        ],
        [
            'title' => 'E-req #',
            'dataIndex' => 'req_no',

        ],
        [
            'title' => 'Transaction Date',
            'dataIndex' => 'trans_date',

        ],
        [
            'title' => 'Supplier Name',
            'dataIndex' => 'sup_name',

        ],
        [
            'title' => 'P.O #',
            'dataIndex' => 'po_no',

        ],
        [
            'title' => 'Setup',
            'dataIndex' => 'setup',
            'align' => 'center'
        ],
    ];

    public static $barcode_checker_columns = [
        [
            'title' => 'Gc Barcode #',
            'dataIndex' => 'bcheck_barcode',
        ],
        [
            'title' => 'Denomination.',
            'dataIndex' => 'denomination',

        ],
        [
            'title' => 'Date Scanned.',
            'dataIndex' => 'bcheck_date',
            'key' => 'denom_id'

        ],
        [
            'title' => 'Scanned By.',
            'dataIndex' => 'fullname',
        ],
    ];

    public static $gc_ledger_columns = [
        [
            'title' => 'Legder No.',
            'dataIndex' => 'ledger_no',

        ],
        [
            'title' => 'Date',
            'dataIndex' => 'date',

        ],
        [
            'title' => 'Transaction',
            'dataIndex' => 'transaction',

        ],
        [
            'title' => 'Debit',
            'dataIndex' => 'debit',

        ],
        [
            'title' => 'Credit',
            'dataIndex' => 'credit',

        ],
        [
            'title' => 'Posted By.',
            'dataIndex' => 'posted_by'

        ],
    ];

    public static $ver_gc_alturas_mall_columns = [
        [
            'title' => 'Barcode #.',
            'dataIndex' => 'vs_barcode',
        ],
        [
            'title' => 'Denomination.',
            'dataIndex' => 'vs_tf_denomination',
        ],
        [
            'title' => 'Date Verified/Reverified.',
            'dataIndex' => 'vs_date',
            'key' => 'dateVerRev'
        ],
        [
            'title' => 'Verified/Reverified By.',
            'dataIndex' => 'verbyFirstname',
            'key' => 'verby'
        ],
        [
            'title' => 'Customer.',
            'dataIndex' => 'customersLastname',
            'key' => 'customer'
        ],
        [
            'title' => 'Balance.',
            'dataIndex' => 'vs_tf_balance',
        ],
        [
            'title' => 'View.',
            'key' => 'view',
        ],

    ];

    public static $bar_table_columns = [
        [
            'title' => 'Approval',
            'dataIndex' => 'spexgc_num',
        ],
        [
            'title' => 'Date Request.',
            'dataIndex' => 'datereq',
        ],
        [
            'title' => 'Barcode #.',
            'dataIndex' => 'spexgcemp_barcode',
        ],
        [
            'title' => 'Customer.',
            'dataIndex' => 'full_name',
        ],
        [
            'title' => 'Denomination.',
            'dataIndex' => 'spexgcemp_denom',
        ],
        [
            'title' => 'Date Approved.',
            'dataIndex' => 'daterel',
        ],
    ];
    public static $cus_table_columns = [
        [
            'title' => 'Approval.',
            'dataIndex' => 'spexgc_num',
        ],
        [
            'title' => 'Date Request.',
            'dataIndex' => 'datereq',
        ],
        [
            'title' => 'Date Released.',
            'dataIndex' => 'daterel',
        ],
        [
            'title' => 'Account Name.',
            'dataIndex' => 'spcus_acctname',
        ],
        [
            'title' => 'Total Denom.',
            'dataIndex' => 'totdenom',
        ],

    ];

    public static $received_gc_columns = [
        [
            'title' => 'Receiving No.',
            'dataIndex' => 'rec_no',
        ],
        [
            'title' => 'Date Received.',
            'dataIndex' => 'date_rec',
        ],
        [
            'title' => 'E Requisiton No.',
            'dataIndex' => 'e_reqno',
        ],
        [
            'title' => 'Supplier Name.',
            'dataIndex' => 'supname',
        ],
        [
            'title' => 'Received By.',
            'dataIndex' => 'recby',
        ],
        [
            'title' => 'Received Type.',
            'dataIndex' => 'rectype',
        ],

    ];
    public static $purchase_details_columns = [
        [
            'title' => 'Requisiton No.',
            'dataIndex' => 'req_no',
            'align' => 'center'
        ],
        [
            'title' => 'Supplier Name.',
            'dataIndex' => 'sup_name',
        ],
        [
            'title' => 'Mode Of Payment.',
            'dataIndex' => 'mop',
        ],
        [
            'title' => 'Transaction Date.',
            'dataIndex' => 'trans_date',
        ],
        [
            'title' => 'Purchase Date.',
            'dataIndex' => 'pur_date',
        ],
        [
            'title' => 'Srr Type.',
            'dataIndex' => 'srr_type',
        ],
        [
            'title' => 'Pay Terms.',
            'dataIndex' => 'pay_terms',
            'align' => 'center'
        ],
        [
            'title' => 'Action.',
            'key' => 'action',
            'align' => 'center'
        ],


    ];
    public static $approved_gc_column = [
        [
            'title' => 'RFSEGC#',
            'dataIndex' => 'spexgc_num',
        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => 'datereq',
        ],
        [
            'title' => 'Date Validity',
            'dataIndex' => 'dateneeded',
        ],
        [
            'title' => 'Customer',
            'dataIndex' => 'company',
        ],
        [
            'title' => 'Date Approved',
            'dataIndex' => 'reqap_date',
        ],
        [
            'title' => 'Approved By',
            'dataIndex' => 'reqap_approvedby',
        ],
        [
            'title' => 'Action',
            'key' => 'setup',
            'align' => 'center'
        ],

    ];

    public static function app_pend_request_columns($isPending = false)
    {

        return Collection::make([
            [
                'title' => 'RFPROM #.',
                'dataIndex' => 'req_no',
            ],
            [
                'title' => 'Date Requested.',
                'dataIndex' => 'date_req',
            ],
            [
                'title' => 'Date Needed.',
                'dataIndex' => 'date_needed',
            ],
            [
                'title' => 'Total Gc.',
                'dataIndex' => 'total',
            ],
            [
                'title' => 'Recommended By.',
                'dataIndex' => 'approved_by',
            ],
            [
                'title' => 'Requested By.',
                'dataIndex' => 'user',
            ],
            $isPending ? [
                'title' => 'Action.',
                'dataIndex' => 'open',
                'align' => 'center'
            ] : null,
        ])->reject(fn($value) => $value === null)->values();
    }




    public static function getColumns($columns)
    {
        return array_map(function ($item) {
            return [
                'title' => $item['title'] ?? '',
                'dataIndex' => $item['dataIndex'] ?? '',
                'key' => $item['dataIndex'] ?? '',
            ];
        }, $columns);
    }

    public static function arrayHelper($title, $column)
    {
        return [
            'title' => $title,
            'dataIndex' => $column,
        ];
    }

    public static function pendingGcRequest()
    {
        return [
            [
                'title' => 'Request Number',
                'dataIndex' => 'sgc_num',
                'align' => 'center'
            ],
            [
                'title' => 'Date Requested',
                'dataIndex' => 'dateRequest',
                'align' => 'center'
            ],
            [
                'title' => 'Retail Store',
                'dataIndex' => 'store_name',
                'align' => 'center'
            ],
            [
                'title' => 'Requested By',
                'dataIndex' => 'requestedBy',
                'align' => 'center'
            ],
            [
                'title' => 'Date Needed',
                'dataIndex' => 'dateNeeded',
                'align' => 'center'
            ],
            [
                'title' => 'Action',
                'dataIndex' => 'action',
                'align' => 'center'
            ],
        ];
    }

    public static function pendingGcRequestBarcode()
    {
        return [
            [
                'title' => 'Denomination',
                'dataIndex' => 'denomination',
                'align' => 'center'
            ],
            [
                'title' => 'Requested Qty',
                'dataIndex' => 'sri_items_quantity',
                'align' => 'center'
            ]
        ];
    }

    public static function denomCols()
    {
        return [
            [
                'title' => 'Denomination',
                'dataIndex' => 'denomination',
                'align' => 'center'
            ],
            [
                'title' => 'Quantity',
                'dataIndex' => 'qty',
                'align' => 'center'
            ],
        ];
    }
    public static function promopendinglistcols()
    {
        return [
            [
                'title' => 'RFPROM',
                'dataIndex' => 'pgcreq_reqnum',
                'align' => 'center'
            ],
            [
                'title' => 'Date Requested',
                'dataIndex' => 'dateRequested',
                'align' => 'center'
            ],
            [
                'title' => 'Date Needed',
                'dataIndex' => 'dateNeeded',
                'align' => 'center'
            ],
            [
                'title' => 'Total GC',
                'dataIndex' => 'pgcreq_total',
                'align' => 'center'
            ],
            [
                'title' => 'Requested By',
                'dataIndex' => 'RequestedBy',
                'align' => 'center'
            ],
            [
                'title' => 'View',
                'dataIndex' => 'view',
                'align' => 'center'
            ],
        ];
    }
}
