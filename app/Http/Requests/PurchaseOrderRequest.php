<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "req_no" => "required",
            "sup_name" => "required",
            "mop" => "required",
            "rec_no" => "required",
            "trans_date" => "required",
            "ref_no" => "required",
            "po_no" => "required",
            "pay_terms" => "required",
            "loc_code" => "required",
            "ref_po_no" => "required",
            "dep_code" => "required",
            "remarks" => "required",
            "prep_by" => "required",
            "check_by" => "required",
            "srr_type" => "required",
            "denom" => 'required',
            "pur_date" => "required",
        ];
    }
}
