<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class DocumentImageHelper
{
    /**
     * Supported standard image extensions.
     */
    protected static array $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];

    /**
     * Resolve absolute file path on disk for an image/document,
     * converting PDF to image if necessary.
     */
    public static function resolveImageAbsolutePath(?string $relativePath): ?string
    {
        if (empty($relativePath)) {
            return null;
        }

        $basePath = storage_path('app/public/' . ltrim($relativePath, '/'));
        if (!file_exists($basePath)) {
            if (file_exists($relativePath)) {
                $basePath = $relativePath;
            } else {
                return null;
            }
        }

        $extension = strtolower(pathinfo($basePath, PATHINFO_EXTENSION));

        // If it's already an image, return directly
        if (in_array($extension, self::$imageExtensions, true)) {
            return $basePath;
        }

        // If it's a PDF, convert or extract image
        if ($extension === 'pdf') {
            return self::convertPdfToImage($basePath);
        }

        return $basePath;
    }

    /**
     * Resolve web asset URL for an image/document,
     * converting PDF to image if necessary.
     */
    public static function resolveImageUrl(?string $relativePath): ?string
    {
        if (empty($relativePath)) {
            return null;
        }

        $absPath = self::resolveImageAbsolutePath($relativePath);
        if (!$absPath || !file_exists($absPath)) {
            return null;
        }

        // Check if path is within storage/app/public
        $publicStorage = storage_path('app/public');
        if (str_starts_with($absPath, $publicStorage)) {
            $rel = ltrim(substr($absPath, strlen($publicStorage)), '/\\');
            return asset('storage/' . str_replace('\\', '/', $rel));
        }

        return asset('storage/' . ltrim($relativePath, '/'));
    }

    /**
     * Convert or extract the first page of a PDF file to a PNG/JPEG image.
     */
    protected static function convertPdfToImage(string $pdfPath): ?string
    {
        try {
            $dir = pathinfo($pdfPath, PATHINFO_DIRNAME);
            $filename = pathinfo($pdfPath, PATHINFO_FILENAME);

            // Check for pre-existing converted file
            $cachedJpg = $dir . '/' . $filename . '_converted.jpg';
            $cachedPng = $dir . '/' . $filename . '_converted.png';

            $pdfMtime = filemtime($pdfPath);

            if (file_exists($cachedJpg) && filemtime($cachedJpg) >= $pdfMtime && filesize($cachedJpg) > 100) {
                return $cachedJpg;
            }

            if (file_exists($cachedPng) && filemtime($cachedPng) >= $pdfMtime && filesize($cachedPng) > 100) {
                return $cachedPng;
            }

            // Method 1: Pure PHP extraction of embedded JPEG from PDF stream (Fastest, zero quality loss, zero dependencies)
            $extractedPath = self::extractEmbeddedImageFromPdf($pdfPath, $cachedJpg);
            if ($extractedPath && file_exists($extractedPath) && filesize($extractedPath) > 100) {
                return $extractedPath;
            }

            // Method 2: macOS native `sips` CLI
            if (PHP_OS_FAMILY === 'Darwin' && is_executable('/usr/bin/sips')) {
                $cmd = sprintf('/usr/bin/sips -s format png %s --out %s 2>&1', escapeshellarg($pdfPath), escapeshellarg($cachedPng));
                @exec($cmd, $output, $code);
                if ($code === 0 && file_exists($cachedPng) && filesize($cachedPng) > 100) {
                    return $cachedPng;
                }
            }

            // Method 3: `pdftoppm` (poppler-utils, common on Linux)
            $pdftoppm = self::findBinary('pdftoppm');
            if ($pdftoppm) {
                $prefix = $dir . '/' . $filename . '_converted';
                $cmd = sprintf('%s -png -r 150 -f 1 -l 1 -singlefile %s %s 2>&1', escapeshellarg($pdftoppm), escapeshellarg($pdfPath), escapeshellarg($prefix));
                @exec($cmd, $output, $code);
                if (file_exists($cachedPng) && filesize($cachedPng) > 100) {
                    return $cachedPng;
                }
            }

            // Method 4: `gs` (Ghostscript)
            $gs = self::findBinary('gs');
            if ($gs) {
                $cmd = sprintf('%s -dNOPAUSE -dBATCH -sDEVICE=png16m -r150 -dFirstPage=1 -dLastPage=1 -sOutputFile=%s %s 2>&1', escapeshellarg($gs), escapeshellarg($cachedPng), escapeshellarg($pdfPath));
                @exec($cmd, $output, $code);
                if (file_exists($cachedPng) && filesize($cachedPng) > 100) {
                    return $cachedPng;
                }
            }

            // Method 5: PHP Imagick extension
            if (extension_loaded('imagick')) {
                try {
                    $imagick = new \Imagick();
                    $imagick->setResolution(150, 150);
                    $imagick->readImage($pdfPath . '[0]');
                    $imagick->setImageFormat('png');
                    $imagick->writeImage($cachedPng);
                    $imagick->clear();
                    $imagick->destroy();

                    if (file_exists($cachedPng) && filesize($cachedPng) > 100) {
                        return $cachedPng;
                    }
                } catch (\Throwable $e) {
                    Log::warning('Imagick PDF conversion failed: ' . $e->getMessage());
                }
            }

            // Method 6: macOS qlmanage thumbnail generator fallback
            if (PHP_OS_FAMILY === 'Darwin' && is_executable('/usr/bin/qlmanage')) {
                $cmd = sprintf('/usr/bin/qlmanage -t -s 1000 -o %s %s 2>&1', escapeshellarg($dir), escapeshellarg($pdfPath));
                @exec($cmd, $output, $code);
                $qlPng = $dir . '/' . pathinfo($pdfPath, PATHINFO_BASENAME) . '.png';
                if (file_exists($qlPng) && filesize($qlPng) > 100) {
                    rename($qlPng, $cachedPng);
                    return $cachedPng;
                }
            }

        } catch (\Throwable $e) {
            Log::warning('DocumentImageHelper convertPdfToImage error: ' . $e->getMessage());
        }

        // Return original file path if conversion could not be performed
        return $pdfPath;
    }

    /**
     * Pure PHP stream extractor to extract embedded JPEG or PNG images from a PDF file.
     */
    protected static function extractEmbeddedImageFromPdf(string $pdfPath, string $targetJpgPath): ?string
    {
        $content = @file_get_contents($pdfPath);
        if (!$content) {
            return null;
        }

        // Find streams in PDF
        preg_match_all('/stream[\r\n]+(.*?)[\r\n]+endstream/s', $content, $matches);
        if (empty($matches[1])) {
            return null;
        }

        foreach ($matches[1] as $stream) {
            // Check direct JPEG header (FF D8 FF)
            if (str_starts_with($stream, "\xFF\xD8\xFF")) {
                file_put_contents($targetJpgPath, $stream);
                return $targetJpgPath;
            }

            // Check if stream was compressed with FlateDecode (zlib)
            $uncompressed = @gzuncompress($stream);
            if ($uncompressed && str_starts_with($uncompressed, "\xFF\xD8\xFF")) {
                file_put_contents($targetJpgPath, $uncompressed);
                return $targetJpgPath;
            }

            // Check if uncompressed has PNG signature (89 50 4E 47)
            if ($uncompressed && str_starts_with($uncompressed, "\x89PNG\r\n\x1a\n")) {
                $targetPngPath = substr($targetJpgPath, 0, -4) . '.png';
                file_put_contents($targetPngPath, $uncompressed);
                return $targetPngPath;
            }
        }

        return null;
    }

    /**
     * Helper to find a binary on the system PATH.
     */
    protected static function findBinary(string $binary): ?string
    {
        $output = null;
        $returnCode = 0;
        $cmd = PHP_OS_FAMILY === 'Windows' ? "where {$binary}" : "which {$binary}";
        @exec($cmd, $output, $returnCode);

        if ($returnCode === 0 && !empty($output[0])) {
            return trim($output[0]);
        }

        return null;
    }
}
