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
            'dataIndex' => 'items_calculation',

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
            'dataIndex' => $column
        ];
    }
}
