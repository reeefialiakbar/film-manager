@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <h2 class="text-2xl font-bold mb-6">داشبورد</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- کارت آمار فیلم‌ها -->
        <div class="bg-blue-500 text-white rounded-lg p-6">
            <div class="flex items-center">
                <i class="fas fa-film text-3xl ml-4"></i>
                <div>
                    <h3 class="text-lg">تعداد فیلم‌ها</h3>
                    <p class="text-2xl font-bold">150</p>
                </div>
            </div>
        </div>

        <!-- کارت آمار سریال‌ها -->
        <div class="bg-green-500 text-white rounded-lg p-6">
            <div class="flex items-center">
                <i class="fas fa-tv text-3xl ml-4"></i>
                <div>
                    <h3 class="text-lg">تعداد سریال‌ها</h3>
                    <p class="text-2xl font-bold">75</p>
                </div>
            </div>
        </div>

        <!-- کارت آمار انیمیشن‌ها -->
        <div class="bg-purple-500 text-white rounded-lg p-6">
            <div class="flex items-center">
                <i class="fas fa-video text-3xl ml-4"></i>
                <div>
                    <h3 class="text-lg">تعداد انیمیشن‌ها</h3>
                    <p class="text-2xl font-bold">45</p>
                </div>
            </div>
        </div>

        <!-- کارت فضای ذخیره‌سازی -->
        <div class="bg-red-500 text-white rounded-lg p-6">
            <div class="flex items-center">
                <i class="fas fa-hdd text-3xl ml-4"></i>
                <div>
                    <h3 class="text-lg">فضای استفاده شده</h3>
                    <p class="text-2xl font-bold">500GB</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
