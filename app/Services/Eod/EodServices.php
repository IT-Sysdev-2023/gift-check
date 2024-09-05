<?php

namespace App\Services\Eod;

use App\Models\Store;
use App\Models\StoreEod;
use App\Models\StoreEodItem;
use App\Models\StoreEodTextfileTransaction;
use App\Models\StoreVerification;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Sodium\compare;

class EodServices
{

    public function getVerifiedFromStore()
    {

        $eod = StoreVerification::selectFilter()->join('users', 'user_id', '=', 'vs_by')
            ->join('customers', 'cus_id', '=', 'vs_cn')
            ->join('stores', 'store_id', '=', 'vs_store')
            ->join('gc_type', 'gc_type_id', '=', 'vs_gctype')
            ->where('vs_tf_used', '')
            ->where('vs_tf_eod', '')
            ->where(function ($q) {
                $q->whereDate('vs_reverifydate', today())
                    ->orWhereDate('vs_date', '<=', today());
            })
            ->orderByDesc('vs_id')
            ->paginate(10)->withQueryString();

        $eod->transform(function ($item) {
            $item->dateFmatted = Date::parse($item->vs_date)->toFormattedDateString();
            $item->fullname = $item->firstname . ' ' . $item->lastname;
            return $item;
        });


        return $eod;
    }

    public function processEod($request, $id)
    {

        // $wholesaletime = date("H:i", strtotime(now()));

        $wholesaletime = now()->format('H:i');

        $wholesaletime = str_replace(":", "", $wholesaletime);

        // dd(Date::parse()->format('H:i'))

        $user = 'IT';
        $password = 'itsysdev';

        exec('net use \\\172.16.43.7\Gift\\\\ /user:' . $user . ' ' . $password . ' /persistent:no');


        $store = StoreVerification::select(
            'username',
            'vs_tf',
            'vs_barcode',
            'vs_tf_denomination',
            'vs_tf_balance',
            'vs_tf_used',
            'vs_tf_eod',
            'vs_tf_eod2',
            'vs_store',
            'vs_payto'
        )->join('users', 'user_id', '=', 'vs_by')->where('vs_tf_used', '')
            ->where('vs_tf_eod', '')
            ->where(function ($q) {
                $q->whereDate('vs_reverifydate', today())
                    ->orWhereDate('vs_date', '<=', today());
            })
            ->orderByDesc('vs_id')
            ->get();

        if (!$store) {
        } else {

            if ($store->count() == 0) {
                return back()->with([
                    'msg' => 'No TextFile Exists'
                ]);
            }

            $ip = '\\\172.16.43.7\Gift\\';

            $quickCheck = collect(File::files($ip));

            // dd($quickCheck->toArray());

            $txtfiles_temp = collect();

            $notFoundGC = [];

            $store->each(function ($item) use ($quickCheck, $ip, &$txtfiles_temp, &$notFoundGC) {

                $res = $quickCheck->contains(function ($value, int $key) use ($item) {
                    return $value->getFilename() == $item->vs_tf;
                });

                if ($res) {

                    $txtfiles_temp[] =  [
                        'positive' => true,
                        'ver_barcode'        => $item->vs_barcode,
                        'ver_textfilename'     => $item->vs_tf,
                        'ver_denom'         => $item->vs_tf_denomination,
                        'ver_balance'         => $item->vs_tf_balance,
                        'ver_used'            => $item->vs_tf_used,
                        'ver_eod1'            => $item->vs_tf_eod,
                        'txtfile_ip'        => $ip,
                        'payto'                => $item->vs_payto
                    ];
                } else {

                    if ($item->vs_payto == 'WHOLESALE') {
                        $txtfiles_temp[] =  [
                            'ver_barcode'        => $item->vs_barcode,
                            'ver_textfilename'     => $item->vs_tf,
                            'ver_denom'         => $item->vs_tf_denomination,
                            'ver_balance'         => $item->vs_tf_balance,
                            'ver_used'            => $item->vs_tf_used,
                            'ver_eod1'            => $item->vs_tf_eod,
                            'txtfile_ip'        => $ip,
                            'payto'                => $item->vs_payto
                        ];
                    } else {

                        $notFoundGC[] = $item->vs_tf;
                    }
                }
            });

            $rss = [];



            $txtfiles_temp->each(function ($item) use ($id, $wholesaletime, &$rss) {

                if ($item['payto'] == 'WHOLESALE') {
                    // DB::transaction(function () use ($item, $id,  $wholesaletime) {

                    //     StoreVerification::where('vs_barcode', $item['ver_barcode'])->update([
                    //         'vs_tf_used' => '*',
                    //         'vs_tf_balance' => '0',
                    //         'vs_tf_purchasecredit' => $item['ver_denom'],
                    //         'vs_tf_eod' => '1',
                    //     ]);

                    //     StoreEodTextfileTransaction::create([
                    //         'seodtt_eod_id' => $id,
                    //         'seodtt_barcode' => $item['ver_barcode'],
                    //         'seodtt_line' => '000',
                    //         'seodtt_creditlimit' => $item['ver_denom'],
                    //         'seodtt_credpuramt' => $item['ver_denom'],
                    //         'seodtt_addonamt' => '0',
                    //         'seodtt_balance' => '0',
                    //         'seodtt_transno' => '0',
                    //         'seodtt_timetrnx' => $wholesaletime,
                    //         'seodtt_bu' => '',
                    //         'seodtt_terminalno' => 'WHOLESALE',
                    //         'seodtt_ackslipno' => '0',
                    //         'seodtt_crditpurchaseamt' => $item['ver_denom'],
                    //     ]);

                    //     StoreEodItem::create([
                    //         'st_eod_barcode' => $item['ver_barcode'],
                    //         'st_eod_trid' => $id,
                    //     ]);
                    // });
                    $rss[] = [
                        's' => 's',
                        'status' => 200
                    ];
                } else {

                    $file = $item['txtfile_ip'] . '\\' . $item['ver_textfilename'];

                    dd($file);

                    if (!File::exists($file)) {
                        $rss[] = [
                            'error' => 'bugok',
                            'status' => 400
                        ];
                    }

                    $temp = explode(".", $file);
                    $extension = end($temp);

                    $allowedExts = collect([
                        'igc',
                        'egc',
                    ]);

                    if (!$allowedExts->contains($extension)) {

                        $rss[] = [
                            'error' => 'bugok',
                            'status' => 400
                        ];
                    }

                    $text = File::get($file);

                    $exp = explode("\n", $text);

                    $pc = '';
                    $am = '';
                    $used = false;
                    $amount = 0;

                    // dd($exp);

                    $exprn = [];

                    foreach ($exp as $key => $line) {


                        dump($line);

                        $exprn[] = explode(",", $line);

                        if ($key == 2) {
                            $pc = $exprn[1][1];
                        }
                        if ($key == 3) {

                            $am = $exprn[1][1];
                        }
                        if ($key == 4) {
                            $amount = $exprn[1][1];

                            if ($amount < $item['ver_denom']) {
                                $used = true;

                            }
                        }

                        // dd($key > 7);

                        if ($key > 7) {

                            if ($key !== '') {
                                // $this->storeEodTransaction($item, $exprn, $id, $wholesaletime);
                            }
                        }
                    }

                    if ($used) {



                        // $query_update_used = $link->query(
                        //     "UPDATE
                        //         store_verification
                        //     SET
                        //         vs_tf_used='*',
                        //          vs_tf_balance='$rem_amt',
                        //          vs_tf_purchasecredit='$pc',
                        //         vs_tf_addon_amt='$am'
                        //     WHERE
                        //         vs_barcode='".$value['ver_barcode']."'

                        // ");

                        // if(!$query_update_used)
                        // {
                        //     $errquery = true;
                        //     $msg = $link->error;
                        //     break;
                        // }
                    }



                    //  if($exprn[1][0]==2){
                    //     dd();
                    //  }

                    // dd($exprn);
                }
            });
            dd();


            //    return $rss;
        }
    }
    private function storeEodTransaction($item, $exprn, $id)
    {
        dd($exprn);
        // StoreEodTextfileTransaction::create([
        //     'seodtt_eod_id' => $id,
        //     'seodtt_barcode' => $item['ver_barcode'],
        //     'seodtt_line' => $item,
        //     'seodtt_creditlimit' => $item['ver_denom'],
        //     'seodtt_credpuramt' => $item['ver_denom'],
        //     'seodtt_addonamt' => '0',
        //     'seodtt_balance' => '0',
        //     'seodtt_transno' => '0',
        //     'seodtt_timetrnx' => '0',
        //     'seodtt_bu' => '',
        //     'seodtt_terminalno' => 'WHOLESALE',
        //     'seodtt_ackslipno' => '0',
        //     'seodtt_crditpurchaseamt' => $item['ver_denom'],
        // ]);
    }
}
