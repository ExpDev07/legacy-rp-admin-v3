<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'gender' => [Rule::in(['male', 'female'])],
            'backstory' => ['string'],
            'job_name' => ['nullable', 'string'],
            'department_name' => ['nullable', 'string'],
            'position_name' => ['nullable', 'string'],
        ];
    }

}
