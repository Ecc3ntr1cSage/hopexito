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
        ?string $outputPath = null
    ): string {
        $templateFullPath = $this->fullPath($templatePath);
        $designFullPath = $design instanceof UploadedFile ? $design->getRealPath() : $this->fullPath($design);

        if (! extension_loaded('gd')) {
            $path = $outputPath ?: 'products/'.uniqid('mockup-', true).'.png';
            Storage::disk('public')->put($path, file_get_contents($templateFullPath));
            return $path;
        }

        $template = Image::make($templateFullPath)->resize(880, 900);
        $canvas = Image::canvas($template->width(), $template->height(), $this->color($garmentColor));

        $canvas->insert($template, 'top-left', 0, 0);

        $designImage = Image::make($designFullPath)->resize(
            (int) $position['w'],
            (int) $position['h'],
            function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            }
        );

        $x = (int) $position['x'] + (int) (((int) $position['w'] - $designImage->width()) / 2);
        $y = (int) $position['y'] + (int) (((int) $position['h'] - $designImage->height()) / 2);

        $canvas->insert($designImage, 'top-left', max(0, $x), max(0, $y));

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
