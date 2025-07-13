<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>مدیریت فیلم</title>
        <!-- Tailwind CSS -->
    @vite('resources/css/app.css')

    <!-- فونت ایران‌سنس -->
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
        body {
            font-family: "Vazirmatn", sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 h-screen bg-gradient-to-b from-blue-800 to-blue-600 text-white fixed">
            <div class="p-4">
                <h1 class="text-2xl font-bold mb-8">مدیریت فیلم</h1>

                <!-- منوی اصلی -->
                <nav>
                    <a href="/dashboard" class="flex items-center p-3 mb-2 rounded hover:bg-blue-700">
                        <i class="fas fa-home w-6"></i>
                        <span>داشبورد</span>
                    </a>

                    <!-- منوی فیلم‌ها -->
                    <div class="mb-4">
                        <div class="flex items-center p-3 rounded hover:bg-blue-700">
                            <i class="fas fa-film w-6"></i>
                            <span>فیلم‌ها</span>
                        </div>
                        <div class="pr-8">
                            <a href="/movies/iranian" class="flex items-center p-2 rounded hover:bg-blue-700">
                                <i class="fas fa-circle-dot w-4 text-xs"></i>
                                <span>ایرانی</span>
                            </a>
                            <a href="/movies/foreign" class="flex items-center p-2 rounded hover:bg-blue-700">
                                <i class="fas fa-circle-dot w-4 text-xs"></i>
                                <span>خارجی</span>
                            </a>
                            <a href="/movies/animation" class="flex items-center p-2 rounded hover:bg-blue-700">
                                <i class="fas fa-circle-dot w-4 text-xs"></i>
                                <span>انیمیشن</span>
                            </a>
                            <a href="/movies/trailer" class="flex items-center p-2 rounded hover:bg-blue-700">
                                <i class="fas fa-circle-dot w-4 text-xs"></i>
                                <span>تیزر</span>
                            </a>
                        </div>
                    </div>

                    <!-- منوی سریال‌ها -->
                    <div class="mb-4">
                        <div class="flex items-center p-3 rounded hover:bg-blue-700">
                            <i class="fas fa-tv w-6"></i>
                            <span>سریال‌ها</span>
                        </div>
                        <div class="pr-8">
                            <a href="/series/iranian" class="flex items-center p-2 rounded hover:bg-blue-700">
                                <i class="fas fa-circle-dot w-4 text-xs"></i>
                                <span>ایرانی</span>
                            </a>
                            <a href="/series/foreign" class="flex items-center p-2 rounded hover:bg-blue-700">
                                <i class="fas fa-circle-dot w-4 text-xs"></i>
                                <span>خارجی</span>
                            </a>
                            <a href="/series/animation" class="flex items-center p-2 rounded hover:bg-blue-700">
                                <i class="fas fa-circle-dot w-4 text-xs"></i>
                                <span>انیمیشن</span>
                            </a>
                            <a href="/series/trailer" class="flex items-center p-2 rounded hover:bg-blue-700">
                                <i class="fas fa-circle-dot w-4 text-xs"></i>
                                <span>تیزر</span>
                            </a>
                        </div>
                    </div>

                    <!-- تنظیمات -->
                    <a href="/settings" class="flex items-center p-3 rounded hover:bg-blue-700">
                        <i class="fas fa-cog w-6"></i>
                        <span>تنظیمات</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 mr-64 p-8">
            @yield('content')
        </div>
    </div>
</body>
</html>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@stack('styles')

<!-- در انتهای body -->
@stack('scripts')
