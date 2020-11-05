<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'user_id'=> 'required|integer',
            'name'   => 'required|string',
            'price'  => 'required|numeric',
            'weight' => 'required|numeric',
            'image'  => 'nullable|file'
        ];
    }

    public function messages(){
        return [
            'user_id.required' => 'O campo condominium_id é requerido.',
            'name.required' => 'O campo period é requerido.',
            'price.required' => 'O campo account_year_id é requerido.',
            'weight.required' => 'O campo date_limit é requerido.',
            'image.required' => 'O campo date_approve é requerido.',
        ];
    }
}
