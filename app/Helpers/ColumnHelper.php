<?php

namespace App\Helpers;

class ColumnHelper
{
    public static $budget_ledger_columns = [
        [
            'title' => 'Legder No.',
            'dataIndex' => 'bledger_no',
         
        ],
        [
            'title' => 'Date.',
            'dataIndex' => 'bledger_datetime',

        ],
        [
            'title' => 'Transaction.',
            'dataIndex' => 'transactionType',

        ],
        [
            'title' => 'Debit.',
            'dataIndex' => 'bdebit_amt',

        ],
        [
            'title' => 'Credit.',
            'dataIndex' => 'bcredit_amt',

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
            'dataIndex' => 'bledger_type',

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
