<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can_manage_clients;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $clientId = $this->route('client')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:100'],
            'src_code' => ['nullable', 'string', 'max:50', Rule::unique('clients', 'src_code')->ignore($clientId)->whereNull('deleted_at')],
            'contracts' => ['nullable', 'array'],
            'contracts.*.src_code' => ['nullable', 'string', 'max:50'],
            'contracts.*.name' => ['required', 'string', 'max:255'],
            'contracts.*.short_name' => ['nullable', 'string', 'max:100'],
            'contracts.*.start_date' => ['nullable', 'date', 'before:contracts.*.end_date'],
            'contracts.*.end_date' => ['nullable', 'date', 'after:contracts.*.start_date'],
            'contracts.*.monthly_value' => ['nullable', 'decimal:0,4', 'min:0']
        ];
    }
}
