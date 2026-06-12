<?php

namespace App\Http\Requests;

use App\Rules\LatitudeInChelyabinskOblast;
use App\Rules\LongitudeInChelyabinskOblast;
use App\Rules\PointInChelyabinskOblast;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreProjectRequest extends FormRequest
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

            'fotos' => ['nullable', 'array', 'max:20'],
            'fotos.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp'],

            'ownShip' => ['nullable', 'string', 'max:255'],
            'activity' => ['nullable', 'string', 'max:255'],
            'type_building' => ['nullable', 'string', 'max:255'],
            'addres' => ['required', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:-90,90', new LatitudeInChelyabinskOblast],
            'longitude' => ['required', 'numeric', 'between:-180,180', new LongitudeInChelyabinskOblast],
            
            'total_investment' => ['required', 'numeric', 'min:0'],
            'number_date_realise' => ['required', 'integer', 'min:1', 'max:1200'],
            'count_new_job' => ['required', 'integer', 'min:0'],
            'collected_total_investment' => ['nullable', 'numeric', 'min:0'],

            'expenses' => ['nullable', 'array'],
            'expenses.*.item_name' => ['required_with:expenses.*.amount', 'string', 'max:255'],
            'expenses.*.amount' => ['required_with:expenses.*.item_name', 'numeric', 'min:0'],

            'revenues' => ['nullable', 'array'],
            'revenues.*.year' => ['required_with:revenues.*.amount', 'integer', 'min:1'],
            'revenues.*.amount' => ['required_with:revenues.*.year', 'numeric', 'min:0'],

            'yearly_expenses' => ['nullable', 'array'],
            'yearly_expenses.*.year' => ['required_with:yearly_expenses.*.amount', 'integer', 'min:1'],
            'yearly_expenses.*.amount' => ['required_with:yearly_expenses.*.year', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'fotos.*.image' => 'Каждый файл должен быть изображением.',
            'fotos.*.mimes' => 'Разрешены только: jpg, jpeg, png, webp.',
            'revenues.*.year.required_with' => 'Укажите год для дохода.',
            'yearly_expenses.*.year.required_with' => 'Укажите год для расхода.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'название проекта',
            'shotr_descr' => 'короткое описание',
            'full_descr' => 'подробное описание',
            'addres' => 'адрес',
            'latitude' => 'широта',
            'longitude' => 'долгота',
            'revenues' => 'доходы по годам',
            'yearly_expenses' => 'расходы по годам',
            'count_new_job' => 'количество рабочих мест',
            'total_investment' => 'требуемая сумма инвестиций',
            'number_date_realise' => 'срок окупаемости',
            'fotos.*' => 'фотография',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $revenues = collect($this->input('revenues', []))
                ->filter(fn ($r) => isset($r['year']) && $r['year'] !== '' && isset($r['amount']) && $r['amount'] !== '');

            $yearlyExpenses = collect($this->input('yearly_expenses', []))
                ->filter(fn ($r) => isset($r['year']) && $r['year'] !== '' && isset($r['amount']) && $r['amount'] !== '');

            if ($revenues->count() !== $yearlyExpenses->count()) {
                $validator->errors()->add(
                    'yearly_expenses',
                    'Количество лет доходов и расходов должно совпадать.'
                );
                return;
            }

            $revenueYears = $revenues->pluck('year')->map(fn ($y) => (int) $y)->sort()->values()->all();
            $expenseYears = $yearlyExpenses->pluck('year')->map(fn ($y) => (int) $y)->sort()->values()->all();

            if ($revenueYears !== $expenseYears) {
                $validator->errors()->add(
                    'yearly_expenses',
                    'Годы в доходах и расходах должны совпадать.'
                );
            }
        });
    }
}