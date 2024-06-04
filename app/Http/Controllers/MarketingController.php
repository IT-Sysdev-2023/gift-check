<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class MarketingController extends Controller
{
    //
    public function promoList()
    {

        return Inertia::render('Marketing/PromoList');
    }

    public function addnewpromo(){
        return Inertia::render('Marketing/AddNewPromo');
    }

    public function promogcrequest(){
        return Inertia::render('Marketing/PromoGcRequest');
    }

    public function releasedpromogc(){
        return Inertia::render('Marketing/ReleasedPromoGc');
    }
    public function promoStatus(){
        return Inertia::render('Marketing/PromoStatus');
    }
}
