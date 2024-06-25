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
            'title' => 'Prepaired By',
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
}