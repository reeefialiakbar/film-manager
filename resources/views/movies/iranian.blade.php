@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">فیلم‌های ایرانی</h2>
        <button onclick="openModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus ml-2"></i>
            افزودن فیلم جدید
        </button>
    </div>

    <!-- لیست فیلم‌ها -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($movies as $movie)
        <div class="bg-gray-50 rounded-lg shadow p-4">
            <img src="{{ $movie->image ? asset('storage/' . $movie->image) : asset('images/default-movie.jpg') }}"
                 alt="{{ $movie->title }}"
                 class="w-full h-48 object-cover rounded-lg mb-4">
            <h3 class="text-lg font-bold mb-2">{{ $movie->title }}</h3>
            <div class="text-sm text-gray-600">
                <p><i class="fas fa-user ml-2"></i>کارگردان: {{ $movie->director ?: 'نامشخص' }}</p>
                <p><i class="fas fa-calendar ml-2"></i>سال: {{ $movie->year ?: 'نامشخص' }}</p>
                <p><i class="fas fa-clock ml-2"></i>مدت: {{ $movie->duration ?: 'نامشخص' }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Modal -->
    <div id="movieModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold">افزودن فیلم جدید</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="movieForm" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">نام فیلم</label>
                    <input type="text" name="title" class="w-full rounded-lg border-gray-300" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">کارگردان</label>
                    <input type="text" name="director" class="w-full rounded-lg border-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">سال ساخت</label>
                    <input type="number" name="year" class="w-full rounded-lg border-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">مدت زمان</label>
                    <input type="text" name="duration" class="w-full rounded-lg border-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">توضیحات</label>
                    <textarea name="description" class="w-full rounded-lg border-gray-300" rows="3"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">تصویر فیلم</label>
                    <input type="file" name="image" class="w-full" accept="image/*">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">آدرس فایل فیلم</label>
                    <div class="flex">
                        <input type="text" name="file_path" id="file_path" class="w-full rounded-lg border-gray-300" readonly required>
                        <button type="button" onclick="openFileDialog()" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg mr-2">
                            انتخاب فایل
                        </button>
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg ml-2">
                        انصراف
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        ذخیره
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openModal() {
    document.getElementById('movieModal').classList.remove('hidden');
    document.getElementById('movieModal').classList.add('flex');
}

function closeModal() {
    document.getElementById('movieModal').classList.add('hidden');
    document.getElementById('movieModal').classList.remove('flex');
}

function openFileDialog() {
    // این تابع باید با کتابخانه مناسب برای انتخاب فایل از سیستم پیاده‌سازی شود
    // مثلاً می‌توانید از FilePond یا DropzoneJS استفاده کنید
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'video/*';
    input.onchange = function(e) {
        document.getElementById('file_path').value = e.target.files[0].path;
    };
    input.click();
}

document.getElementById('movieForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    try {
        const response = await fetch('/movies', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            closeModal();
            location.reload();
        }
    } catch (error) {
        console.error('Error:', error);
    }
});
</script>
@endpush
@endsection
