<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceStore extends FormRequest
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
            'number' => 'required|string',
            'transmitter_name' => 'required|string',
            'transmitter_nit' => 'required|string',
            'receiver_name' => 'required|string',
            'receiver_nit' => 'required|string',
            'subtotal' => 'required|string',            
            'total' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'number.required' => 'El campo Numero de Factura es obligatorio.',
            'transmitter_name.required' => 'El campo Nombre del vendedor es obligatorio.',
            'transmitter_nit.required' => 'El campo Nit del vendedor es obligatorio.',
            'receiver_name.required' => 'El campo Nombre del comprador es obligatorio.',
            'receiver_nit.required' => 'El campo Nit del comprador es obligatorio.',
            'subtotal.required' => 'La factura no tiene un valor antes de Iva',            
            'total.required' => 'La factura no tiene un valor total.'
        ];
    }
}
