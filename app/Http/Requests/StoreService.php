<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreService extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3'],
            'url' => ['required_with:host|port|protocol', 'nullable', 'url'],
            'port' => ['required_without:url', 'nullable', 'int', 'min:0'],
            'host' => ['required_without:url', 'nullable', 'string'],
            'protocol' => ['required_without:url', 'nullable', 'string'],
            'readTimeout' => ['required', 'int', 'min:0'],
            'writeTimeout' => ['required', 'int', 'min:0'],
            'connectTimeout' => ['required', 'int', 'min:0']
        ];
    }
}
