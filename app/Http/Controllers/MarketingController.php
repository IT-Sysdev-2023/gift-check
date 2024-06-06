<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Helpers\GetVerifiedGc;
use App\Models\StoreVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MarketingController extends Controller
{
    //
    public function promoList()
    {

        return Inertia::render('Marketing/PromoList');
    }

    public function addnewpromo()
    {
        return Inertia::render('Marketing/AddNewPromo');
    }

    public function promogcrequest()
    {
        return Inertia::render('Marketing/PromoGcRequest');
    }

    public function releasedpromogc()
    {
        return Inertia::render('Marketing/ReleasedPromoGc');
    }
    public function promoStatus()
    {
        return Inertia::render('Marketing/PromoStatus');
    }
    public function manageSupplier()
    {
        return Inertia::render('Marketing/ManageSupplier');
    }
    public function treasurySales()
    {
        return Inertia::render('Marketing/Sale_treasurySales');
    }
    public function storeSales()
    {
        return Inertia::render('Marketing/Sale_storeSales');
    }


    public function verifiedGc_Amall()
    {

        $data = GetVerifiedGc::getVerifiedGc(1);
        return Inertia::render('Marketing/VerifiedGCperStore/AlturasMall', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_A_talibon()
    {
        $data = GetVerifiedGc::getVerifiedGc(2);

        return Inertia::render('Marketing/VerifiedGCperStore/AlturasTalibon', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_A_tubigon()
    {

        $data = GetVerifiedGc::getVerifiedGc(0);

        return Inertia::render('Marketing/VerifiedGCperStore/Alturastubigon', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }


    public function verifiedGc_AltaCita()
    {
        $data = GetVerifiedGc::getVerifiedGc(8);
        return Inertia::render('Marketing/VerifiedGCperStore/AltaCita', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }

    public function verifiedGc_AscTech()
    {
        $data = GetVerifiedGc::getVerifiedGc(12);
        return Inertia::render('Marketing/VerifiedGCperStore/AscTech', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_colonadeColon()
    {
        $data = GetVerifiedGc::getVerifiedGc(5);
        return Inertia::render('Marketing/VerifiedGCperStore/Colonade_colon', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_colonadeMandaue()
    {$data = GetVerifiedGc::getVerifiedGc(6);
        return Inertia::render('Marketing/VerifiedGCperStore/ColonadeMandaue', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_plazaMarcela()
    {
        $data = GetVerifiedGc::getVerifiedGc(4);
        return Inertia::render('Marketing/VerifiedGCperStore/PlazaMarcela', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_farmersMarket()
    {
        $data = GetVerifiedGc::getVerifiedGc(10);
        return Inertia::render('Marketing/VerifiedGCperStore/FarmersMarket', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_udc()
    {
        $data = GetVerifiedGc::getVerifiedGc(10);
        return Inertia::render('Marketing/VerifiedGCperStore/UbayDistributionCenter', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_screenville()
    {
        $data = GetVerifiedGc::getVerifiedGc(11);
        return Inertia::render('Marketing/VerifiedGCperStore/ScreenVille', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_icm()
    {
        $data = GetVerifiedGc::getVerifiedGc(3);
        return Inertia::render('Marketing/VerifiedGCperStore/IslandCityMall', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
}
