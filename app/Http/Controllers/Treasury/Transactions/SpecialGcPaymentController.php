<?php

namespace App\Http\Controllers\Treasury\Transactions;

use App\Helpers\NumberHelper;
use App\Http\Controllers\Controller;
use App\Models\SpecialExternalGcrequest;

class SpecialGcPaymentController extends Controller
{
    public function specialExternalPayment()
    {
        $transactionNumber = SpecialExternalGcrequest::max('spexgc_num');

        return inertia('Treasury/Transactions/SpecialGcPayment/SpecialExtPayment', [
            'title' => 'Special External GC Payment',
            'trans' => $transactionNumber ? NumberHelper::leadingZero($transactionNumber + 1, "%03d") : '0001'
        ]);
    }
}
