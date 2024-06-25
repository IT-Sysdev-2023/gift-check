<?php

namespace App\Services\Treasury;

class ColumnHelper
{
    public static function approved_buget_request(){
        $ob1 = ['abr' => 'approved_at'];
        $columns= [
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
                'dataIndex' => 'prepared_by',
                'key' => 'prepared_by',
    
            ],
            [
                'title' => 'Date Approved',
                'dataIndex' => ['abr']['approved_at'],
    
            ],
            [
                'title' => 'Approved By',
                'dataIndex' => 'abr',
                'key' => 'abr',
    
            ],
            [
                'title' => 'Actions',
                'dataIndex' => 'action'
    
            ],
        ];

        return response()->json($columns);
    }
    
       
}