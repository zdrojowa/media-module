<?php

namespace Selene\Modules\MediaModule\Models;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;

class MediaType
{
    private string $name;

    private Image $media;

    private string $namePrefix;

    public string $file;

    public string $fullPath;

    public string $path;

    public string $extension;

    public string $mimetype;

    public int $size;

    public function __construct(string $name, Image $image)
    {
        $this->name = $name;
        $this->media = $image;
        $this->namePrefix = $this->media->filename;
        $this->file = $this->getNewName();
        $this->fullPath = $this->getNewPath();
        $this->mimetype = $this->media->mime();
        $this->size = $this->media->filesize();
        $this->extension = explode('/', $this->mimetype)[1];
        $this->path = str_replace(Storage::disk('public')->getAdapter()->getPathPrefix(), '', $this->fullPath);
    }

    private function getNewPath() {
        return $this->media->dirname . '/' . $this->getNewName();
    }

    private function getNewName() {
        return $this->namePrefix . '-'. $this->name . '.' . $this->media->extension;
    }

    public function save() {
        $this->media->save($this->getNewPath());
    }

    public function setNamePrefix(string $namePrefix) {
        $this->namePrefix = $namePrefix;
        $this->file = $this->getNewName();
        $this->fullPath = $this->getNewPath();
        $this->path = str_replace(Storage::disk('public')->getAdapter()->getPathPrefix(), '', $this->fullPath);
    }

    public function name() {
        return $this->name;
    }

    public function toArray() {
        return [
          'file' => $this->file,
          'fullPath' => $this->fullPath,
          'path' => $this->path,
          'extension' => $this->extension,
          'mimetype' => $this->mimetype,
          'size' => $this->size
        ];
    }


}
