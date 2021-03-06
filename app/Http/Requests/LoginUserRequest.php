<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class LoginUserRequest extends FormRequest
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
            'email' => [
                'required',
                'email'
            ],
            'password' => [
                'required',
                'between:8,12'
            ]
        ];
    }


    /**
     * Returning an throwable exaction whens the validation goes wrong.
     *
     * @param Validator $validator
     *
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    /**
     * Checking an Valid user details by login credentials.
     *
     * @return void
     */
    public function checkValidLoginUser()
    {
        if (!Auth::attempt($this->validated())) {
            throw new HttpResponseException(response()->json(['status' => false, 'message' => __('Invalid Login credentials')]));
        }
        return true;
    }
}
