<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveTemplateCanvasRequest;
use App\Models\Template;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TemplateCanvasController extends Controller
{
    public function edit(Template $template): View
    {
        abort_unless($template->isVisual(), 404);

        return view('admin.template-canvas', [
            'template' => $template,
            'design' => $template->draftDesignSettings(),
            'sections' => ['cover' => 'Cover', ...$template->orderableSections(), 'closing' => 'Penutup'],
        ]);
    }

    public function update(SaveTemplateCanvasRequest $request, Template $template): RedirectResponse
    {
        abort_unless($template->isVisual(), 404);

        $validated = $request->validated();
        $design = $template->draftDesignSettings();
        $design['canvas'] = $validated['canvas'];
        $design['section_order'] = $validated['section_order'];
        $design['sections'] = array_replace($design['sections'], $validated['sections']);
        $design['primary_color'] = $validated['primary_color'];
        $design['text_color'] = $validated['text_color'];
        $design['background_color'] = $validated['background_color'];
        $design['heading_font'] = $validated['heading_font'];
        $design['body_font'] = $validated['body_font'];

        $template->update(['design_draft_settings' => $design]);

        return back()->with('success', 'Draft desain tersimpan. Lihat pratinjau sebelum diterbitkan.');
    }
}
