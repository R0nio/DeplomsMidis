<?php

namespace App\Services;

class GeometryService
{
    private array $polygons = [];
    private array $boundingBox = [];

    public function __construct()
    {
        $this->loadPolygons();
    }

    private function loadPolygons(): void
    {
        $geoJsonPath = public_path('geo/chelyabinsk-oblast.json');
        
        if (!is_file($geoJsonPath)) {
            throw new \RuntimeException('GeoJSON файл с границами региона не найден: ' . $geoJsonPath);
        }

        $content = file_get_contents($geoJsonPath);
        $geo = json_decode($content, true);
        
        if (!is_array($geo) || !isset($geo['type'])) {
            throw new \RuntimeException('Некорректный GeoJSON файл');
        }

        $this->polygons = $this->extractPolygons($geo);
        $this->computeBoundingBox();
    }

    private function extractPolygons(array $geo): array
    {
        $polygons = [];

        if ($geo['type'] === 'FeatureCollection') {
            foreach ($geo['features'] as $feature) {
                $geom = $feature['geometry'] ?? null;
                if (!$geom) continue;
                
                $type = $geom['type'] ?? null;
                $coords = $geom['coordinates'] ?? [];
                
                if ($type === 'Polygon') {
                    $polygons[] = $coords;
                } elseif ($type === 'MultiPolygon') {
                    foreach ($coords as $polygon) {
                        $polygons[] = $polygon;
                    }
                }
            }
        } elseif ($geo['type'] === 'Feature') {
            $geom = $geo['geometry'] ?? [];
            $type = $geom['type'] ?? null;
            $coords = $geom['coordinates'] ?? [];
            
            if ($type === 'Polygon') {
                $polygons[] = $coords;
            } elseif ($type === 'MultiPolygon') {
                foreach ($coords as $polygon) {
                    $polygons[] = $polygon;
                }
            }
        } elseif ($geo['type'] === 'Polygon') {
            $polygons[] = $geo['coordinates'];
        } elseif ($geo['type'] === 'MultiPolygon') {
            foreach ($geo['coordinates'] as $polygon) {
                $polygons[] = $polygon;
            }
        }

        return $polygons;
    }

    private function computeBoundingBox(): void
    {
        $minLon = INF;
        $maxLon = -INF;
        $minLat = INF;
        $maxLat = -INF;

        foreach ($this->polygons as $rings) {
            if (empty($rings)) continue;
            $outer = $rings[0];
            foreach ($outer as $point) {
                $lon = (float) $point[0];
                $lat = (float) $point[1];
                
                $minLon = min($minLon, $lon);
                $maxLon = max($maxLon, $lon);
                $minLat = min($minLat, $lat);
                $maxLat = max($maxLat, $lat);
            }
        }

        $this->boundingBox = [
            'min_lon' => $minLon,
            'max_lon' => $maxLon,
            'min_lat' => $minLat,
            'max_lat' => $maxLat,
        ];
    }

    public function checkPoint(float $lat, float $lon): array
    {
        // Быстрая проверка по bounding box
        if (!$this->pointInBoundingBox($lon, $lat)) {
            $diffLon = $this->differenceToBoundingBoxLon($lon);
            $diffLat = $this->differenceToBoundingBoxLat($lat);
            
            // Для bounding box определяем ближайшую точку на углу
            $nearestPoint = $this->getNearestBoundingBoxPoint($lon, $lat);
            
            return [
                'inside' => false,
                'difference_lon' => $diffLon,
                'difference_lat' => $diffLat,
                'difference_total' => sqrt($diffLon * $diffLon + $diffLat * $diffLat),
                'nearest_point' => $nearestPoint,
                'message' => $this->formatMessage($diffLon, $diffLat, $nearestPoint)
            ];
        }

        // Точная проверка по всем полигонам
        $bestResult = null;
        $minDistance = INF;

        foreach ($this->polygons as $rings) {
            $result = $this->pointInPolygonWithDetailedDifference($lon, $lat, $rings);
            
            if ($result['inside']) {
                return [
                    'inside' => true,
                    'difference_lon' => 0,
                    'difference_lat' => 0,
                    'difference_total' => 0,
                    'nearest_point' => null,
                    'message' => null
                ];
            }
            
            if ($result['distance'] < $minDistance) {
                $minDistance = $result['distance'];
                $bestResult = $result;
            }
        }

        return [
            'inside' => false,
            'difference_lon' => $bestResult['diff_lon'],
            'difference_lat' => $bestResult['diff_lat'],
            'difference_total' => $bestResult['distance'],
            'nearest_point' => $bestResult['nearest_point'],
            'message' => $this->formatMessage($bestResult['diff_lon'], $bestResult['diff_lat'], $bestResult['nearest_point'])
        ];
    }

    private function formatMessage(float $diffLon, float $diffLat, ?array $nearestPoint = null): string
    {
        $parts = [];
        
        if ($diffLon > 0) {
            $parts[] = "по долготе " . number_format($diffLon, 6) . " пунктов";
        }
        if ($diffLat > 0) {
            $parts[] = "по широте " . number_format($diffLat, 6) . " пунктов";
        }
        
        $message = "Не хватает " . implode(" и ", $parts);
        
        if ($nearestPoint && isset($nearestPoint['lat']) && isset($nearestPoint['lon'])) {
            $message .= "\nБлижайшая точка на границе: широта {$nearestPoint['lat']}, долгота {$nearestPoint['lon']}";
        }
        
        return $message;
    }

    private function getNearestBoundingBoxPoint(float $lon, float $lat): array
    {
        // Находим ближайший угол bounding box'а
        $corners = [
            ['lon' => $this->boundingBox['min_lon'], 'lat' => $this->boundingBox['min_lat']],
            ['lon' => $this->boundingBox['min_lon'], 'lat' => $this->boundingBox['max_lat']],
            ['lon' => $this->boundingBox['max_lon'], 'lat' => $this->boundingBox['min_lat']],
            ['lon' => $this->boundingBox['max_lon'], 'lat' => $this->boundingBox['max_lat']],
        ];
        
        $minDistance = INF;
        $nearestPoint = null;
        
        foreach ($corners as $corner) {
            $distance = sqrt(
                ($lon - $corner['lon']) * ($lon - $corner['lon']) + 
                ($lat - $corner['lat']) * ($lat - $corner['lat'])
            );
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearestPoint = $corner;
            }
        }
        
        return $nearestPoint;
    }

    private function pointInBoundingBox(float $lon, float $lat): bool
    {
        return $lon >= $this->boundingBox['min_lon'] &&
               $lon <= $this->boundingBox['max_lon'] &&
               $lat >= $this->boundingBox['min_lat'] &&
               $lat <= $this->boundingBox['max_lat'];
    }

    private function differenceToBoundingBoxLon(float $lon): float
    {
        if ($lon < $this->boundingBox['min_lon']) {
            return $this->boundingBox['min_lon'] - $lon;
        } elseif ($lon > $this->boundingBox['max_lon']) {
            return $lon - $this->boundingBox['max_lon'];
        }
        return 0;
    }

    private function differenceToBoundingBoxLat(float $lat): float
    {
        if ($lat < $this->boundingBox['min_lat']) {
            return $this->boundingBox['min_lat'] - $lat;
        } elseif ($lat > $this->boundingBox['max_lat']) {
            return $lat - $this->boundingBox['max_lat'];
        }
        return 0;
    }

    private function pointInPolygonWithDetailedDifference(float $lon, float $lat, array $rings): array
    {
        if (empty($rings)) {
            return [
                'inside' => false, 
                'distance' => INF,
                'diff_lon' => 0,
                'diff_lat' => 0,
                'nearest_point' => null
            ];
        }

        $outer = $rings[0];
        $inside = $this->pointInRing($lon, $lat, $outer);
        
        if ($inside) {
            // Проверяем дырки
            for ($i = 1; $i < count($rings); $i++) {
                if ($this->pointInRing($lon, $lat, $rings[$i])) {
                    $result = $this->findNearestPointOnRing($lon, $lat, $rings[$i]);
                    return [
                        'inside' => false,
                        'distance' => $result['distance'],
                        'diff_lon' => $result['diff_lon'],
                        'diff_lat' => $result['diff_lat'],
                        'nearest_point' => $result['nearest_point']
                    ];
                }
            }
            return [
                'inside' => true,
                'distance' => 0,
                'diff_lon' => 0,
                'diff_lat' => 0,
                'nearest_point' => null
            ];
        }
        
        // Ищем ближайшую границу
        $bestResult = $this->findNearestPointOnRing($lon, $lat, $outer);
        
        // Проверяем дырки на случай, если точка ближе к дырке
        for ($i = 1; $i < count($rings); $i++) {
            $result = $this->findNearestPointOnRing($lon, $lat, $rings[$i]);
            if ($result['distance'] < $bestResult['distance']) {
                $bestResult = $result;
            }
        }
        
        return [
            'inside' => false,
            'distance' => $bestResult['distance'],
            'diff_lon' => $bestResult['diff_lon'],
            'diff_lat' => $bestResult['diff_lat'],
            'nearest_point' => $bestResult['nearest_point']
        ];
    }

    private function pointInRing(float $lon, float $lat, array $ring): bool
    {
        $inside = false;
        $n = count($ring);

        if ($n < 3) {
            return false;
        }

        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $xi = (float) $ring[$i][0];
            $yi = (float) $ring[$i][1];
            $xj = (float) $ring[$j][0];
            $yj = (float) $ring[$j][1];

            $intersect = (($yi > $lat) !== ($yj > $lat))
                && ($lon < ($xj - $xi) * ($lat - $yi) / (($yj - $yi) ?: 1e-12) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

    private function findNearestPointOnRing(float $lon, float $lat, array $ring): array
    {
        $minDistance = INF;
        $bestDiffLon = 0;
        $bestDiffLat = 0;
        $bestPoint = null;
        $n = count($ring);

        for ($i = 0; $i < $n; $i++) {
            $j = ($i + 1) % $n;
            
            $x1 = (float) $ring[$i][0];
            $y1 = (float) $ring[$i][1];
            $x2 = (float) $ring[$j][0];
            $y2 = (float) $ring[$j][1];
            
            $closest = $this->closestPointOnSegment($lon, $lat, $x1, $y1, $x2, $y2);
            $distance = sqrt(
                ($lon - $closest['x']) * ($lon - $closest['x']) + 
                ($lat - $closest['y']) * ($lat - $closest['y'])
            );
            
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $bestDiffLon = abs($lon - $closest['x']);
                $bestDiffLat = abs($lat - $closest['y']);
                $bestPoint = ['lon' => $closest['x'], 'lat' => $closest['y']];
            }
        }

        return [
            'distance' => $minDistance,
            'diff_lon' => $bestDiffLon,
            'diff_lat' => $bestDiffLat,
            'nearest_point' => $bestPoint
        ];
    }

    private function closestPointOnSegment(float $px, float $py, float $x1, float $y1, float $x2, float $y2): array
    {
        $dx = $x2 - $x1;
        $dy = $y2 - $y1;
        
        if ($dx == 0 && $dy == 0) {
            return ['x' => $x1, 'y' => $y1];
        }
        
        $t = (($px - $x1) * $dx + ($py - $y1) * $dy) / ($dx * $dx + $dy * $dy);
        
        if ($t < 0) {
            $t = 0;
        } elseif ($t > 1) {
            $t = 1;
        }
        
        return [
            'x' => $x1 + $t * $dx,
            'y' => $y1 + $t * $dy
        ];
    }
}