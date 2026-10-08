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
            [$contents, $width, $height] = $this->encodeImageAsWebp($file);
            $filePath = 'invitations/'.$invitation->id.'/'.Str::uuid().'.webp';
            $stored = Storage::disk('public')->put($filePath, $contents, 'public');

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

    private function encodeImageAsWebp(UploadedFile $file): array
    {
        if (class_exists(Imagick::class)) {
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
            $contents = $image->getImagesBlob();
            $image->clear();

            return [$contents, $width, $height];
        }

        return $this->encodeImageWithGd($file);
    }

    private function encodeImageWithGd(UploadedFile $file): array
    {
        $source = match ($file->getMimeType()) {
            'image/jpeg' => imagecreatefromjpeg($file->getRealPath()),
            'image/png' => imagecreatefrompng($file->getRealPath()),
            'image/webp' => imagecreatefromwebp($file->getRealPath()),
            default => false,
        };

        abort_unless($source, 422, 'Format gambar tidak didukung.');

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $width = min($sourceWidth, 1200);
        $height = (int) round($sourceHeight * ($width / $sourceWidth));

        $canvas = imagecreatetruecolor($width, $height);
        imagepalettetotruecolor($source);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);

        ob_start();
        imagewebp($canvas, null, 78);
        $contents = ob_get_clean();

        imagedestroy($source);
        imagedestroy($canvas);

        abort_unless(is_string($contents) && $contents !== '', 500, 'Media gagal diproses.');

        return [$contents, $width, $height];
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
