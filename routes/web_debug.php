<?php
// Debug route - xóa sau khi test xong
Route::get('/debug-permissions', function () {
    $user = auth()->user();
    
    if (!$user) {
        return 'User not authenticated';
    }
    
    return [
        'user_id' => $user->id,
        'email' => $user->email,
        'roles' => $user->getRoleNames(),
        'permissions' => $user->getAllPermissions()->pluck('name'),
        'can_view_vehicles' => $user->can('view vehicles'),
        'can_view_incidents' => $user->can('view incidents'),
        'can_view_transactions' => $user->can('view transactions'),
        'can_view_patients' => $user->can('view patients'),
        'can_view_reports' => $user->can('view reports'),
    ];
});
