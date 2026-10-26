<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Imagick;

class TemplateCanvasImageController extends Controller
{
    public function __invoke(Request $request, Template $template): JsonResponse
    {
        abort_unless($template->isVisual(), 404);

        $request->validate(['image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120']]);

        $image = new Imagick($request->file('image')->getRealPath());
        $image->autoOrient();
        $image->setImagePage(0, 0, 0, 0);
        if ($image->getImageWidth() > 1800 || $image->getImageHeight() > 1800) {
            $image->thumbnailImage(1800, 1800, true);
        }
        $image->setImageFormat('webp');
        $image->setImageCompressionQuality(80);
        $image->stripImage();

        $path = 'template-canvas/'.$template->id.'/'.Str::uuid().'.webp';
        $stored = Storage::disk('public')->put($path, $image->getImagesBlob(), 'public');
        $image->clear();
        abort_unless($stored, 500, 'Gambar gagal disimpan.');

        return response()->json(['path' => 'storage/'.$path, 'url' => asset('storage/'.$path)]);
    }
}
