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
            'dataIndex' => ['cancelled_by', 'approved_by'],
            'key' => 'approved_by',

        ],
    ];

    public static $pendingStoreGcRequest = [
        [
            'title' => 'Gc Request No.',
            'dataIndex' => 'sgc_num',

        ],
        [
            'title' => 'Requested By',
            // 'dataIndex' => ['store', 'store_name'],
            // 'key' => 'requested_by',

        ],
        [
            'title' => 'Date Needed',
            'dataIndex' => 'sgc_date_needed',

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
    ];

    public static $releasedStoreGcRequest = [
        [
            'title' => 'Released No.',
            'dataIndex' => 'agcr_request_relnum',

        ],
        [
            'title' => 'Date Requested',
            'dataIndex' => ['storeGcRequest','sgc_date_request'],
            'key' => 'date_requested',

        ],
        [
            'title' => 'Retail Store',
            'dataIndex' =>  ['storeGcRequest','store', 'store_name'],
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
            'dataIndex' =>  ['store', 'store_name'],
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
            'dataIndex' =>  'pe_date_needed',

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
            'dataIndex' =>  'spexgc_dateneed',

        ],
        [
            'title' => 'Total Denomination',
            'dataIndex' => 'specialExternalGcrequestItems',

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
            'dataIndex' =>  'spexgc_dateneed',

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
}