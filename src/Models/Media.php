<?php

namespace Selene\Modules\MediaModule\Models;

use Illuminate\Support\Facades\Storage;
use Jenssegers\Mongodb\Eloquent\Model;

/**
 * @property mixed types
 */
class Media extends Model
{
    protected $connection = 'mongodb';

    protected $attributes = [
      'alt' => '',
      'title' => '',
      'description' => ''
    ];
    protected $fillable = [
        'file',
        'path',
        'fullPath',
        'extension',
        'mimetype',
        'size',
        'alt',
        'title',
        'description'
    ];

    public function addType(MediaType $mediaType)
    {
        if (!$this->types) {
            $this->types = [];
        }

        $newTypes = $this->types;

        $newTypes[$mediaType->name()] = (object) $mediaType->toArray();

        $this->types = $newTypes;
        $mediaType->save();
        $this->save();
    }

    public function removeType(string $mediaType)
    {
        if (!$this->types) {
            return false;
        }
        if (!isset($this->types[$mediaType])) {
            return false;
        }
        if (!Storage::disk('public')->delete($this->types[$mediaType]['path'])) {
            return false;
        }
        $types = $this->types;

        unset($types[$mediaType]);

        $this->types = $types;
        return $this->save();
    }


}
