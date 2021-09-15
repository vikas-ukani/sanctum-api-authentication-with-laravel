<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class RegisterUserRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255'
            ],
            'email' => [
                'required',
                'string',
                'email',
                'min:3', 
                'max:255',
                'unique:users'
            ],
            'password' => [
                'required',
                'string',
                'between:8,12'
            ]
        ];
    }

    /**
     * Storing Users Information to the database from this code
     *
     * @return object
     */
    public function store()
    {
        return User::create($this->validated());
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
        throw new HttpResponseException(response()->json($validator->errors()), Response::HTTP_BAD_REQUEST);
    }
}
