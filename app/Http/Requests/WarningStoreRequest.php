<?php

namespace App\Http\Requests;

use App\Warning;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WarningStoreRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'message'      => ['required'],
            'warning_type' => ['required', Rule::in([Warning::TypeWarning, Warning::TypeNote, Warning::TypeStrike, Warning::TypeHidden])],
        ];
    }

}
