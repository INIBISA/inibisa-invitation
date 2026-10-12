<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadInvitationImageRequest;
use App\Models\TemplateDemo;
use App\Services\TemplateDemoMediaManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class DemoMediaUploadController extends Controller
{
    public function __invoke(UploadInvitationImageRequest $request, TemplateDemo $demo, TemplateDemoMediaManager $mediaManager): JsonResponse
    {
        $collection = $request->string('collection')->toString();
        $replace = $request->boolean('replace') || ! in_array($collection, ['gallery', 'story'], true);
        $limit = $collection === 'gallery' ? 12 : ($collection === 'story' ? 10 : 1);
        $currentCount = collect($demo->data['media'] ?? [])->where('collection', $collection)->count();

        if (! $replace && $currentCount >= $limit) {
            throw ValidationException::withMessages(['file' => 'Maksimal '.$limit.' foto untuk kategori ini.']);
        }

        $media = $mediaManager->uploadImage($demo, $collection, $request->file('file'), $replace);

        return response()->json([
            'message' => 'Foto berhasil diunggah.',
            'url' => asset('storage/'.$media['file_path']),
            'collection' => $collection,
        ]);
    }
}
