<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use App\Models\MediaFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MediaController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Media/Index');
    }

    // API structure for Vue component
    public function getFolders(Request $request)
    {
        $parent_id = $request->input('parent_id', null);

        // Root folders
        $folders = MediaFolder::where('parent_id', $parent_id)->orderBy('name')->get();

        // Assets in this folder
        $assets = MediaAsset::where('folder_id', $parent_id)->latest()->get();

        // Path (Breadcrumb)
        $path = [];
        $current = $parent_id ? MediaFolder::find($parent_id) : null;
        while ($current) {
            array_unshift($path, $current);
            $current = $current->parent;
        }

        return response()->json([
            'folders' => $folders,
            'assets' => $assets,
            'path' => $path
        ]);
    }

    public function storeFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:media_folders,id'
        ]);

        $folder = MediaFolder::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . uniqid(),
            'parent_id' => $request->parent_id
        ]);

        return response()->json($folder);
    }

    public function storeAsset(Request $request)
    {
        $request->validate([
            'file' => 'required|file|image|max:10240', // 10MB max, image only
            'folder_id' => 'nullable|exists:media_folders,id',
        ]);

        $file = $request->file('file');

        $path = $file->store('media_library', 'public');

        $asset = MediaAsset::create([
            'folder_id' => $request->folder_id,
            'disk' => 'public',
            'original_name' => $file->getClientOriginalName(),
            'original_path' => $path,
            'mime_type' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
            'status' => 'ready'
        ]);

        return response()->json($asset);
    }

    public function destroyFolder($id)
    {
        $folder = MediaFolder::findOrFail($id);
        if ($folder->children()->count() > 0 || $folder->assets()->count() > 0) {
            return response()->json(['error' => 'Folder is not empty.'], 403);
        }
        $folder->delete();
        return response()->json(['success' => true]);
    }

    public function destroyAsset($id)
    {
        $asset = MediaAsset::findOrFail($id);

        if (Storage::disk($asset->disk)->exists($asset->original_path)) {
            Storage::disk($asset->disk)->delete($asset->original_path);
        }

        $asset->delete();
        return response()->json(['success' => true]);
    }
}
