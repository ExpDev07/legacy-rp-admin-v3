<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BanStoreRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reason' => ['string', 'nullable'],
            'expire' => ['nullable', 'integer']
        ];
    }

}
