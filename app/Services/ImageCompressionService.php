<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageCompressionService
{
    protected ImageManager $manager;
    
    // Configuration
    protected int $maxWidth = 1920;
    protected int $maxHeight = 1920;
    protected int $quality = 85;
    protected int $thumbnailWidth = 400;
    protected int $thumbnailHeight = 400;

    public function __construct()
    {
        // Use GD driver (pre-installed with PHP)
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Process and compress an uploaded image.
     *
     * @param UploadedFile $file The uploaded image file
     * @param string $directory Storage directory (e.g., 'gallery', 'profiles')
     * @param bool $createThumbnail Whether to create thumbnail
     * @param array $options Additional options (width, height, quality)
     * @return array Returns ['path' => 'original_path', 'thumbnail' => 'thumbnail_path']
     */
    public function processImage(
        UploadedFile $file,
        string $directory,
        bool $createThumbnail = false,
        array $options = []
    ): array {
        // Override defaults with options
        $maxWidth = $options['max_width'] ?? $this->maxWidth;
        $maxHeight = $options['max_height'] ?? $this->maxHeight;
        $quality = $options['quality'] ?? $this->quality;

        // Generate unique filename
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = "{$directory}/{$filename}";

        // Load and process image
        $image = $this->manager->read($file->getRealPath());

        // Resize if image exceeds max dimensions (maintain aspect ratio)
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        if ($originalWidth > $maxWidth || $originalHeight > $maxHeight) {
            $image->scale(
                width: $maxWidth,
                height: $maxHeight
            );
        }

        // Save compressed image to storage
        $encoded = $image->toJpeg(quality: $quality);
        Storage::disk('public')->put($path, $encoded);

        $result = ['path' => $path];

        // Create thumbnail if requested
        if ($createThumbnail) {
            $result['thumbnail'] = $this->createThumbnail($file, $directory, $filename, $options);
        }

        return $result;
    }

    /**
     * Create a thumbnail from an uploaded image.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string $originalFilename
     * @param array $options
     * @return string Thumbnail path
     */
    protected function createThumbnail(
        UploadedFile $file,
        string $directory,
        string $originalFilename,
        array $options = []
    ): string {
        $thumbWidth = $options['thumb_width'] ?? $this->thumbnailWidth;
        $thumbHeight = $options['thumb_height'] ?? $this->thumbnailHeight;
        $quality = $options['quality'] ?? $this->quality;

        // Generate thumbnail filename
        $thumbFilename = 'thumb_' . $originalFilename;
        $thumbPath = "{$directory}/thumbnails/{$thumbFilename}";

        // Load image
        $image = $this->manager->read($file->getRealPath());

        // Create square thumbnail with cover (crop to fit)
        $image->cover($thumbWidth, $thumbHeight);

        // Save thumbnail
        $encoded = $image->toJpeg(quality: $quality);
        Storage::disk('public')->put($thumbPath, $encoded);

        return $thumbPath;
    }

    /**
     * Process profile photo with specific dimensions.
     *
     * @param UploadedFile $file
     * @return string Saved file path
     */
    public function processProfilePhoto(UploadedFile $file): string
    {
        $filename = 'profile_' . uniqid() . '_' . time() . '.jpg';
        $path = "profiles/{$filename}";

        // Load image
        $image = $this->manager->read($file->getRealPath());

        // Create square profile photo (500x500)
        $image->cover(500, 500);

        // Save with high quality
        $encoded = $image->toJpeg(quality: 90);
        Storage::disk('public')->put($path, $encoded);

        return $path;
    }

    /**
     * Process gallery image with thumbnail.
     *
     * @param UploadedFile $file
     * @return array ['path' => string, 'thumbnail' => string]
     */
    public function processGalleryImage(UploadedFile $file): array
    {
        return $this->processImage(
            file: $file,
            directory: 'gallery',
            createThumbnail: true,
            options: [
                'max_width' => 1920,
                'max_height' => 1920,
                'quality' => 85,
                'thumb_width' => 400,
                'thumb_height' => 400,
            ]
        );
    }

    /**
     * Process service image.
     *
     * @param UploadedFile $file
     * @return string Saved file path
     */
    public function processServiceImage(UploadedFile $file): string
    {
        $result = $this->processImage(
            file: $file,
            directory: 'services',
            createThumbnail: false,
            options: [
                'max_width' => 1200,
                'max_height' => 800,
                'quality' => 85,
            ]
        );

        return $result['path'];
    }

    /**
     * Delete image and its thumbnail if exists.
     *
     * @param string $path Image path
     * @param bool $hasThumbnail Whether image has thumbnail
     * @return bool
     */
    public function deleteImage(string $path, bool $hasThumbnail = false): bool
    {
        $deleted = Storage::disk('public')->delete($path);

        if ($hasThumbnail) {
            // Extract directory and filename
            $directory = dirname($path);
            $filename = basename($path);
            $thumbPath = "{$directory}/thumbnails/thumb_{$filename}";
            
            Storage::disk('public')->delete($thumbPath);
        }

        return $deleted;
    }

    /**
     * Get image dimensions without loading full image.
     *
     * @param string $path Storage path
     * @return array ['width' => int, 'height' => int]
     */
    public function getImageDimensions(string $path): array
    {
        $fullPath = Storage::disk('public')->path($path);
        
        if (!file_exists($fullPath)) {
            return ['width' => 0, 'height' => 0];
        }

        [$width, $height] = getimagesize($fullPath);

        return compact('width', 'height');
    }

    /**
     * Optimize existing image in storage.
     *
     * @param string $path Storage path
     * @param int $quality Compression quality (1-100)
     * @return bool
     */
    public function optimizeExistingImage(string $path, int $quality = 85): bool
    {
        if (!Storage::disk('public')->exists($path)) {
            return false;
        }

        $fullPath = Storage::disk('public')->path($path);
        
        // Load image
        $image = $this->manager->read($fullPath);

        // Compress and save
        $encoded = $image->toJpeg(quality: $quality);
        Storage::disk('public')->put($path, $encoded);

        return true;
    }

    /**
     * Batch optimize images in a directory.
     *
     * @param string $directory Storage directory
     * @param int $quality Compression quality
     * @return array Statistics ['processed' => int, 'failed' => int, 'saved_bytes' => int]
     */
    public function batchOptimize(string $directory, int $quality = 85): array
    {
        $files = Storage::disk('public')->files($directory);
        $stats = ['processed' => 0, 'failed' => 0, 'saved_bytes' => 0];

        foreach ($files as $file) {
            if (!$this->isImage($file)) {
                continue;
            }

            $sizeBefore = Storage::disk('public')->size($file);
            
            if ($this->optimizeExistingImage($file, $quality)) {
                $sizeAfter = Storage::disk('public')->size($file);
                $stats['saved_bytes'] += ($sizeBefore - $sizeAfter);
                $stats['processed']++;
            } else {
                $stats['failed']++;
            }
        }

        return $stats;
    }

    /**
     * Check if file is an image.
     *
     * @param string $path
     * @return bool
     */
    protected function isImage(string $path): bool
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }
}
