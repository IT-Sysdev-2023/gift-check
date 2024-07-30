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
