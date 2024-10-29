<?php

namespace App\Traits\Iad;

use App\Helpers\NumberHelper;
use App\Models\Gc;
use App\Models\GcRelease;
use App\Models\InstitutTransactionsItem;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\DB;

trait AuditTraits
{

    public function dataTraits($request, $date)
    {

        [$addedGiftCheck, $beginningbal, $gcrelease, $unusedgc] = Concurrency::run([

            fn() =>  Gc::select('barcode_no', 'gc.denom_id', 'denomination.denom_id', 'denomination')
                ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
                ->where('gc_validated', '')
                ->where('gc_allocated', '')
                ->where('gc_ispromo', '')
                ->where('gc_treasury_release', '')
                ->where('pe_entry_gc', '>=', '14')
                ->when($date !== [], function ($q) use ($date) {
                    $q->whereBetween('date', $date);
                })
                ->get()
                ->groupBy('denom_id'),

            fn() => Gc::select('barcode_no', 'gc.denom_id', 'denomination.denom_id', 'denomination')
                ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
                ->join('custodian_srr_items as items', 'items.cssitem_barcode', '=', 'gc.barcode_no')
                ->join('custodian_srr as srr', 'srr.csrr_id', '=', 'items.cssitem_recnum')
                ->where('gc.gc_validated', '*')
                ->where('gc_allocated', '')
                ->where('gc_ispromo', '')
                ->where('gc_treasury_release', '')
                ->where('pe_entry_gc', '>=', '14')
                ->get()
                ->groupBy('denom_id'),

            fn() => GcRelease::select(
                DB::raw("COUNT(re_barcode_no) as count"),
                DB::raw("SUBSTRING(re_barcode_no, 1, 3) as barcode"),
                DB::raw("MAX(denomination.denomination) as denom"),
            )
                ->join('gc', 'gc.barcode_no', '=', 'gc_release.re_barcode_no')
                ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
                ->when($date !== [], function ($q) use ($date) {
                    $q->whereBetween('gc_release.rel_date', $date);
                })
                ->where('gc.gc_validated', '*')
                ->where('gc.gc_allocated', '*')
                ->groupBy(DB::raw("SUBSTRING(gc_release.re_barcode_no, 1, 3)"))
                ->union(
                    InstitutTransactionsItem::select(
                        DB::raw("COUNT(instituttritems_barcode) as count"),
                        DB::raw("SUBSTRING(instituttritems_barcode, 1, 3) as barcode"),
                        DB::raw("MAX(denomination.denomination) as denom"),
                    )
                        ->join('institut_transactions as trans', 'trans.institutr_id', '=', 'institut_transactions_items.instituttritems_trid')
                        ->join('gc', 'gc.barcode_no', '=', 'institut_transactions_items.instituttritems_barcode')
                        ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
                        ->when($date !== [], function ($q) use ($date) {
                            $q->whereBetween('institutr_date', $date);
                        })
                        ->where('gc.gc_validated', '*')
                        ->where('gc.gc_treasury_release', '*')
                        ->groupBy(DB::raw("SUBSTRING(instituttritems_barcode, 1, 3)"))
                )
                ->get(),

            fn() => Gc::select('barcode_no', 'gc.denom_id', 'denomination.denom_id', 'denomination')
                ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
                ->join('custodian_srr_items as items', 'items.cssitem_barcode', '=', 'gc.barcode_no')
                ->join('custodian_srr as srr', 'srr.csrr_id', '=', 'items.cssitem_recnum')
                ->where('gc.gc_validated', '*')
                ->where('gc_allocated', '')
                ->where('gc_ispromo', '')
                ->where('gc_treasury_release', '')
                ->where('csrr_id', '>=', '14')
                ->get()
                ->groupBy('denom_id'),

        ]);


        return (object) [
            'addedgc' => $addedGiftCheck,
            'begbal' => $beginningbal,
            'gcrelease' => $gcrelease,
            'unusedgc' => $unusedgc
        ];
    }

    private function getBarcodes($item, $date)
    {
        if (!empty($date)) {

            $threeD = substr($item->barcode, 0, 3) ?? null;

            $regular = DB::table('gc_release')
                ->distinct()
                ->select('re_barcode_no as barcode')
                ->join('gc', 'gc.barcode_no', '=', 'gc_release.re_barcode_no')
                ->whereBetween('gc_release.rel_date', $date)
                ->where('gc.gc_validated', '*')
                ->where('gc.gc_allocated', '*')
                ->where(DB::raw('LEFT(re_barcode_no, 3)'), $threeD);


            $institution = DB::table('institut_transactions_items')
                ->distinct()
                ->select('instituttritems_barcode as end')
                ->join('institut_transactions as trans', 'trans.institutr_id', '=', 'institut_transactions_items.instituttritems_trid')
                ->join('gc', 'gc.barcode_no', '=', 'institut_transactions_items.instituttritems_barcode')
                ->whereBetween(DB::raw('DATE(trans.institutr_date)'), $date)
                ->where('gc.gc_validated', '*')
                ->where('gc.gc_treasury_release', '*')
                ->where(DB::raw('LEFT(instituttritems_barcode, 3)'), $threeD);

            return $regular->union($institution) ?? null;
        }
    }
}
