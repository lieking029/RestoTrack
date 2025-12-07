<?php

namespace App\Http\Requests\Web\Admin\Menu;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:menus,name,' . $this->menu->id,
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0|max:999999.99',
            'category' => 'required|integer|in:0,1,2,3,4', // Appetizer, MainCourse, Dessert, Beverage, Snack
            'dish_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Optional on update
            'status' => 'nullable', // Checkbox value
            'ingredients' => 'nullable|array',
            'ingredients.*.product_id' => 'required_with:ingredients|exists:products,id',
            'ingredients.*.quantity_needed' => 'required_with:ingredients|numeric|min:0.01|max:9999.99',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'dish name',
            'description' => 'dish description',
            'price' => 'price',
            'category' => 'category',
            'dish_picture' => 'dish image',
            'ingredients.*.product_id' => 'ingredient',
            'ingredients.*.quantity_needed' => 'quantity needed',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The dish name is required.',
            'name.unique' => 'A menu item with this name already exists.',
            'description.required' => 'Please provide a description for the dish.',
            'description.max' => 'The description cannot exceed 1000 characters.',
            'price.required' => 'The price is required.',
            'price.numeric' => 'The price must be a valid number.',
            'price.min' => 'The price cannot be negative.',
            'price.max' => 'The price is too high.',
            'category.required' => 'Please select a category.',
            'category.in' => 'The selected category is invalid.',
            'dish_picture.image' => 'The file must be an image.',
            'dish_picture.mimes' => 'The image must be in JPEG, PNG, JPG, GIF, or WEBP format.',
            'dish_picture.max' => 'The image size cannot exceed 2MB.',
            'ingredients.*.product_id.required_with' => 'Please select an ingredient.',
            'ingredients.*.product_id.exists' => 'The selected ingredient does not exist.',
            'ingredients.*.quantity_needed.required_with' => 'Please specify the quantity needed.',
            'ingredients.*.quantity_needed.numeric' => 'The quantity must be a valid number.',
            'ingredients.*.quantity_needed.min' => 'The quantity must be at least 0.01.',
        ];
    }
}