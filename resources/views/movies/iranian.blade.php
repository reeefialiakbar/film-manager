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

    <!-- جستجو و فیلتر -->
    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" id="searchInput" placeholder="جستجو..."
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <select id="genreFilter" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">همه ژانرها</option>
                    @php
                        $allGenres = collect($movies)->pluck('genres')->flatten()->filter()->unique()->sort()->values()->all();
                    @endphp
                    @foreach($allGenres as $genre)
                        <option value="{{ $genre }}">{{ $genre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <input type="number" id="yearFilter" placeholder="سال ساخت" min="1300" max="2100"
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
        </div>
    </div>

    <!-- جدول فیلم‌ها -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg overflow-hidden" id="moviesTable">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-3 px-4 text-right cursor-pointer hover:bg-gray-200" onclick="sortTable(0)">#</th>
                    <th class="py-3 px-4 text-right">تصویر</th>
                    <th class="py-3 px-4 text-right cursor-pointer hover:bg-gray-200" onclick="sortTable(2)">نام اصلی</th>
                    <th class="py-3 px-4 text-right cursor-pointer hover:bg-gray-200" onclick="sortTable(3)">نام فارسی</th>
                    <th class="py-3 px-4 text-right cursor-pointer hover:bg-gray-200" onclick="sortTable(4)">تاریخ انتشار</th>
                    <th class="py-3 px-4 text-right cursor-pointer hover:bg-gray-200" onclick="sortTable(5)">ژانر</th>
                    <th class="py-3 px-4 text-right cursor-pointer hover:bg-gray-200" onclick="sortTable(6)">امتیاز IMDB</th>
                    <th class="py-3 px-4 text-right cursor-pointer hover:bg-gray-200" onclick="sortTable(7)">امتیاز شما</th>
                    <th class="py-3 px-4 text-right">عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movies as $index => $movie)
                <tr class="hover:bg-gray-50 border-b">
                    <td class="py-3 px-4">{{ $index + 1 }}</td>
                    <td class="py-3 px-4">
                        <img src="{{ $movie->image ? asset('storage/' . $movie->image) : asset('images/default-movie.jpg') }}"
                             alt="{{ $movie->title }}"
                             class="w-16 h-16 object-cover rounded cursor-pointer"
                             onclick="showImage(this.src, '{{ $movie->title }}')">
                    </td>
                    <td class="py-3 px-4">{{ $movie->title }}</td>
                    <td class="py-3 px-4">{{ $movie->director }}</td>
                    <td class="py-3 px-4">{{ $movie->release_date ? $movie->release_date->format('Y/m/d') : '-' }}</td>
                    <td class="py-3 px-4">{{ is_array($movie->genres) ? implode(', ', $movie->genres) : '-' }}</td>
                    <td class="py-3 px-4">{{ $movie->imdb_rating ?: '-' }}</td>
                    <td class="py-3 px-4">{{ $movie->your_rating ?: '-' }}</td>
                    <td class="py-3 px-4">
                        <button class="text-blue-500 hover:text-blue-700 mx-1" onclick="editMovie({{ $movie->id }})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="text-green-500 hover:text-green-700 mx-1" onclick="transferMovie('{{ $movie->file_path }}')">
                            <i class="fas fa-exchange-alt"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal افزودن/ویرایش فیلم -->
    <div id="movieModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold" id="modalTitle">افزودن فیلم جدید</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="movieForm" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                        <div>
        <label class="block text-sm font-medium mb-1">نام اصلی فیلم (انگلیسی)</label>
        <input type="text" name="title" class="w-full rounded-lg border-gray-300" required
               dir="ltr" placeholder="Enter original title">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">نام فارسی فیلم</label>
        <input type="text" name="persian_title" class="w-full rounded-lg border-gray-300"
               placeholder="عنوان فارسی فیلم را وارد کنید">
    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">کارگردان</label>
                        <input type="text" name="director" class="w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">تاریخ انتشار</label>
                        <input type="date" name="release_date" class="w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">مدت زمان</label>
                        <input type="text" name="duration" class="w-full rounded-lg border-gray-300" placeholder="مثال: 02:30:00">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">امتیاز IMDB</label>
                        <input type="number" name="imdb_rating" step="0.1" min="0" max="10" class="w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">امتیاز شما</label>
                        <input type="number" name="your_rating" min="0" max="10" class="w-full rounded-lg border-gray-300">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">ژانرها</label>
                    <select name="genres[]" multiple class="w-full rounded-lg border-gray-300" id="genresSelect">
                        <option value="Action">اکشن</option>
                        <option value="Adventure">ماجراجویی</option>
                        <option value="Animation">انیمیشن</option>
                        <option value="Biography">زندگی‌نامه</option>
                        <option value="Comedy">کمدی</option>
                        <option value="Crime">جنایی</option>
                        <option value="Documentary">مستند</option>
                        <option value="Drama">درام</option>
                        <option value="Family">خانوادگی</option>
                        <option value="Fantasy">فانتزی</option>
                        <option value="History">تاریخی</option>
                        <option value="Horror">ترسناک</option>
                        <option value="Music">موزیکال</option>
                        <option value="Mystery">معمایی</option>
                        <option value="Romance">عاشقانه</option>
                        <option value="Sci-Fi">علمی-تخیلی</option>
                        <option value="Sport">ورزشی</option>
                        <option value="Thriller">هیجان‌انگیز</option>
                        <option value="War">جنگی</option>
                    </select>
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
                    <label class="block text-sm font-medium mb-1">فایل فیلم</label>
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

    <!-- Modal نمایش تصویر -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="relative bg-white p-4 rounded-lg max-w-4xl max-h-[90vh]">
            <button onclick="closeImageModal()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
            <h3 id="imageTitle" class="text-lg font-bold mb-4 text-center"></h3>
            <img id="modalImage" src="" alt="" class="max-h-[80vh] mx-auto">
        </div>
    </div>

    <!-- Modal انتقال فایل -->
    <div id="transferModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">انتقال فایل</h3>
                <button onclick="closeTransferModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">پوشه پیش‌فرض</label>
                <select id="defaultFolderSelect" class="w-full rounded-lg border-gray-300">
                    <option value="">انتخاب پوشه پیش‌فرض</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">انتخاب مقصد</label>
                <select id="driveSelect" class="w-full rounded-lg border-gray-300 mb-2">
                    <option value="">در حال بارگذاری درایوها...</option>
                </select>

                <div id="folderPath" class="text-sm text-gray-600 mb-2"></div>
            </div>

            <div class="flex justify-end">
                <button onclick="startTransfer()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                    شروع انتقال
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .modal {
        transition: opacity 0.25s ease;
    }
    body.modal-active {
        overflow-x: hidden;
        overflow-y: visible !important;
    }
    .select2-container {
        width: 100% !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2
    $('#genresSelect').select2({
        placeholder: 'ژانرها را انتخاب کنید',
        dir: 'rtl',
        width: '100%'
    });

    // تابع باز کردن مودال
    window.openModal = function() {
        const modal = document.getElementById('movieModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('modal-active');
    };

    // تابع بستن مودال
    window.closeModal = function() {
        const modal = document.getElementById('movieModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('modal-active');
    };

    // تابع نمایش تصویر
    window.showImage = function(src, title) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const imageTitle = document.getElementById('imageTitle');

        modalImage.src = src;
        imageTitle.textContent = title;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    // تابع بستن مودال تصویر
    window.closeImageModal = function() {
        const modal = document.getElementById('imageModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    // تابع انتخاب فایل
    window.openFileDialog = function() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'video/*';
        input.onchange = function(e) {
            document.getElementById('file_path').value = e.target.files[0].path;
        };
        input.click();
    };

    // توابع انتقال فایل
    let availableDrives = [];
    let defaultFolders = [];

window.loadDrives = async function() {
    try {
        const response = await fetch('/get-drives');
        availableDrives = await response.json();

        const driveSelect = document.getElementById('driveSelect');
        driveSelect.innerHTML = availableDrives.map(drive =>
            `<option value="${drive.path}">${drive.label} (${drive.path})</option>`
        ).join('');
    } catch (error) {
        console.error('Error loading drives:', error);
    }
};

window.loadDefaultFolders = async function() {
    try {
        const response = await fetch('/get-default-folders');
        defaultFolders = await response.json();

        const folderSelect = document.getElementById('defaultFolderSelect');
        folderSelect.innerHTML = defaultFolders.map(folder =>
            `<option value="${folder.path}">${folder.name}</option>`
        ).join('');
    } catch (error) {
        console.error('Error loading default folders:', error);
    }
};

    window.transferMovie = function(filePath) {
        const modal = document.getElementById('transferModal');
        modal.dataset.filePath = filePath;

        loadDrives();
        loadDefaultFolders();

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    window.closeTransferModal = function() {
        const modal = document.getElementById('transferModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

window.startTransfer = async function() {
    const modal = document.getElementById('transferModal');
    const filePath = modal.dataset.filePath;
    const destinationPath = document.getElementById('driveSelect').value;
    const defaultFolder = document.getElementById('defaultFolderSelect').value;

    try {
        const response = await fetch('/transfer-file', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                sourcePath: filePath,
                destinationPath: destinationPath,
                defaultFolder: defaultFolder
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('فایل با موفقیت منتقل شد');
            closeTransferModal();
        } else {
            alert('خطا در انتقال فایل: ' + result.error);
        }
    } catch (error) {
        console.error('Error transferring file:', error);
        alert('خطا در انتقال فایل');
    }
};

    // تنظیم فرم
    const movieForm = document.getElementById('movieForm');
    if (movieForm) {
        movieForm.addEventListener('submit', async function(e) {
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
                alert('خطا در ذخیره‌سازی فیلم');
            }
        });
    }

    // تنظیم فیلترها
    const setupFilters = function() {
        const searchInput = document.getElementById('searchInput');
        const genreFilter = document.getElementById('genreFilter');
        const yearFilter = document.getElementById('yearFilter');

        const filterTable = function() {
            const searchText = searchInput.value.toLowerCase();
            const selectedGenre = genreFilter.value.toLowerCase();
            const selectedYear = yearFilter.value;

            const rows = document.querySelectorAll('#moviesTable tbody tr');

            rows.forEach(row => {
                const title = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const genres = row.querySelector('td:nth-child(6)').textContent.toLowerCase();
                const year = row.querySelector('td:nth-child(5)').textContent.split('/')[0];

                const matchesSearch = title.includes(searchText);
                const matchesGenre = !selectedGenre || genres.includes(selectedGenre);
                const matchesYear = !selectedYear || (year.includes(selectedYear) && selectedYear !== '');

                row.style.display = matchesSearch && matchesGenre && matchesYear ? '' : 'none';
            });
        };

        searchInput?.addEventListener('input', filterTable);
        genreFilter?.addEventListener('change', filterTable);
        yearFilter?.addEventListener('input', filterTable);
    };

    setupFilters();

    // مرتب‌سازی جدول
    window.sortTable = function(n) {
        const table = document.getElementById("moviesTable");
        let switching = true;
        let dir = "asc";
        let switchcount = 0;

        while (switching) {
            switching = false;
            const rows = table.rows;

            for (let i = 1; i < (rows.length - 1); i++) {
                let shouldSwitch = false;
                const x = rows[i].getElementsByTagName("TD")[n];
                const y = rows[i + 1].getElementsByTagName("TD")[n];

                if (dir === "asc") {
                    if (x.textContent.toLowerCase() > y.textContent.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                } else if (dir === "desc") {
                    if (x.textContent.toLowerCase() < y.textContent.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                }
            }

            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
                switchcount++;
            } else {
                if (switchcount === 0 && dir === "asc") {
                    dir = "desc";
                    switching = true;
                }
            }
        }
    };
});
</script>
@endpush
@endsection
