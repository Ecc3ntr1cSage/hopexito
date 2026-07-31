<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class MockupGenerator
{
    public function generate(
        UploadedFile|string $design,
        string $templatePath,
        array $position,
        string $garmentColor,
        ?string $outputPath = null,
        array $transform = []
    ): string {
        $templateFullPath = $this->fullPath($templatePath);
        $designFullPath = $design instanceof UploadedFile ? $design->getRealPath() : $this->fullPath($design);

        if (! extension_loaded('gd')) {
            $path = $outputPath ?: 'products/'.uniqid('mockup-', true).'.png';
            Storage::disk('public')->put($path, file_get_contents($templateFullPath));
            return $path;
        }

        $canvasSize = config('catalog.canvas', ['width' => 850, 'height' => 900]);
        $template = Image::make($templateFullPath)->resize($canvasSize['width'], $canvasSize['height']);
        $canvas = Image::canvas($template->width(), $template->height(), $this->color($garmentColor));

        $canvas->insert($template, 'top-left', 0, 0);

        $transform = array_merge([
            'x' => 50,
            'y' => 50,
            'scale' => 1,
            'rotation' => 0,
        ], $transform);

        $printArea = Image::canvas(
            (int) $position['w'],
            (int) $position['h'],
            'rgba(0,0,0,0)'
        );

        $designImage = Image::make($designFullPath)->resize(
            (int) ((int) $position['w'] * (float) $transform['scale']),
            (int) ((int) $position['h'] * (float) $transform['scale']),
            function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            }
        );

        if ((float) $transform['rotation'] !== 0.0) {
            $designImage->rotate((float) $transform['rotation'], 'rgba(0,0,0,0)');
        }

        $centerX = (int) round((int) $position['w'] * ((float) $transform['x'] / 100));
        $centerY = (int) round((int) $position['h'] * ((float) $transform['y'] / 100));
        $x = $centerX - (int) round($designImage->width() / 2);
        $y = $centerY - (int) round($designImage->height() / 2);

        $printArea->insert($designImage, 'top-left', $x, $y);
        $canvas->insert($printArea, 'top-left', (int) $position['x'], (int) $position['y']);

        $path = $outputPath ?: 'products/'.uniqid('mockup-', true).'.png';
        Storage::disk('public')->put($path, (string) $canvas->encode('png'));

        return $path;
    }

    private function fullPath(string $path): string
    {
        if (is_file($path)) {
            return $path;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->path($path);
        }

        if (Storage::disk('public')->exists('mockup-image/'.$path)) {
            return Storage::disk('public')->path('mockup-image/'.$path);
        }

        return public_path(ltrim($path, '/'));
    }

    private function color(string $color): string
    {
        return match (strtolower($color)) {
            'black' => '#111111',
            'gray', 'grey' => '#808080',
            'navy' => '#1f2a44',
            'red' => '#b91c1c',
            'blue' => '#2563eb',
            default => '#ffffff',
        };
    }
}
