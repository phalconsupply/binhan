<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SystemSettingController extends Controller
{
    /**
     * Display the system settings page.
     */
    public function index()
    {
        $settingGroups = [
            'company' => 'Thông tin Công ty',
            'appearance' => 'Giao diện',
            'language' => 'Ngôn ngữ & Định dạng',
            'business' => 'Nghiệp vụ',
            'security' => 'Bảo mật',
            'maintenance' => 'Backup & Bảo trì',
            'system' => 'Hệ thống',
        ];

        $settings = [];
        foreach (array_keys($settingGroups) as $group) {
            $settings[$group] = SystemSetting::byGroup($group)
                ->orderBy('order')
                ->get();
        }

        return view('settings.index', compact('settingGroups', 'settings'));
    }

    /**
     * Update system settings.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $successCount = 0;
        $errors = [];

        foreach ($request->settings as $key => $value) {
            try {
                $setting = SystemSetting::where('key', $key)->first();
                
                if (!$setting) {
                    continue;
                }

                // Handle checkbox type (value will be null if unchecked)
                if ($setting->type === 'checkbox') {
                    $value = $value ? '1' : '0';
                }

                // Validate based on type
                if ($setting->type === 'number' && !is_numeric($value) && $value !== '') {
                    $errors[] = "Giá trị cho {$setting->description} phải là số";
                    continue;
                }

                if ($setting->type === 'email' && $value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Email không hợp lệ cho {$setting->description}";
                    continue;
                }

                if ($setting->type === 'url' && $value && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $errors[] = "URL không hợp lệ cho {$setting->description}";
                    continue;
                }

                $setting->value = $value;
                $setting->save();
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Lỗi khi lưu {$key}: " . $e->getMessage();
            }
        }

        // Clear cache after updating
        SystemSetting::clearCache();

        // Get active tab to redirect back to it
        $activeTab = $request->input('active_tab', 'company');

        if (!empty($errors)) {
            return redirect()->route('settings.index', ['tab' => $activeTab])
                ->with('warning', "Đã lưu {$successCount} cấu hình. Có " . count($errors) . " lỗi.")
                ->with('errors', $errors);
        }

        return redirect()->route('settings.index', ['tab' => $activeTab])
            ->with('success', "Đã cập nhật {$successCount} cấu hình thành công!");
    }

    /**
     * Upload file (logo, favicon, images).
     */
    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|exists:system_settings,key',
            'file' => 'required|file|max:2048|mimes:jpg,jpeg,png,ico',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $setting = SystemSetting::where('key', $request->key)->first();

            if (!$setting->isFileType()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cấu hình này không phải loại file/image',
                ], 400);
            }

            // Delete old file if exists
            if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                Storage::disk('public')->delete($setting->value);
            }

            // Store new file
            $path = $request->file('file')->store('settings', 'public');
            
            $setting->value = $path;
            $setting->save();

            return response()->json([
                'success' => true,
                'message' => 'Upload file thành công',
                'url' => $setting->getFileUrl(),
                'path' => $path,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete uploaded file.
     */
    public function deleteFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|exists:system_settings,key',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $setting = SystemSetting::where('key', $request->key)->first();

            if (!$setting->isFileType()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cấu hình này không phải loại file/image',
                ], 400);
            }

            // Delete file from storage
            if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                Storage::disk('public')->delete($setting->value);
            }

            $setting->value = null;
            $setting->save();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa file thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get setting value by key (for AJAX).
     */
    public function getValue(Request $request)
    {
        $key = $request->get('key');
        $setting = SystemSetting::where('key', $key)->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy cấu hình',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'value' => $setting->value,
            'type' => $setting->type,
        ]);
    }
}
