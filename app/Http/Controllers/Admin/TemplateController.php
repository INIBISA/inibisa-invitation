<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator as LaravelValidator;
use Illuminate\View\View;
use Imagick;

class TemplateController extends Controller
{
    public function __invoke(): View
    {
        $templates = Template::query()
            ->withCount('invitations')
            ->with([
                'demo',
                'designRevisions' => fn ($query) => $query->with('creator')->limit(5),
            ])
            ->orderBy('name')
            ->get();
        $demos = $templates->mapWithKeys(fn (Template $template) => [$template->id => $template->demo]);

        return view('admin.templates', compact('templates', 'demos'));
    }

    public function update(Request $request, Template $template): RedirectResponse
    {
        $orderableSections = array_keys($template->orderableSections());
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:80'],
            'price' => ['required', 'integer', 'min:1000', 'max:100000000'],
            'is_active' => ['nullable', 'boolean'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'design_settings' => ['sometimes', 'array:primary_color,text_color,background_color,heading_font,body_font,card_radius,hero_overlay,sections,section_order'],
            'design_settings.primary_color' => ['required_with:design_settings', 'hex_color'],
            'design_settings.text_color' => ['required_with:design_settings', 'hex_color'],
            'design_settings.background_color' => ['required_with:design_settings', 'hex_color'],
            'design_settings.heading_font' => ['required_with:design_settings', Rule::in(['serif', 'sans', 'monospace'])],
            'design_settings.body_font' => ['required_with:design_settings', Rule::in(['sans', 'serif'])],
            'design_settings.card_radius' => ['required_with:design_settings', 'integer', 'between:0,32'],
            'design_settings.hero_overlay' => ['required_with:design_settings', 'integer', 'between:0,80'],
            'design_settings.sections' => ['sometimes', 'array:quote,countdown,events,story,gallery,rsvp,gift,wishes'],
            'design_settings.sections.*' => ['boolean'],
            'design_settings.section_order' => ['required_with:design_settings', 'array', 'size:'.count($orderableSections)],
            'design_settings.section_order.*' => ['string', 'distinct', Rule::in($orderableSections)],
        ]);
        $validator->after(function (LaravelValidator $validator) use ($request, $template): void {
            $textColor = $request->input('design_settings.text_color');
            $backgroundColor = $request->input('design_settings.background_color');

            if (is_string($textColor) && is_string($backgroundColor) && self::contrastRatio($textColor, $backgroundColor) < 4.5) {
                $validator->errors()->add('design_settings.text_color', 'Kontras warna teks dan latar minimal harus 4.5:1.');
            }

            if ($request->boolean('is_active') && ! ViewFacade::exists($template->view_path)) {
                $validator->errors()->add('is_active', 'Template tidak dapat diaktifkan karena file view belum tersedia.');
            }
        });
        $data = $validator->validate();
        unset($data['thumbnail']);
        $designSettings = $data['design_settings'] ?? null;
        unset($data['design_settings']);

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

        $template->update([
            ...$data,
            'design_draft_settings' => $designSettings ?? $template->design_draft_settings,
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->hasFile('thumbnail') && is_string($previousThumbnail) && str_starts_with($previousThumbnail, 'storage/template-thumbnails/'.$template->id.'/')) {
            Storage::disk('public')->delete(Str::after($previousThumbnail, 'storage/'));
        }

        return back()->with('success', $designSettings ? 'Template dan draft desain disimpan.' : 'Template diperbarui.');
    }

    private static function contrastRatio(string $firstColor, string $secondColor): float
    {
        $luminance = static function (string $color): float {
            if (! preg_match('/^#[0-9a-f]{6}$/i', $color)) {
                return 1;
            }

            $channels = array_map(
                static function (string $channel): float {
                    $value = hexdec($channel) / 255;

                    return $value <= 0.04045 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
                },
                str_split(substr($color, 1), 2),
            );

            return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
        };

        [$lighter, $darker] = [max($luminance($firstColor), $luminance($secondColor)), min($luminance($firstColor), $luminance($secondColor))];

        return ($lighter + 0.05) / ($darker + 0.05);
    }
}
