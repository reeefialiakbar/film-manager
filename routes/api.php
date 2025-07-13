<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/drives', function () {
    $drives = [];
    if (PHP_OS === 'WINNT') {
        foreach (range('A', 'Z') as $letter) {
            $path = $letter . ':\\';
            if (file_exists($path)) {
                $drives[] = [
                    'path' => $path,
                    'label' => $letter . ':'
                ];
            }
        }
    }
    return response()->json($drives);
});

Route::get('/default-folders', function () {
    // این مسیرها را می‌توانید از دیتابیس یا فایل کانفیگ بخوانید
    return response()->json([
        ['path' => 'D:\\Movies', 'name' => 'پوشه فیلم‌ها'],
        ['path' => 'E:\\Series', 'name' => 'پوشه سریال‌ها'],
    ]);
});

Route::post('/transfer-file', function (Request $request) {
    $sourcePath = $request->input('sourcePath');
    $destinationPath = $request->input('destinationPath');
    $defaultFolder = $request->input('defaultFolder');

    try {
        $finalPath = $defaultFolder ?: $destinationPath;
        if (!is_dir($finalPath)) {
            mkdir($finalPath, 0777, true);
        }

        $fileName = basename($sourcePath);
        $success = copy($sourcePath, $finalPath . DIRECTORY_SEPARATOR . $fileName);

        return response()->json([
            'success' => $success,
            'error' => $success ? null : 'خطا در کپی فایل'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});
