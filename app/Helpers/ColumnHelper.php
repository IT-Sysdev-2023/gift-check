<?php

namespace App\Helpers;

class ColumnHelper
{
    public static $ledger_columns = [
        [
            'title' => 'Legder No.',
            'dataIndex' => 'ledgerNo',
            'key' => 'ledger'

        ],
        [
            'title' => 'Date.',
            'dataIndex' => 'date',

        ],
        [
            'title' => 'Transaction.',
            'dataIndex' => 'transaction',

        ],
        [
            'title' => 'Debit.',
            'dataIndex' => 'debit',

        ],
        [
            'title' => 'Credit.',
            'dataIndex' => 'credit',

        ],
        [
            'title' => 'Info.',
            'key' => 'info',

        ],
    ];
}
