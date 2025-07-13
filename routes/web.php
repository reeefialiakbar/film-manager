<?php

use App\Http\Controllers\MovieController;
use Illuminate\Support\Facades\Route;

// مسیرهای قبلی
Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

// مسیرهای فیلم
Route::get('/movies/iranian', [MovieController::class, 'iranianIndex']);
Route::post('/movies', [MovieController::class, 'store']);

// مسیرهای جدید برای درایوها و پوشه‌ها
Route::get('/get-drives', function () {
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

Route::get('/get-default-folders', function () {
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

Route::get('/get-folders-files', function (\Illuminate\Http\Request $request) {
    $path = $request->input('path');
    $result = [
        'folders' => [],
        'files' => []
    ];
    if ($path && is_dir($path)) {
        foreach (scandir($path) as $item) {
            if ($item == '.' || $item == '..') continue;
            $fullPath = $path . DIRECTORY_SEPARATOR . $item;
            if (is_dir($fullPath)) {
                $result['folders'][] = [
                    'name' => $item,
                    'path' => $fullPath
                ];
            } else {
                $result['files'][] = [
                    'name' => $item,
                    'path' => $fullPath
                ];
            }
        }
    }
    return response()->json($result);
});

// دریافت محتویات پوشه
Route::get('/get-folders-files', function (Request $request) {
    $path = $request->input('path');
    $result = [
        'folders' => [],
        'files' => []
    ];
    if ($path && is_dir($path)) {
        foreach (scandir($path) as $item) {
            if ($item == '.' || $item == '..') continue;
            $fullPath = $path . DIRECTORY_SEPARATOR . $item;
            if (is_dir($fullPath)) {
                $result['folders'][] = [
                    'name' => $item,
                    'path' => $fullPath
                ];
            } else {
                $result['files'][] = [
                    'name' => $item,
                    'path' => $fullPath
                ];
            }
        }
    }
    return response()->json($result);
});

// ساخت پوشه جدید
Route::post('/create-folder', function (Request $request) {
    $parent = $request->input('parent');
    $name = $request->input('name');
    $newPath = $parent . DIRECTORY_SEPARATOR . $name;
    try {
        if (!is_dir($newPath)) {
            mkdir($newPath, 0777, true);
        }
        return response()->json(['success' => true, 'path' => $newPath]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
});

// حذف پوشه
Route::post('/delete-folder', function (Request $request) {
    $path = $request->input('path');
    try {
        if (is_dir($path)) {
            rmdir($path);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'error' => 'پوشه یافت نشد']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
});

// انتقال فایل با درصد پیشرفت (کپی chunk شده و ذخیره درصد در فایل یا session)
Route::post('/transfer-file-progress', function (Request $request) {
    $sourcePath = $request->input('sourcePath');
    $destinationPath = $request->input('destinationPath');
    $fileName = basename($sourcePath);
    $targetFile = $destinationPath . DIRECTORY_SEPARATOR . $fileName;

    // اندازه فایل و chunk
    $chunkSize = 1024 * 1024 * 10; // 10MB
    $totalSize = filesize($sourcePath);
    $copiedSize = 0;

    $src = fopen($sourcePath, 'rb');
    $dst = fopen($targetFile, 'wb');
    while (!feof($src)) {
        $buffer = fread($src, $chunkSize);
        fwrite($dst, $buffer);
        $copiedSize += strlen($buffer);

        // ذخیره درصد پیشرفت در فایل temp
        file_put_contents(storage_path('app/transfer_progress_' . $fileName . '.txt'), $copiedSize / $totalSize * 100);
        usleep(200000); // برای تست، انتقال را آهسته می‌کنیم
    }
    fclose($src);
    fclose($dst);

    unlink(storage_path('app/transfer_progress_' . $fileName . '.txt')); // پس از پایان حذف درصد

    return response()->json(['success' => true]);
});

// دریافت درصد پیشرفت انتقال فایل
Route::get('/transfer-progress', function (Request $request) {
    $fileName = $request->input('fileName');
    $progressFile = storage_path('app/transfer_progress_' . $fileName . '.txt');
    if (file_exists($progressFile)) {
        $percent = (float)file_get_contents($progressFile);
        return response()->json(['percent' => $percent]);
    }
    return response()->json(['percent' => 100]);
});
