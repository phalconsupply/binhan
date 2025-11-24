<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Auth;

class MediaController extends Controller
{
    /**
     * Display media library.
     */
    public function index(Request $request)
    {
        $collection = $request->get('collection', 'default');
        
        // Get all media for current user or all users (admin)
        if (Auth::user()->hasRole(['admin', 'manager'])) {
            $media = Media::when($collection !== 'all', function ($query) use ($collection) {
                    return $query->where('collection_name', $collection);
                })
                ->with('model')
                ->latest()
                ->paginate(24);
        } else {
            $media = Auth::user()
                ->getMedia($collection !== 'all' ? $collection : null)
                ->sortByDesc('created_at')
                ->take(24);
        }

        $collections = [
            'all' => 'Tất cả',
            'avatars' => 'Avatar',
            'logos' => 'Logo',
            'documents' => 'Tài liệu',
            'images' => 'Hình ảnh',
            'default' => 'Khác',
        ];

        return view('media.index', compact('media', 'collections', 'collection'));
    }

    /**
     * Upload new media.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'collection' => 'nullable|string|in:avatars,logos,documents,images,default',
        ]);

        $collection = $request->get('collection', 'default');
        
        try {
            $media = Auth::user()
                ->addMedia($request->file('file'))
                ->toMediaCollection($collection);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Upload thành công!',
                    'media' => [
                        'id' => $media->id,
                        'name' => $media->name,
                        'file_name' => $media->file_name,
                        'size' => $media->human_readable_size,
                        'url' => $media->getUrl(),
                        'type' => $media->mime_type,
                    ],
                ]);
            }

            return redirect()->back()->with('success', 'Upload thành công!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Delete media.
     */
    public function destroy($id)
    {
        try {
            $media = Media::findOrFail($id);
            
            // Check permission
            if (!Auth::user()->hasRole(['admin', 'manager']) && $media->model_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa file này.',
                ], 403);
            }

            $media->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa file thành công!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get media info.
     */
    public function show($id)
    {
        try {
            $media = Media::with('model')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'media' => [
                    'id' => $media->id,
                    'name' => $media->name,
                    'file_name' => $media->file_name,
                    'mime_type' => $media->mime_type,
                    'size' => $media->human_readable_size,
                    'collection' => $media->collection_name,
                    'url' => $media->getUrl(),
                    'created_at' => $media->created_at->format('d/m/Y H:i'),
                    'uploader' => $media->model ? $media->model->name : 'N/A',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy file.',
            ], 404);
        }
    }

    /**
     * Download media.
     */
    public function download($id)
    {
        try {
            $media = Media::findOrFail($id);
            return response()->download($media->getPath(), $media->file_name);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không tìm thấy file.');
        }
    }
}
