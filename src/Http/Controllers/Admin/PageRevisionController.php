<?php

namespace Dcodegroup\PageBuilder\Http\Controllers\Admin;

use Dcodegroup\PageBuilder\Models\Page;
use Dcodegroup\PageBuilder\Models\PageRevision;
use Dcodegroup\PageBuilder\Services\PageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PageRevisionController extends Controller
{
    public function index(Page $page): View
    {
        $revisions = $page->revisions()->paginate(20);

        return view('admin.cms.revisions.index', compact('page', 'revisions'));
    }

    public function restore(PageRevision $revision): RedirectResponse
    {
        PageService::restoreRevision($revision);

        return redirect()->route('admin.pages.edit', $revision->page)
                         ->with('status', 'Page revision content was restored');
    }

    public function destroy(PageRevision $revision): RedirectResponse
    {
        PageService::deleteRevision($revision);

        return back()->with('status', 'Page revision was deleted');
    }
}