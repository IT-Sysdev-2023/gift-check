<?php

namespace App\Exports\VerifiedGCReportYearly;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;

class verifiedReportYearly implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */


    protected $verifiedYearly;

    public function __construct($request){
        $this->verifiedYearly = $request;
    }
    public function collection()
    {
        return User::all();
    }
}
