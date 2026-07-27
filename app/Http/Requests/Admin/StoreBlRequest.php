<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Mêmes règles que App\Filament\Resources\BlResource::form().
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'bl' => ['required', 'string', 'max:255'],
            'customer' => ['required', 'exists:customers,id'],
            'customer_company' => ['nullable', 'exists:customer_companies,id'],
            'company' => ['required', 'exists:companies,id'],
            'type_operation' => ['required', 'in:CHARGEMENT,DEPOTAGE,TRANSFERT MAD'],
            'telex' => ['nullable', 'boolean'],
            'is_urgent' => ['nullable', 'boolean'],
            'description' => ['required', 'string', 'max:255'],
            'product_type' => ['required', 'exists:product_type,id'],
            'nb_package' => ['required', 'numeric'],
            'quantity' => ['required', 'numeric'],
            'product_value' => ['nullable', 'numeric'],
            'tariff' => ['nullable', 'numeric'],
            'shipper' => ['required', 'string', 'max:200'],
            'vessel' => ['required', 'string', 'max:200'],
            'loading_date' => ['required', 'date'],
            'port_of_load' => ['required', 'string', 'max:200'],
            'port_of_discharge' => ['required', 'string', 'max:200'],
            'eta_date' => ['nullable', 'date'],
            'route' => ['nullable', 'string'],
            'consignee' => ['nullable', 'string', 'max:200'],
            'notify' => ['nullable', 'string', 'max:200'],
            'agent' => ['nullable', 'string', 'max:100'],
            'observation' => ['nullable', 'string'],
        ];
    }
}
