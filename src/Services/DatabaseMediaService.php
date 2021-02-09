<?php

namespace Selene\Modules\MediaModule\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Selene\Modules\MediaModule\Models\Media;
use Selene\Modules\MediaModule\Models\MediaType;

class DatabaseMediaService
{
    public function upload(UploadedFile $file): Media
    {
        $path = $file->store(self::getUploadPath(), ['disk' => 'public']);

        return Media::create(
            [
                'path' => $path,
                'fullPath' => Storage::disk('public')->path($path),
                'file' => basename($path),
                'extension' => $file->extension(),
                'mimetype' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
    }

    public function createDashboardThumbnail(Media $media): MediaType
    {
        $img = Image::make($media->fullPath)->heighten(300);

        return new MediaType('dashboardthumbnail', $img);
    }

    public static function getUploadPath(): string
    {
        return 'media/' . now()->format('Y/m');
    }

    public function createMediaTypeFromImage(Media $media, UploadedFile $file, string $name): MediaType
    {
        $prefix = basename($media->file, '.' . $file->extension());
        $fileName = $prefix . '-' . $name . '.' . $file->extension();
        $path = $file->storeAs(self::getUploadPath(), $fileName, ['disk' => 'public']);
        $img = Image::make(Storage::disk('public')->path($path));
        $mediaType = new MediaType($name, $img);
        $mediaType->setNamePrefix($prefix);
        $media->addType($mediaType);
        return $mediaType;
    }

}
