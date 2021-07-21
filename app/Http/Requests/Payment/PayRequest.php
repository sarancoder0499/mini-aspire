<?php

namespace App\Http\Requests\Payment;

use App\Http\Requests\BaseRequest;

class PayRequest extends BaseRequest
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
            'loan' => 'required|numeric|gt:0',
        ];
    }
}
