<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CoverageRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'value' => 'integer',
            'value_user_over30' => 'integer',
            'description' => 'required|string|max:255',
        ];
    }
}
