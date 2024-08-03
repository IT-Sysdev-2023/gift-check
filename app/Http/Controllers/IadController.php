<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Services\Iad\IadServices;
use Illuminate\Http\Request;

class IadController extends Controller
{
    public function __construct(public IadServices $iadServices)
    {
    }
    public function index()
    {
        return inertia('Iad/IadDashboard');
    }
    public function receivingIndex()
    {
        return inertia('Iad/GcReceivingIndex', [
            'record' =>  $this->iadServices->gcReceivingIndex(),
            'columns' => ColumnHelper::$receiving_columns,
        ]);
    }
    public function setupReceiving(Request $request)
    {
        $data = $this->iadServices->setupReceivingtxt($request);

        return inertia('Iad/SetupReceiving', [
            'record' => $data->result,
            'denomination' => $this->iadServices->getDenomination($data->denomres),
            'columns' => ColumnHelper::$denomination_column,
        ]);
    }
}
