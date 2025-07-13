document.addEventListener('DOMContentLoaded', function() {
    let defaultDestFolder = localStorage.getItem('defaultDestFolder') || '';
    document.getElementById('currentDestFolder').textContent = defaultDestFolder || 'انتخاب نشده';
    let selectedFolderPath = '';

    window.openDestManager = async function() {
        const modal = document.getElementById('destManagerModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('modal-active');
        await loadDrives();
    };

    window.closeDestManager = function() {
        const modal = document.getElementById('destManagerModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('modal-active');
    };

    window.loadDrives = async function() {
        const driveSelect = document.getElementById('selectDrive');
        driveSelect.innerHTML = '<option value="">در حال بارگذاری...</option>';
        let drives = [];
        try {
            const res = await fetch('/get-drives');
            drives = await res.json();
        } catch {}
        driveSelect.innerHTML = drives.map(d =>
            `<option value="${d.path}">${d.label} (${d.path})</option>`
        ).join('');
        driveSelect.onchange = () => loadFoldersFiles(driveSelect.value);
        if (drives.length > 0) loadFoldersFiles(drives[0].path);
    };

    window.loadFoldersFiles = async function(path) {
        selectedFolderPath = path;
        const foldersFilesList = document.getElementById('foldersFilesList');
        foldersFilesList.innerHTML = '<div>در حال بارگذاری...</div>';
        let result = { folders: [], files: [] };
        try {
            const res = await fetch(`/get-folders-files?path=${encodeURIComponent(path)}`);
            result = await res.json();
        } catch { foldersFilesList.innerHTML = '<div>خطا در بارگذاری</div>'; return; }

        let html = '';
        result.folders.forEach(f => {
            html += `<div class="folder-item" onclick="selectDefaultFolder('${f.path}')">
                        <i class="fas fa-folder folder-icon"></i>
                        ${f.name}
                    </div>`;
        });
        result.files.forEach(f => {
            html += `<div class="file-item" title="${f.name}">
                        <i class="fas fa-file file-icon"></i>
                        ${f.name}
                    </div>`;
        });
        foldersFilesList.innerHTML = html;
    };

    window.selectDefaultFolder = function(folderPath) {
        defaultDestFolder = folderPath;
        localStorage.setItem('defaultDestFolder', folderPath);
        document.getElementById('currentDestFolder').textContent = folderPath;
        selectedFolderPath = folderPath;
        Swal.fire({
            icon: 'success',
            title: 'پوشه پیش‌فرض انتخاب شد',
            text: folderPath,
            confirmButtonText: 'باشه'
        });
    };

    window.createNewFolder = async function() {
        const name = document.getElementById('newFolderName').value;
        if (!name || !selectedFolderPath) {
            Swal.fire({ icon: 'warning', title: 'نام پوشه یا مسیر انتخاب نشده!', confirmButtonText: 'باشه' });
            return;
        }
        try {
            const res = await fetch('/create-folder', {
                method: 'POST',
                headers: {'Content-Type': 'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
                body: JSON.stringify({parent: selectedFolderPath, name: name})
            });
            const data = await res.json();
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'پوشه ساخته شد', text: name, confirmButtonText: 'باشه' });
                loadFoldersFiles(selectedFolderPath);
            } else {
                Swal.fire({ icon: 'error', title: 'خطا', text: data.error, confirmButtonText: 'باشه' });
            }
        } catch {
            Swal.fire({ icon: 'error', title: 'خطا', text: 'ارتباط با سرور قطع شد', confirmButtonText: 'باشه' });
        }
    };

    window.deleteSelectedFolder = async function() {
        if (!selectedFolderPath) {
            Swal.fire({ icon: 'warning', title: 'مسیر پوشه انتخاب نشده!', confirmButtonText: 'باشه' });
            return;
        }
        Swal.fire({
            icon: 'warning',
            title: 'حذف پوشه؟',
            text: 'آیا مطمئن هستید که می‌خواهید این پوشه حذف شود؟',
            showCancelButton: true,
            confirmButtonText: 'بله، حذف کن',
            cancelButtonText: 'نه'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch('/delete-folder', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
                        body: JSON.stringify({path: selectedFolderPath})
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'پوشه حذف شد', confirmButtonText: 'باشه' });
                        loadFoldersFiles(document.getElementById('selectDrive').value);
                    } else {
                        Swal.fire({ icon: 'error', title: 'خطا', text: data.error, confirmButtonText: 'باشه' });
                    }
                } catch {
                    Swal.fire({ icon: 'error', title: 'خطا', text: 'ارتباط با سرور قطع شد', confirmButtonText: 'باشه' });
                }
            }
        });
    };

    // انتقال فیلم با نمایش درصد پیشرفت
    window.transferMovieToDefault = async function(filePath) {
        if (!defaultDestFolder) {
            Swal.fire({icon: 'warning',title: 'پوشه پیش‌فرض انتخاب نشده',text: 'ابتدا پوشه را انتخاب کنید',confirmButtonText: 'باشه'});
            return;
        }
        // نمایش مودال Progress
        const modal = document.getElementById('transferProgressModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('transferProgressBar').style.width = '0%';
        document.getElementById('transferProgressBar').textContent = '0%';

        // شروع انتقال فایل (AJAX)
        fetch('/transfer-file-progress', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
            body: JSON.stringify({sourcePath: filePath, destinationPath: defaultDestFolder})
        }).then(async res => {
            // تا زمانی که فایل منتقل می‌شود، درصدش را با polling می‌گیریم
            let percent = 0;
            const fileName = filePath.split(/[/\\]/).pop();
            while (percent < 100) {
                await new Promise((resolve) => setTimeout(resolve, 500));
                const progressRes = await fetch(`/transfer-progress?fileName=${encodeURIComponent(fileName)}`);
                const progressData = await progressRes.json();
                percent = Math.floor(progressData.percent);
                document.getElementById('transferProgressBar').style.width = percent + '%';
                document.getElementById('transferProgressBar').textContent = percent + '%';
            }
            // پایان انتقال
            Swal.fire({icon: 'success',title: 'انتقال کامل شد!',text: `فایل با موفقیت منتقل شد به ${defaultDestFolder}`,confirmButtonText: 'باشه'});
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });
    };
});
