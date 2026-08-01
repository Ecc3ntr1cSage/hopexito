<?php

namespace App\Support;

use InvalidArgumentException;

final class MockupAssets
{
    public static function path(string $productType, string $color, string $side): string
    {
        if (! array_key_exists($productType, config('catalog.types', []))) {
            throw new InvalidArgumentException("Unknown product type: {$productType}");
        }

        if (! in_array($side, ['front', 'back'], true)) {
            throw new InvalidArgumentException("Unknown mockup side: {$side}");
        }

        return sprintf(
            'mockups/%s/%s-%s-%s.png',
            $productType,
            strtolower($color),
            $productType,
            $side,
        );
    }

    public static function migrateLegacyPath(string $path): ?string
    {
        if (! str_starts_with($path, 'mockups/')) {
            return null;
        }

        $filename = basename($path);
        $parts = explode('-', pathinfo($filename, PATHINFO_FILENAME));
        $productType = $parts[count($parts) - 2] ?? null;

        return $productType && array_key_exists($productType, config('catalog.types', []))
            ? 'mockups/'.$productType.'/'.$filename
            : null;
    }
}
