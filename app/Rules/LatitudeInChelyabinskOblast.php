<?php

namespace App\Rules;

use App\Services\GeometryService;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class LatitudeInChelyabinskOblast implements ValidationRule, DataAwareRule
{
    protected array $data = [];
    private GeometryService $geometryService;

    public function __construct()
    {
        $this->geometryService = app(GeometryService::class);
    }

    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }


    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $lat = (float) $value;
        $lon = isset($this->data['longitude']) ? (float) $this->data['longitude'] : null;

        if ($lon === null) {
            return;
        }

        $result = $this->geometryService->checkPoint($lat, $lon);
        

        $threshold = 0.00001;
        
        if (!$result['inside'] && $result['difference_total'] > $threshold) {
            $message = "Широта: не хватает " . number_format($result['difference_lat'], 6) . " пунктов";
            
            if ($result['nearest_point']) {
                $message .= ". Ближайшая граница: широта {$result['nearest_point']['lat']}, долгота {$result['nearest_point']['lon']}";
            }
            
            $fail($message);
        }
    }
}