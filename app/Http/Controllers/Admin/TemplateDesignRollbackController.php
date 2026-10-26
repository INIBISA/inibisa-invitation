<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\TemplateDesignRevision;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class TemplateDesignRollbackController extends Controller
{
    public function __invoke(Template $template, TemplateDesignRevision $revision): RedirectResponse
    {
        abort_unless($revision->template_id === $template->id, 404);

        DB::transaction(function () use ($template, $revision): void {
            $lockedTemplate = Template::query()->lockForUpdate()->findOrFail($template->id);
            $lockedRevision = TemplateDesignRevision::query()
                ->whereBelongsTo($lockedTemplate)
                ->findOrFail($revision->id);

            $lockedTemplate->designRevisions()->create([
                'created_by' => auth()->id(),
                'settings' => $lockedTemplate->designSettings(),
            ]);
            $lockedTemplate->update([
                'design_settings' => $lockedRevision->settings,
                'design_draft_settings' => null,
            ]);
        });

        return back()->with('success', 'Desain berhasil dikembalikan.');
    }
}
