<?php

namespace App\Helpers;

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

    public static  $ver_gc_alturas_mall_columns = [
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
            'title' => 'No.',
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
    ];
    public static $cus_table_columns = [
        [
            'title' => 'No.',
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
            'title' => 'Trby.',
            'dataIndex' => 'trby',
        ],
        [
            'title' => 'Account Name.',
            'dataIndex' => 'spcus_acctname',
        ],
        [
            'title' => 'Company.',
            'dataIndex' => 'spcus_companyname',
        ],
        [
            'title' => 'Total.',
            'dataIndex' => 'totcnt',
        ],
        [
            'title' => 'Total Denomination.',
            'dataIndex' => 'totdenom',
        ],

    ];

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
