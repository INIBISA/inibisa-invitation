<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Imagick;

class TemplateController extends Controller
{
    public function __invoke(): View
    {
        $templates = Template::query()->withCount('invitations')->with('demo')->orderBy('name')->get();
        $demos = $templates->mapWithKeys(fn (Template $template) => [$template->id => $template->demo]);

        return view('admin.templates', compact('templates', 'demos'));
    }

    public function update(Request $request, Template $template): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:80'],
            'price' => ['required', 'integer', 'min:1000', 'max:100000000'],
            'is_active' => ['nullable', 'boolean'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        unset($data['thumbnail']);

        $previousThumbnail = $template->thumbnail;

        if ($request->hasFile('thumbnail')) {
            $image = new Imagick($request->file('thumbnail')->getRealPath());
            $image->autoOrient();
            $image->setImagePage(0, 0, 0, 0);

            if ($image->getImageWidth() > 1200) {
                $image->thumbnailImage(1200, 0);
            }

            $image->setImageFormat('webp');
            $image->setImageCompressionQuality(78);
            $image->stripImage();
            $path = 'template-thumbnails/'.$template->id.'/'.Str::uuid().'.webp';
            $stored = Storage::disk('public')->put($path, $image->getImagesBlob(), 'public');
            $image->clear();

            abort_unless($stored, 500, 'Thumbnail gagal disimpan.');

            $data['thumbnail'] = 'storage/'.$path;
        }

        $template->update([...$data, 'is_active' => $request->boolean('is_active')]);

        if ($request->hasFile('thumbnail') && is_string($previousThumbnail) && str_starts_with($previousThumbnail, 'storage/template-thumbnails/'.$template->id.'/')) {
            Storage::disk('public')->delete(Str::after($previousThumbnail, 'storage/'));
        }

        return back()->with('success', 'Template diperbarui.');
    }
}
