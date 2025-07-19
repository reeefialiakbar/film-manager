document.addEventListener('DOMContentLoaded', function() {
    // انتخاب فیلم با لیست پوشه
    window.openSelectFolderDialog = async function() {
        const modal = document.getElementById('selectFolderModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('modal-active');
        await loadDrivesForFile();
    };

    window.closeSelectFolderModal = function() {
        const modal = document.getElementById('selectFolderModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('modal-active');
    };

    window.loadDrivesForFile = async function() {
        const driveSelect = document.getElementById('selectDriveForFile');
        driveSelect.innerHTML = '<option value="">در حال بارگذاری...</option>';
        let drives = [];
        try {
            const res = await fetch('/get-drives');
            drives = await res.json();
        } catch {}
        driveSelect.innerHTML = drives.map(d =>
            `<option value="${d.path}">${d.label} (${d.path})</option>`
        ).join('');
        driveSelect.onchange = () => loadVideosFiles(driveSelect.value);
        if (drives.length > 0) loadVideosFiles(drives[0].path);
    };

    window.loadVideosFiles = async function(path) {
        const videosFilesList = document.getElementById('videosFilesList');
        videosFilesList.innerHTML = '<div>در حال بارگذاری...</div>';
        let result = { folders: [], files: [] };
        try {
            const res = await fetch(`/get-folders-files?path=${encodeURIComponent(path)}`);
            result = await res.json();
        } catch { videosFilesList.innerHTML = '<div>خطا در بارگذاری</div>'; return; }
        let html = '';
        result.folders.forEach(f => {
            html += `<div class="folder-item" onclick="loadVideosFiles('${f.path}')">
                        <i class="fas fa-folder folder-icon"></i>
                        ${f.name}
                    </div>`;
        });
        result.files.forEach(f => {
            if (/\.(mp4|mkv|avi|wmv|flv|mov)$/i.test(f.name))
                html += `<div class="file-item" onclick="selectMovieFile('${f.path}')">
                            <i class="fas fa-film file-icon"></i>
                            ${f.name}
                        </div>`;
        });
        videosFilesList.innerHTML = html;
    };

    window.selectMovieFile = function(filePath) {
        document.getElementById('file_path').value = filePath;
        Swal.fire({
            icon: 'success',
            title: 'فیلم انتخاب شد',
            text: filePath,
            confirmButtonText: 'باشه'
        });
        closeSelectFolderModal();
    };

    // باز کردن پوشه فایل فیلم
    window.openFileFolder = function(filePath) {
        fetch('/open-file-folder', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ filePath })
        }).then(async res => {
            const data = await res.json();
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'پوشه باز شد',
                    text: 'پوشه فیلم در سیستم شما باز شد.',
                    confirmButtonText: 'باشه'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطا',
                    text: data.error || 'باز کردن پوشه ممکن نشد!',
                    confirmButtonText: 'باشه'
                });
            }
        });
    };
});
