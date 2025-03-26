<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignatory extends Model
{
    use HasFactory;

    protected $primaryKey = 'assig_id';

    public static function assignatories($request){
        return self::select('assig_position', 'assig_name as label', 'assig_id as value')
            ->where(function ($q) use ($request) {
            $q->where('assig_dept', $request->user()->usertype);
        })->get();
    }
}
