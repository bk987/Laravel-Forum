<?php

namespace App\Http\Requests;

use App\Thread;
use App\Rules\Recaptcha;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ThreadStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('store', new Thread);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Recaptcha $recaptcha)
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|spamfree',
            'content' => 'required|spamfree',
            'g-recaptcha-response' => ['required', $recaptcha]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'category_id.required' => 'The category field is required.'
        ];
    }
}
