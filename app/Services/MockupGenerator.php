<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use RuntimeException;

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

        $canvasSize = config('catalog.canvas', ['width' => 850, 'height' => 900]);

        if (! extension_loaded('gd')) {
            return $this->generateWithFfmpeg(
                $templateFullPath,
                $designFullPath,
                $position,
                $outputPath,
                $transform,
                $canvasSize
            );
        }

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

    private function generateWithFfmpeg(
        string $templateFullPath,
        string $designFullPath,
        array $position,
        ?string $outputPath,
        array $transform,
        array $canvasSize
    ): string {
        $path = $outputPath ?: 'products/'.uniqid('mockup-', true).'.png';
        $directory = dirname($path);

        if ($directory !== '.') {
            Storage::disk('public')->makeDirectory($directory);
        }

        $outputFullPath = Storage::disk('public')->path($path);
        $transform = array_merge([
            'x' => 50,
            'y' => 50,
            'scale' => 1,
            'rotation' => 0,
        ], $transform);

        $printWidth = max(1, (int) $position['w']);
        $printHeight = max(1, (int) $position['h']);
        $designWidth = max(1, (int) round($printWidth * (float) $transform['scale']));
        $designHeight = max(1, (int) round($printHeight * (float) $transform['scale']));
        $centerX = (int) round($printWidth * ((float) $transform['x'] / 100));
        $centerY = (int) round($printHeight * ((float) $transform['y'] / 100));
        $rotation = ((float) $transform['rotation']) * pi() / 180;

        $filter = implode('', [
            sprintf('[0:v]scale=%d:%d,format=rgba[template];', (int) $canvasSize['width'], (int) $canvasSize['height']),
            sprintf('[1:v]scale=%d:%d:force_original_aspect_ratio=decrease,format=rgba,rotate=%F:c=none:ow=rotw(iw):oh=roth(ih)[art];', $designWidth, $designHeight, $rotation),
            sprintf('color=color=black@0.0:size=%dx%d:d=1,format=rgba[area];', $printWidth, $printHeight),
            sprintf('[area][art]overlay=x=%d-w/2:y=%d-h/2:format=auto[printed];', $centerX, $centerY),
            sprintf('[template][printed]overlay=x=%d:y=%d:format=auto,format=rgba[out]', (int) $position['x'], (int) $position['y']),
        ]);

        $this->runCommand([
            'ffmpeg',
            '-y',
            '-v',
            'error',
            '-i',
            $templateFullPath,
            '-i',
            $designFullPath,
            '-filter_complex',
            $filter,
            '-map',
            '[out]',
            '-frames:v',
            '1',
            $outputFullPath,
        ]);

        if (! is_file($outputFullPath) || filesize($outputFullPath) === 0) {
            throw new RuntimeException('Mockup generation failed: FFmpeg did not produce an output image.');
        }

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
            'green' => '#2f6848',
            'red' => '#b91c1c',
            'blue' => '#2563eb',
            default => '#ffffff',
        };
    }

    private function runCommand(array $command): void
    {
        $process = proc_open($command, [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes);

        if (! is_resource($process)) {
            throw new RuntimeException('Mockup generation failed: unable to start FFmpeg.');
        }

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            $message = trim($stderr ?: $stdout);
            throw new RuntimeException('Mockup generation failed: '.($message ?: 'FFmpeg exited with code '.$exitCode.'.'));
        }
    }
}
