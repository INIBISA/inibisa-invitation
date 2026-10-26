<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class TemplateDesignPublicationController extends Controller
{
    public function store(Template $template): RedirectResponse
    {
        DB::transaction(function () use ($template): void {
            $lockedTemplate = Template::query()->lockForUpdate()->findOrFail($template->id);
            abort_if($lockedTemplate->design_draft_settings === null, 422, 'Draft desain belum tersedia.');

            $lockedTemplate->designRevisions()->create([
                'created_by' => auth()->id(),
                'settings' => $lockedTemplate->designSettings(),
            ]);
            $lockedTemplate->update([
                'design_settings' => $lockedTemplate->design_draft_settings,
                'design_draft_settings' => null,
            ]);
        });

        return back()->with('success', 'Draft desain diterbitkan.');
    }
}
