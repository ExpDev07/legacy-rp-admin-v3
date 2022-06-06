<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlacklistedIdentifierStoreRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'identifier' => ['string', 'required'],
            'reason'     => ['string', 'required'],
            'note'       => ['string', 'required'],
        ];
    }

}
