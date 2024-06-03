<?php

namespace App\Helpers;

class ColumnHelper
{
    public static $budget_ledger_columns = [
        [
            'title' => 'Legder No.',
            'dataIndex' => 'bledger_no',
            // 'width' => '10%',
        ],
        [
            'title' => 'Date.',
            'dataIndex' => 'bledger_datetime',
            // 'width' => '10%',
        ],
        [
            'title' => 'Transaction.',
            'dataIndex' => 'bledger_type',
            // 'width' => '10%',
        ],
        [
            'title' => 'Debit.',
            'dataIndex' => 'bdebit_amt',
            // 'width' => '10%',
        ],
        [
            'title' => 'Credit.',
            'dataIndex' => 'bcredit_amt',
            // 'width' => '10%',
        ],
        [
            'title' => 'Info.',
            'key' => 'info',
            // 'width' => '10%',
        ],
    ];
}
