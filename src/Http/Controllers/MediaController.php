<?php

namespace Selene\Modules\MediaModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Intervention\Image\Facades\Image as ImageFacade;
use Selene\Modules\MediaModule\Models\Media;
use Selene\Modules\MediaModule\Services\DatabaseMediaService;

class MediaController extends Controller
{
    public function index() {
        return view("MediaModule::index");
    }

    public function ajaxUpload(Request $request, DatabaseMediaService $databaseMediaService) {
        ini_set('memory_limit', '2048M');
        $media = $databaseMediaService->upload($request->file('file'));
        if($media->mimetype === 'image/jpg' || $media->mimetype === 'image/png' || $media->mimetype === 'image/jpeg' ) {
            $media->addType($databaseMediaService->createDashboardThumbnail($media));
        }

        return response()->json($media);
    }

    public function imageType(Media $media, string $mediatype) {
        if($mediatype === 'default') {
            return $this->image($media);
        }

        if($media->types && $media->types[$mediatype]) {
            $image = ImageFacade::make($media->types[$mediatype]['fullPath']);
            return $image->response();
        }

        abort(404);
    }

    public function image(Media $media) {
        if($media->extension === 'svg') {
            return response(file_get_contents($media->fullPath))->header('Content-Type', 'image/svg+xml');
        }

        if(strpos($media->mimetype, 'image')) {
            $image = ImageFacade::make($media->fullPath);
            return $image->response();
        }

        return response()->file($media->fullPath);
    }


    public function info(Media $media) {
        return response()->json($media);
    }

    public function editorSave(Request $request, DatabaseMediaService $databaseMediaService) {
        $media = Media::query()->find($request->media);
        return response()->json(
            $databaseMediaService->createMediaTypeFromImage($media, $request->image, $request->name)
        );
    }

    public function updateInfo(Request $request, Media $id) {
        $id->update([$request->attribute => (string) $request->value]);

        return response()->json(['status' => 'success']);
    }
}
