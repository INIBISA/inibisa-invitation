<?php

namespace App\Services;

use App\Models\Invitation;
use App\Models\InvitationMedia;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Imagick;

class InvitationMediaManager
{
    public function syncFromRequest(Invitation $invitation, Request $request): void
    {
        $singleImages = [
            'cover' => 'cover',
            'groom_photo' => 'groom',
            'bride_photo' => 'bride',
            'qris' => 'qris',
        ];

        foreach ($singleImages as $input => $collection) {
            if ($request->hasFile($input)) {
                $this->replaceImages($invitation, $collection, [$request->file($input)]);
            }
        }

        if ($request->hasFile('gallery')) {
            $this->replaceImages($invitation, 'gallery', $request->file('gallery'));
        }

        if ($request->hasFile('story_photos')) {
            $this->replaceImages($invitation, 'story', $request->file('story_photos'));
        }

        if ($request->hasFile('music')) {
            $this->replaceMusic($invitation, $request->file('music'));
        }
    }

    public function deleteAll(Invitation $invitation): void
    {
        Storage::disk('public')->deleteDirectory('invitations/'.$invitation->id);
    }

    /** @param array<int, UploadedFile> $files */
    private function replaceImages(Invitation $invitation, string $collection, array $files): void
    {
        $created = [];

        foreach (array_values($files) as $sortOrder => $file) {
            $image = new Imagick($file->getRealPath());
            $image->autoOrient();
            $image->setImagePage(0, 0, 0, 0);

            if ($image->getImageWidth() > 1200) {
                $image->thumbnailImage(1200, 0);
            }

            $image->setImageFormat('webp');
            $image->setImageCompressionQuality(78);
            $image->stripImage();
            $width = $image->getImageWidth();
            $height = $image->getImageHeight();
            $filePath = 'invitations/'.$invitation->id.'/'.Str::uuid().'.webp';
            $stored = Storage::disk('public')->put($filePath, $image->getImagesBlob(), 'public');
            $image->clear();

            abort_unless($stored, 500, 'Media gagal disimpan.');

            $created[] = [
                'collection' => $collection,
                'file_path' => $filePath,
                'width' => $width,
                'height' => $height,
                'sort_order' => $sortOrder,
            ];
        }

        $this->removeCollection($invitation, $collection);
        $invitation->media()->createMany($created);
    }

    private function replaceMusic(Invitation $invitation, UploadedFile $file): void
    {
        $path = $file->storePubliclyAs(
            'invitations/'.$invitation->id,
            Str::uuid().'.'.$file->guessExtension(),
            'public',
        );

        $this->removeCollection($invitation, 'music');
        $invitation->media()->create([
            'collection' => 'music',
            'file_path' => $path,
            'sort_order' => 0,
        ]);
    }

    private function removeCollection(Invitation $invitation, string $collection): void
    {
        $media = $invitation->media()->where('collection', $collection)->get();
        Storage::disk('public')->delete($media->pluck('file_path')->all());
        InvitationMedia::query()->whereKey($media->modelKeys())->delete();
    }
}
