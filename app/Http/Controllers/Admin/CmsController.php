<?php
// app/Http/Controllers/Admin/CmsController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\CmsPageTranslation;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CmsController extends Controller
{
    // List all CMS pages
    public function index()
    {
        if (!auth('admin')->user()->hasPermission('cms.view')) {
            abort(403, __('Unauthorized access'));
        }

        $cmsPages = CmsPage::with('translations')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('admin.cms.index', [
            'cmsPages' => $cmsPages,
            'title' => __('CMS Pages')
        ]);
    }

    // Show create form
    public function create()
    {
        if (!auth('admin')->user()->hasPermission('cms.create')) {
            abort(403, __('Unauthorized access'));
        }

        $locales = ['en', 'es'];

        return view('admin.cms.create', [
            'locales' => $locales,
            'title' => __('Create CMS Page')
        ]);
    }

    // Store new CMS page
    public function store(Request $request)
    {
        if (!auth('admin')->user()->hasPermission('cms.create')) {
            abort(403, __('Unauthorized access'));
        }

        $validated = $request->validate([
            'slug' => 'required|string|max:255|unique:cms_pages,slug',
            'status' => 'required|in:active,inactive',
            'translations' => 'required|array|min:1',
            'translations.*.locale' => 'required|in:en,es,ta',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.content' => 'required|string',
            'translations.*.meta_title' => 'nullable|string|max:255',
            'translations.*.meta_description' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            // Create CMS page
            $cmsPage = CmsPage::create([
                'slug' => Str::slug($validated['slug']),
                'status' => $validated['status']
            ]);

            // Create translations
            foreach ($validated['translations'] as $translation) {
                $cmsPage->translations()->create($translation);
            }

            // Log activity
            AdminActivityLog::create([
                'admin_id' => auth('admin')->id(),
                'action' => 'created',
                'module' => 'CmsPage',
                'model_id' => $cmsPage->id,
                'description' => 'Created CMS page: ' . $cmsPage->slug
            ]);

            DB::commit();

            return redirect()->route('admin.cms.index')
                ->with('success', __('CMS page created successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', __('Failed to create CMS page'));
        }
    }

    // Show edit form
    public function edit($id)
    {
        if (!auth('admin')->user()->hasPermission('cms.edit')) {
            abort(403, __('Unauthorized access'));
        }

        $cmsPage = CmsPage::with('translations')->findOrFail($id);
        $locales = ['en', 'es'];

        return view('admin.cms.edit', [
            'cmsPage' => $cmsPage,
            'locales' => $locales,
            'title' => __('Edit CMS Page')
        ]);
    }

    // Update CMS page
    public function update(Request $request, $id)
    {
        if (!auth('admin')->user()->hasPermission('cms.edit')) {
            abort(403, __('Unauthorized access'));
        }

        $cmsPage = CmsPage::findOrFail($id);

        $validated = $request->validate([
            'slug' => 'required|string|max:255|unique:cms_pages,slug,' . $id,
            'status' => 'required|in:active,inactive',
            'translations' => 'required|array|min:1',
            'translations.*.locale' => 'required|in:en,es,ta',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.content' => 'required|string',
            'translations.*.meta_title' => 'nullable|string|max:255',
            'translations.*.meta_description' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            // Update CMS page
            $cmsPage->update([
                'slug' => Str::slug($validated['slug']),
                'status' => $validated['status']
            ]);

            // Delete old translations
            $cmsPage->translations()->delete();

            // Create new translations
            foreach ($validated['translations'] as $translation) {
                $cmsPage->translations()->create($translation);
            }

            // Log activity
            AdminActivityLog::create([
                'admin_id' => auth('admin')->id(),
                'action' => 'updated',
                'module' => 'CmsPage',
                'model_id' => $cmsPage->id,
                'description' => 'Updated CMS page: ' . $cmsPage->slug
            ]);

            DB::commit();

            return redirect()->route('admin.cms.index')
                ->with('success', __('CMS page updated successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', __('Failed to update CMS page'));
        }
    }

    // Toggle status
    public function toggleStatus($id)
    {
        if (!auth('admin')->user()->hasPermission('cms.edit')) {
            abort(403, __('Unauthorized access'));
        }

        $cmsPage = CmsPage::findOrFail($id);
        $cmsPage->status = $cmsPage->status === 'active' ? 'inactive' : 'active';
        $cmsPage->save();

        // Log activity
        AdminActivityLog::create([
            'admin_id' => auth('admin')->id(),
            'action' => 'status_changed',
            'module' => 'CmsPage',
            'model_id' => $cmsPage->id,
            'description' => 'Changed CMS page status to: ' . $cmsPage->status
        ]);

        return back()->with('success', __('Status updated successfully'));
    }

    // Delete CMS page
    public function destroy($id)
    {
        if (!auth('admin')->user()->hasPermission('cms.delete')) {
            abort(403, __('Unauthorized access'));
        }

        $cmsPage = CmsPage::findOrFail($id);
        $slug = $cmsPage->slug;

        DB::beginTransaction();
        try {
            $cmsPage->translations()->delete();
            $cmsPage->delete();

            // Log activity
            AdminActivityLog::create([
                'admin_id' => auth('admin')->id(),
                'action' => 'deleted',
                'module' => 'CmsPage',
                'model_id' => $id,
                'description' => 'Deleted CMS page: ' . $slug
            ]);

            DB::commit();

            return redirect()->route('admin.cms.index')
                ->with('success', __('CMS page deleted successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Failed to delete CMS page'));
        }
    }
}
