<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServerStoreRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'url'  => ['required', 'url'],
            'name' => ['required', 'string'],
        ];
    }

}
