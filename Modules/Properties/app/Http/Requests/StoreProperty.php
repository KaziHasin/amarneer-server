<?php

namespace Modules\Properties\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProperty extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'listing_type' => 'required|in:rent,sale',
            'price' => 'required|numeric',
            'area' => 'required|numeric',
            'location' => 'required|string',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
            'images' => 'required|array|max:3',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:5120',
            'owner_name' => 'required|string',
            'owner_phone' => 'required|string',
            'owner_email' => 'nullable|email',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a valid text value.',

            'category_id.required' => 'Category is required.',
            'category_id.exists' => 'Selected category is invalid.',

            'listing_type.required' => 'Listing type is required.',
            'listing_type.in' => 'Listing type must be either rent or sale.',

            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a valid number.',

            'area.required' => 'Area is required.',
            'area.numeric' => 'Area must be a valid number.',

            'city.required' => 'City is required.',
            'city.string' => 'City must be a valid text value.',

            'location.required' => 'Location is required.',
            'location.string' => 'Location must be a valid text value.',

            'description.string' => 'Description must be a valid text value.',

            'images.array' => 'Images must be an array.',
            'images.*.image' => 'Each uploaded file must be an image.',
            'images.*.mimes' => 'Each image must be a file of type: jpg, jpeg, png.',
            'images.*.max' => 'Each image may not be greater than 5 MB.',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
