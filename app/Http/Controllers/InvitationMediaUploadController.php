<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadInvitationImageRequest;
use App\Models\Invitation;
use App\Services\InvitationMediaManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class InvitationMediaUploadController extends Controller
{
    public function __invoke(UploadInvitationImageRequest $request, Invitation $invitation, InvitationMediaManager $mediaManager): JsonResponse
    {
        $collection = $request->string('collection')->toString();
        $replace = $request->boolean('replace') || ! in_array($collection, ['gallery', 'story'], true);
        $limit = $collection === 'gallery' ? 12 : ($collection === 'story' ? 10 : 1);

        if (! $replace && $invitation->media()->where('collection', $collection)->count() >= $limit) {
            throw ValidationException::withMessages(['file' => 'Maksimal '.$limit.' foto untuk kategori ini.']);
        }

        $media = $mediaManager->uploadImage($invitation, $collection, $request->file('file'), $replace);

        return response()->json([
            'message' => 'Foto berhasil diunggah.',
            'url' => asset('storage/'.$media->file_path),
            'collection' => $collection,
        ]);
    }
}
