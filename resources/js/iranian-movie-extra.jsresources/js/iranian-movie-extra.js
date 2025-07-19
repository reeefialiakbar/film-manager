document.addEventListener('DOMContentLoaded', function() {
    // فقط نوار پیشرفت و SweetAlert2
    function getDefaultDestFolder() {
        return localStorage.getItem('defaultDestFolder') || '';
    }

    function setDefaultDestFolder(path) {
        localStorage.setItem('defaultDestFolder', path);
    }

    // تابع اصلی انتقال فیلم
    window.transferMovieToDefault = async function(filePath) {
        let destFolder = getDefaultDestFolder();

        // اگر مقصد پیش‌فرض تعیین نشده، از کاربر بپرس
        if (!destFolder) {
            const { value: inputPath } = await Swal.fire({
                title: 'مسیر مقصد را وارد کنید',
                input: 'text',
                inputLabel: 'پوشه‌ای که می‌خواهید فیلم منتقل شود:',
                inputPlaceholder: 'مثال: E:\\Movies',
                confirmButtonText: 'ثبت و انتقال',
                cancelButtonText: 'انصراف',
                showCancelButton: true,
                inputValidator: (value) => {
                    if (!value) return 'مسیر پوشه مقصد را وارد کنید!';
                }
            });
            if (!inputPath) return;
            destFolder = inputPath;
            setDefaultDestFolder(destFolder);
        }

        // نمایش نوار پیشرفت سمت چپ پایین
        const progressId = 'progress_' + Math.random().toString(36).substring(2, 10);
        const container = document.getElementById('transferProgressContainer');
        const barWrapper = document.createElement('div');
        barWrapper.className = 'bg-white rounded shadow p-2 w-80 flex flex-col items-end mb-2 border border-gray-200';
        barWrapper.id = progressId + '_wrapper';
        barWrapper.innerHTML = `
            <div class="text-xs font-bold mb-2 text-right">در حال انتقال فایل: <span>${filePath.split(/[\\/]/).pop()}</span></div>
            <div class="w-full bg-gray-200 rounded-full h-5 mb-2">
                <div id="${progressId}" class="bg-indigo-600 h-5 rounded-full text-xs text-white flex items-center justify-center" style="width:0%">0%</div>
            </div>
        `;
        container.appendChild(barWrapper);

        // شروع انتقال فایل
        let percent = 0;
        try {
            // ارسال درخواست انتقال به سرور (شامل آدرس منبع و مقصد)
            fetch('/transfer-file-progress', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
                body: JSON.stringify({sourcePath: filePath, destinationPath: destFolder})
            }).then(async res => {
                const fileName = filePath.split(/[/\\]/).pop();
                while (percent < 100) {
                    await new Promise((resolve) => setTimeout(resolve, 500));
                    const progressRes = await fetch(`/transfer-progress?fileName=${encodeURIComponent(fileName)}`);
                    const progressData = await progressRes.json();
                    percent = Math.floor(progressData.percent);
                    document.getElementById(progressId).style.width = percent + '%';
                    document.getElementById(progressId).textContent = percent + '%';
                }
                Swal.fire({icon: 'success',title: 'انتقال کامل شد!',text: `فایل با موفقیت منتقل شد به ${destFolder}`,confirmButtonText: 'باشه'});
                barWrapper.remove();
            });
        } catch (err) {
            Swal.fire({icon: 'error', title: 'خطا در انتقال', text: 'ارتباط با سرور قطع شد یا مسیر اشتباه است!', confirmButtonText: 'باشه'});
            barWrapper.remove();
        }
    }
});
