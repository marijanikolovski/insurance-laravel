<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsuranceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'age_id' => 'required|integer|exists:ages,id',
            'city_id' => 'required|integer|exists:cities,id',
            'vehicle_power' => 'required|numeric',
            'voucher' => 'numeric',
            'price_match' => 'numeric',
            'discount_id' => 'array|exists:discounts,id',
            'coverage_id' => 'array|exists:coverages,id'
        ];
    }
}
