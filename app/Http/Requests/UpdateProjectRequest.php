<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'shotr_descr' => ['required', 'string', 'min:10', 'max:500'],
            'full_descr' => ['required', 'string', 'min:20', 'max:10000'],

            'categories' => ['nullable', 'array', 'max:4'],
            'categories.*' => ['nullable', 'string', 'max:100'],

            // Фото
            'fotos' => ['nullable', 'array', 'max:20'],
            'fotos.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp'],
            'deleted_photos' => ['nullable', 'array'],
            'deleted_photos.*' => ['integer', 'exists:project_photos,id'],

            'ownShip' => ['nullable', 'string', 'max:255'],
            'activity' => ['nullable', 'string', 'max:255'],
            'type_building' => ['nullable', 'string', 'max:255'],
            'addres' => ['required', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],

            'total_investment' => ['required', 'numeric', 'min:0'],
            'number_date_realise' => ['required', 'integer', 'min:1', 'max:1200'],
            'count_new_job' => ['required', 'integer', 'min:0'],
            'collected_total_investment' => ['nullable', 'numeric', 'min:0'],

            'expenses' => ['nullable', 'array'],
            'expenses.*.item_name' => ['required_with:expenses.*.amount', 'string', 'max:255'],
            'expenses.*.amount' => ['required_with:expenses.*.item_name', 'numeric', 'min:0'],

            // Новый формат прогнозов по годам
            'revenues' => ['nullable', 'array'],
            'revenues.*.year' => ['required_with:revenues.*.amount', 'integer', 'min:1', 'max:100'],
            'revenues.*.amount' => ['required_with:revenues.*.year', 'numeric', 'min:0'],

            'yearly_expenses' => ['nullable', 'array'],
            'yearly_expenses.*.year' => ['required_with:yearly_expenses.*.amount', 'integer', 'min:1', 'max:100'],
            'yearly_expenses.*.amount' => ['required_with:yearly_expenses.*.year', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'fotos.*.image' => 'Каждый файл должен быть изображением.',
            'fotos.*.mimes' => 'Разрешены только форматы: JPG, JPEG, PNG, WEBP.',

            'expenses.*.item_name.required_with' => 'Укажите название статьи расходов.',
            'expenses.*.amount.required_with' => 'Укажите сумму расхода.',

            'revenues.*.year.required_with' => 'Укажите год для дохода.',
            'revenues.*.amount.required_with' => 'Укажите сумму дохода за год.',
            'yearly_expenses.*.year.required_with' => 'Укажите год для расхода.',
            'yearly_expenses.*.amount.required_with' => 'Укажите сумму расхода за год.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'название проекта',
            'shotr_descr' => 'короткое описание',
            'full_descr' => 'подробное описание',
            'addres' => 'адрес',
            'total_investment' => 'требуемая сумма инвестиций',
            'number_date_realise' => 'срок окупаемости',
            'count_new_job' => 'количество рабочих мест',
            'collected_total_investment' => 'собранная сумма',
            'fotos.*' => 'фотография',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $revenues = collect($this->input('revenues', []))
                ->filter(fn ($r) => isset($r['year'], $r['amount']) && $r['year'] !== '' && $r['amount'] !== '');

            $yearlyExpenses = collect($this->input('yearly_expenses', []))
                ->filter(fn ($r) => isset($r['year'], $r['amount']) && $r['year'] !== '' && $r['amount'] !== '');

            if ($revenues->count() !== $yearlyExpenses->count()) {
                $validator->errors()->add('yearly_expenses', 'Количество лет доходов и расходов должно совпадать.');
                return;
            }

            $revenueYears = $revenues->pluck('year')->map(fn ($y) => (int) $y)->sort()->values()->all();
            $expenseYears = $yearlyExpenses->pluck('year')->map(fn ($y) => (int) $y)->sort()->values()->all();

            if ($revenueYears !== $expenseYears) {
                $validator->errors()->add('yearly_expenses', 'Годы в доходах и расходах должны совпадать.');
            }
        });
    }
}