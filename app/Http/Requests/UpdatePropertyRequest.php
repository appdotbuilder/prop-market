<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:house,land,warehouse,shop_house,kiosk,boarding_house,building,apartment',
            'title' => 'required|string|max:255',
            'address' => 'required|string',
            'price' => 'required|numeric|min:0',
            'listing_type' => 'required|in:sale,rent',
            'rent_period' => 'nullable|required_if:listing_type,rent|string|in:monthly,yearly',
            'land_area' => 'nullable|numeric|min:0',
            'building_area' => 'nullable|numeric|min:0',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'description' => 'required|string',
            'photos' => 'nullable|array',
            'photos.*' => 'string',
            'status' => 'nullable|in:available,sold,rented',
            'owner_id' => 'required|exists:users,id',
            'agent_ids' => 'nullable|array',
            'agent_ids.*' => 'exists:users,id',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Property type is required.',
            'type.in' => 'Please select a valid property type.',
            'title.required' => 'Property title is required.',
            'address.required' => 'Property address is required.',
            'price.required' => 'Property price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price must be greater than or equal to 0.',
            'listing_type.required' => 'Please specify if this property is for sale or rent.',
            'rent_period.required_if' => 'Rent period is required for rental properties.',
            'description.required' => 'Property description is required.',
            'owner_id.required' => 'Property owner is required.',
            'owner_id.exists' => 'Selected owner does not exist.',
        ];
    }
}