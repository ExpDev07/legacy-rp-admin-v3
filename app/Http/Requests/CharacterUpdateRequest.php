<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CharacterUpdateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => ['string'],
            'last_name' => ['string'],
            'backstory' => ['string'],
            'job_name' => ['nullable', 'string'],
            'job_department' => ['nullable', 'string'],
            'job_position' => ['nullable', 'string'],
        ];
    }

}
