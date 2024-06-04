<?php

namespace App\Helpers;

class ColumnHelper
{
    public static $budget_ledger_columns = [
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
    public static $spgc_ledger_columns = [
        [
            'title' => 'Legder No.',
            'dataIndex' => 'spgcledger_no',

        ],
        [
            'title' => 'Date.',
            'dataIndex' => 'spgcledger_datetime',

        ],
        [
            'title' => 'Transaction.',
            'dataIndex' => 'transactionType',

        ],
        [
            'title' => 'Debit.',
            'dataIndex' => 'spgcledger_debit',

        ],
        [
            'title' => 'Credit.',
            'dataIndex' => 'spgcledger_credit',

        ],
        [
            'title' => 'Info.',
            'key' => 'info',

        ],
    ];
}
