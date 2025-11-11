<?php
// app/Http/Controllers/CmsController.php

namespace App\Http\Controllers;

use App\Models\CmsPage;

class CmsController extends Controller
{
    public function show($slug)
    {
        $cmsPage = CmsPage::where('slug', $slug)
            ->where('status', 'active')
            ->with('translations')
            ->firstOrFail();

        $translation = $cmsPage->translation();

        if (!$translation) {
            abort(404, __('Page not found'));
        }

        return view('cms.show', [
            'cmsPage' => $cmsPage,
            'title' => $translation->title,
            'content' => $translation->content,
            'metaTitle' => $translation->meta_title ?? $translation->title,
            'metaDescription' => $translation->meta_description
        ]);
    }
}
