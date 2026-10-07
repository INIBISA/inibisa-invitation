<?php

namespace App\Services;

use App\Models\TemplateDemo;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Imagick;

class TemplateDemoMediaManager
{
    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function syncFromRequest(TemplateDemo $demo, Request $request, array $data): array
    {
        $media = collect($data['media'] ?? [])->groupBy('collection');
        $singleImages = ['cover' => 'cover', 'groom_photo' => 'groom', 'bride_photo' => 'bride', 'qris' => 'qris'];

        foreach ($request->input('remove_media', []) as $collection) {
            $this->deleteFiles($media->get($collection, collect())->all());
            $media->forget($collection);
        }

        foreach ($singleImages as $input => $collection) {
            if ($request->hasFile($input)) {
                $created = $this->storeImages($demo, [$request->file($input)], $collection);
                $this->deleteFiles($media->get($collection, collect())->all());
                $media->put($collection, collect($created));
            }
        }

        foreach (['gallery' => 'gallery', 'story_photos' => 'story'] as $input => $collection) {
            if ($request->hasFile($input)) {
                $created = $this->storeImages($demo, $request->file($input), $collection);
                $this->deleteFiles($media->get($collection, collect())->all());
                $media->put($collection, collect($created));
            }
        }

        $data['media'] = $media->flatten(1)->values()->all();

        return $data;
    }

    /** @param array<int, UploadedFile> $files
     * @return array<int, array{collection: string, file_path: string, width: int, height: int, sort_order: int}>
     */
    private function storeImages(TemplateDemo $demo, array $files, string $collection): array
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
            $path = 'template-demos/'.$demo->id.'/'.Str::uuid().'.webp';
            $stored = Storage::disk('public')->put($path, $image->getImagesBlob(), 'public');

            abort_unless($stored, 500, 'Media demo gagal disimpan.');

            $created[] = [
                'collection' => $collection,
                'file_path' => $path,
                'width' => $image->getImageWidth(),
                'height' => $image->getImageHeight(),
                'sort_order' => $sortOrder,
            ];
            $image->clear();
        }

        return $created;
    }

    /** @param array<int, array<string, mixed>> $media */
    private function deleteFiles(array $media): void
    {
        Storage::disk('public')->delete(collect($media)->pluck('file_path')->filter()->all());
    }
}
