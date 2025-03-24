<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromoForApprovalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return true;
    // }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'appby' => 'required',
            'checkby' => 'required',
            'status' => 'required',
            'remarks' => 'required',
            'reqid' => 'nullable',
            'docs' => 'nullable',

        ];
    }

    public function messages(): array
    {
        return [
            'appby.required' => 'Approved By is required',
            'checkby.required' => 'Checked By is required',
            'status.required' => 'Status is required',
            'remarks.required' => 'Remarks is required',
            'docs.image' => 'Docs should be a type of image',
            'docs.max' => 'The image is too big!',
        ];
    }
}
