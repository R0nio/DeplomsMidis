<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'shotr_descr' => 'required|string|max:255',
            'full_descr' => 'required|string',
            'categories' => 'nullable|array|max:4',
            'categories.*' => 'string|max:255',
            
            // 'fotos' => 'nullable|array|max:10',
            // 'fotos.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            
            'ownShip' => 'nullable|string|max:255',
            'activity' => 'nullable|string|max:255',
            'type_building' => 'nullable|string|max:255',
            'addres' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'total_investment' => 'nullable|numeric|min:0',
            'number_date_realise' => 'nullable|integer|min:1',
            'count_new_job' => 'nullable|integer|min:0',
            'collected_total_investment' => 'nullable|numeric|min:0',
            'expected_revenue' => 'nullable|numeric|min:0',
            'expected_expenses' => 'nullable|numeric|min:0',
            'expenses' => 'nullable|array',
            'expenses.*.item_name' => 'required_with:expenses|string|max:255',
            'expenses.*.amount' => 'required_with:expenses|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Название проекта обязательно для заполнения',
            'shotr_descr.required' => 'Короткое описание обязательно для заполнения',
            'full_descr.required' => 'Полное описание обязательно для заполнения',
            'categories.max' => 'Максимум 4 категории',
            'fotos.max' => 'Можно загрузить не более 10 изображений',
            'fotos.*.image' => 'Файл должен быть изображением',
            'fotos.*.mimes' => 'Поддерживаемые форматы: jpeg, png, jpg, gif',
            'fotos.*.max' => 'Размер каждого изображения не должен превышать 5MB',
            'latitude.between' => 'Широта должна быть в диапазоне от -90 до 90',
            'longitude.between' => 'Долгота должна быть в диапазоне от -180 до 180',
            'expenses.*.item_name.required_with' => 'Укажите название статьи расходов',
            'expenses.*.amount.required_with' => 'Укажите сумму расхода',
            'expenses.*.amount.min' => 'Сумма расхода не может быть отрицательной',
        ];
    }
}