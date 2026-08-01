<?php

namespace App\Support;

class MockupGeometry
{
    public function canvas(): array
    {
        return config('catalog.canvas', ['width' => 850, 'height' => 900]);
    }

    public function position(string $productType, string $side): array
    {
        $position = config("catalog.types.{$productType}.{$side}_position");

        abort_unless(is_array($position), 422, "Missing {$side} mockup position for {$productType}.");

        return $position;
    }

    public function normalizedPositions(array $catalog): array
    {
        $canvas = $this->canvas();
        $positions = [];

        foreach ($catalog as $type => $definition) {
            foreach (['front', 'back'] as $side) {
                $position = $definition["{$side}_position"] ?? [];
                $positions[$type][$side] = [
                    'x' => $this->percent($position['x'] ?? 0, $canvas['width']),
                    'y' => $this->percent($position['y'] ?? 0, $canvas['height']),
                    'w' => $this->percent($position['w'] ?? 0, $canvas['width']),
                    'h' => $this->percent($position['h'] ?? 0, $canvas['height']),
                ];
            }
        }

        return $positions;
    }

    private function percent(float|int $value, float|int $total): float
    {
        return round(($value / $total) * 100, 4);
    }
}
