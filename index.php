<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Manajemen File</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex flex-col items-center justify-center min-h-screen font-sans py-10">

    <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full mx-4 border border-slate-100">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Unggah Berkas</h2>
            <p class="text-sm text-slate-500 mt-1">Pilih berkas dokumen atau gambar untuk diunggah</p>
        </div>

        <form action="upload.php" method="post" enctype="multipart/form-data" class="space-y-5">
            
            <div class="relative group">
                <label for="fileToUpload" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer bg-slate-50 hover:bg-slate-100 hover:border-blue-400 transition-all duration-200">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                        <svg class="w-10 h-10 mb-3 text-slate-400 group-hover:text-blue-500 transition-colors duration-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                        </svg>
                        <p class="mb-1 text-sm text-slate-700 font-semibold">Klik untuk memilih file</p>
                        <p id="file-prompt" class="text-xs text-slate-400 truncate max-w-xs">atau seret (drag & drop) file ke sini</p>
                    </div>
                    <input type="file" name="fileToUpload" id="fileToUpload" class="absolute inset-0 opacity-0 cursor-pointer" onchange="showFileDetails()">
                </label>
            </div>

            <div id="detail-box" class="hidden bg-slate-50 rounded-xl p-4 border border-slate-200 text-xs text-slate-600 space-y-1.5 animate-fade-in">
                <p class="font-semibold text-slate-700 mb-1 text-sm">Pratinjau Berkas:</p>
                <div class="flex justify-between"><span class="text-slate-400">Nama:</span> <span id="det-name" class="font-medium text-slate-800 max-w-[200px] truncate"></span></div>
                <div class="flex justify-between"><span class="text-slate-400">Ekstensi:</span> <span id="det-ext" class="font-medium text-slate-800 uppercase"></span></div>
                <div class="flex justify-between"><span class="text-slate-400">Ukuran:</span> <span id="det-size" class="font-medium text-slate-800"></span></div>
            </div>

            <button type="submit" name="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-xl shadow-md shadow-blue-200 hover:shadow-lg transition-all duration-200">
                Unggah Sekarang
            </button>
        </form>

        <hr class="my-6 border-slate-100">

        <div>
            <h3 class="text-sm font-semibold text-slate-700 mb-3">File Terunggah di Server:</h3>
            <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                <?php
                $dir = "uploads/";
                if (is_dir($dir)) {
                    $files = array_diff(scandir($dir), array('.', '..'));
                    
                    if (count($files) > 0) {
                        foreach ($files as $file) {
                            $filePath = $dir . $file;
                            $extLower = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                            $extUpper = strtoupper($extLower);
                            $size = round(filesize($filePath) / 1024, 1) . ' KB';
                            
                            // Daftar ekstensi yang dianggap sebagai gambar
                            $imgExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                            
                            // Logika menentukan apakah pakai Thumbnail Foto atau Ikon Dokumen Bawaan
                            if (in_array($extLower, $imgExts)) {
                                // JIKA FILE ADALAH GAMBAR -> Munculkan preview foto kecil (Thumbnail)
                                $visualPreview = '<img src="' . $filePath . '" alt="preview" class="w-10 h-10 object-cover rounded-lg shadow-sm border border-slate-200 flex-shrink-0">';
                            } else {
                                // JIKA BUKAN GAMBAR (PDF, ZIP, DLL) -> Munculkan ikon dokumen abu-abu default
                                $visualPreview = '
                                <div class="w-10 h-10 bg-slate-200 rounded-lg flex items-center justify-center text-slate-500 flex-shrink-0 font-bold text-[10px]">
                                    ' . $extUpper . '
                                </div>';
                            }
                            
                            echo '
                            <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-xl hover:bg-slate-100/70 transition-colors">
                                <div class="flex items-center space-x-3 truncate max-w-[240px]">
                                    ' . $visualPreview . '
                                    
                                    <div class="truncate">
                                        <p class="text-xs font-medium text-slate-800 truncate">' . htmlspecialchars($file) . '</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">' . $extUpper . ' • ' . $size . '</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-1">
                                    <a href="' . $filePath . '" download class="text-slate-400 hover:text-blue-500 p-1.5 rounded-lg hover:bg-blue-50 transition-colors" title="Unduh File">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    </a>
                                    <a href="delete.php?file=' . urlencode($file) . '" onclick="return confirm(\'Apakah Anda yakin ingin menghapus file ini?\')" class="text-slate-400 hover:text-red-500 p-1.5 rounded-lg hover:bg-red-50 transition-colors" title="Hapus File">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </a>
                                </div>
                            </div>';
                        }
                    } else {
                        echo '<p class="text-xs text-slate-400 italic text-center py-4">Belum ada file yang diunggah.</p>';
                    }
                } else {
                    echo '<p class="text-xs text-slate-400 italic text-center py-4">Belum ada file yang diunggah.</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        function showFileDetails() {
            const input = document.getElementById('fileToUpload');
            const detailBox = document.getElementById('detail-box');
            if (input.files.length > 0) {
                const file = input.files[0];
                const fullFileName = file.name;
                const lastDot = fullFileName.lastIndexOf('.');
                const nameOnly = lastDot !== -1 ? fullFileName.substring(0, lastDot) : fullFileName;
                const extension = lastDot !== -1 ? fullFileName.substring(lastDot + 1) : 'Unknown';
                const sizeInKB = (file.size / 1024).toFixed(2);

                document.getElementById('det-name').textContent = nameOnly;
                document.getElementById('det-ext').textContent = extension;
                document.getElementById('det-size').textContent = sizeInKB + " KB";
                detailBox.classList.remove('hidden');
                document.getElementById('file-prompt').textContent = "File siap diunggah";
            }
        }
    </script>
</body>
</html>