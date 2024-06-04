<?php

namespace App\Http\Controllers;

use App\Models\StoreVerification;
use Illuminate\Http\Request;
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
        $data = StoreVerification::leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->leftJoin('users as revby', 'revby.user_id', '=', 'store_verification.vs_reverifyby')
            ->leftJoin('users as verby', 'verby.user_id', '=', 'store_verification.vs_by')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->select(
                'store_verification.vs_barcode',
                'store_verification.vs_tf_denomination',
                'customers.cus_lname as customersLastname',
                'customers.cus_fname as customersFirstname',
                'verby.firstname as verbyFirstname',
                'verby.lastname as verbyLastname',
                'revby.firstname as revbyFirstname',
                'revby.lastname as revbyLastname',
                'store_verification.vs_tf_used',
                'store_verification.vs_tf_balance',
                'store_verification.vs_date',
                'store_verification.vs_time',
                'store_verification.vs_reverifydate'
            )
            ->paginate(10)
            ->withQueryString();
        return Inertia::render('Marketing/VerifiedGCperStore/AlturasMall', [
            'data' => $data,
        ]);
    }
    public function verifiedGc_A_talibon()
    {
        return Inertia::render('Marketing/VerifiedGCperStore/AlturasTalibon');
    }
    public function verifiedGc_A_tubigon()
    {
        return Inertia::render('Marketing/VerifiedGCperStore/Alturastubigon');
    }











    public function verifiedGc_AltaCita()
    {

        return Inertia::render('Marketing/VerifiedGCperStore/AltaCita');
    }














    public function verifiedGc_AscTech()
    {
        return Inertia::render('Marketing/VerifiedGCperStore/AscTech');
    }
    public function verifiedGc_colonadeColon()
    {
        return Inertia::render('Marketing/VerifiedGCperStore/Colonade_colon');
    }
    public function verifiedGc_colonadeMandaue()
    {
        return Inertia::render('Marketing/VerifiedGCperStore/ColonadeMandaue');
    }
    public function verifiedGc_plazaMarcela()
    {

        return Inertia::render('Marketing/VerifiedGCperStore/PlazaMarcela');
    }
    public function verifiedGc_farmersMarket()
    {
        return Inertia::render('Marketing/VerifiedGCperStore/FarmersMarket');
    }
    public function verifiedGc_udc()
    {
        return Inertia::render('Marketing/VerifiedGCperStore/UbayDistributionCenter');
    }
    public function verifiedGc_screenville()
    {
        return Inertia::render('Marketing/VerifiedGCperStore/ScreenVille');
    }
    public function verifiedGc_icm()
    {

        return Inertia::render('Marketing/VerifiedGCperStore/IslandCityMall');
    }
}
