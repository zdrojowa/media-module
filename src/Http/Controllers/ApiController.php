<?php

namespace Selene\Modules\MediaModule\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Selene\Modules\MediaModule\Models\Media;

class ApiController extends Controller
{
    public function get(Media $media, string $mediatype = null) {
        return response()->json($media);
    }

    public function files(Request $request): JsonResponse
    {
        $media = Media::query()->orderByDesc('created_at');
        $search = $request->get('search');
        if (!empty($search)) {
            $media->where(function($query) use ($search) {
                $query->where('_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('title', 'LIKE', '%' . $search . '%')
                    ->orWhere('alt', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%')
                    ->orWhere('extension', 'LIKE', '%' . $search . '%');
            });
        }
        return response()->json($media->get());
    }

    public function delete(Media $media, string $mediatype = null) {
        if ($mediatype) {
            $media->removeType($mediatype);
        } else {
            if ($media->types) {
                foreach($media->types as $type) {
                    Storage::disk('public')->delete($type['path']);
                }
            }
            if (Storage::disk('public')->delete($media->path)) {
                $media->delete();
            } else {
                response()->json(['status' => 'error'], JsonResponse::HTTP_BAD_REQUEST);
            }
        }
        return response()->json(['status' => 'ok']);
    }
}
