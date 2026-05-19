<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Unggah Berkas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen font-sans">

    <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full mx-4 border border-slate-100 text-center">

        <?php
        $target_dir = "uploads/";
        
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $originalName = $_FILES["fileToUpload"]["name"];
        $fileSizeByte = $_FILES["fileToUpload"]["size"];
        $target_file = $target_dir . basename($originalName);
        $uploadOk = 1;
        
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $fileNameOnly = pathinfo($originalName, PATHINFO_FILENAME);
        $fileSizeKB = round($fileSizeByte / 1024, 2);

        $statusTitle = "";
        $statusMsg = "";
        $uiType = ""; 

        // 1. Periksa apakah berkas sudah ada
        if (file_exists($target_file)) {
            $statusTitle = "Berkas Sudah Ada";
            $statusMsg = "Maaf, berkas dengan nama tersebut sudah tersedia di server.";
            $uploadOk = 0;
            $uiType = "warning";
        }

        // 2. Periksa ukuran berkas (Maksimal 500KB)
        if ($uploadOk == 1 && $fileSizeByte > 500000) {
            $statusTitle = "Berkas Terlalu Besar";
            $statusMsg = "Maaf, ukuran berkas Anda ($fileSizeKB KB) melebihi batas maksimal 500 KB.";
            $uploadOk = 0;
            $uiType = "error";
        }

        // 3. Eksekusi akhir pengunggahan
        if ($uploadOk == 0) {
            renderResponseBox($uiType, $statusTitle, $statusMsg, null, null);
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $metaData = [
                    "Nama File" => $fileNameOnly,
                    "Ekstensi" => $fileType,
                    "Ukuran" => $fileSizeKB . " KB"
                ];
                
                // Kirim path $target_file yang sukses disimpan
                renderResponseBox("success", "Unggah Berhasil!", "Berkas Anda telah aman disimpan di dalam server.", $metaData, $target_file);
            } else {
                renderResponseBox("error", "Kesalahan Sistem", "Maaf, terjadi masalah internal saat memindahkan berkas Anda.", null, null);
            }
        }

        function renderResponseBox($type, $title, $message, $details = null, $filePath = null) {
            $iconBg = "text-blue-500 bg-blue-50";
            $iconSvg = '<svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
            
            if ($type == "success") {
                $iconBg = "text-green-500 bg-green-50";
                $iconSvg = '<svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
            } elseif ($type == "error") {
                $iconBg = "text-red-500 bg-red-50";
                $iconSvg = '<svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
            } elseif ($type == "warning") {
                $iconBg = "text-amber-500 bg-amber-50";
                $iconSvg = '<svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
            }
            
            echo '
            <div class="w-16 h-16 ' . $iconBg . ' rounded-full flex items-center justify-center mx-auto mb-5 animate-bounce">
                ' . $iconSvg . '
            </div>
            <h2 class="text-xl font-bold text-slate-800 mb-2">' . $title . '</h2>
            <p class="text-sm text-slate-500 leading-relaxed mb-5">' . $message . '</p>
            ';

            // TRICK FIX: Mengonversi gambar menjadi Base64 Data Stream agar pasti tembus render ke UI browser
            if ($filePath !== null && file_exists($filePath)) {
                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                $imgExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                
                if (in_array($ext, $imgExts)) {
                    // Baca file gambar secara biner dan konversi ke base64
                    $imgData = base64_encode(file_get_contents($filePath));
                    $srcData = 'data:image/' . $ext . ';base64,' . $imgData;
                    
                    echo '
                    <div class="w-full flex flex-col items-center bg-slate-50 p-3 rounded-xl border border-slate-200 mb-4 animate-fade-in">
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Pratinjau Hasil Upload</p>
                        <img src="' . $srcData . '" alt="Hasil Upload" class="max-h-40 object-contain rounded-lg shadow-sm border border-white">
                    </div>';
                }
            }

            if ($details !== null) {
                echo '<div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs text-left text-slate-600 space-y-1.5 mb-6">';
                foreach ($details as $key => $value) {
                    echo '<div class="flex justify-between"><span class="text-slate-400">' . $key . ':</span> <span class="font-medium text-slate-800 truncate max-w-[200px] uppercase">' . htmlspecialchars($value) . '</span></div>';
                }
                echo '</div>';
            }
        }
        ?>

        <!-- Tombol Kembali -->
        <a href="index.php" class="inline-block w-full bg-slate-800 hover:bg-slate-900 text-white font-medium py-3 px-4 rounded-xl transition-all duration-200 shadow-md">
            Kembali ke Beranda
        </a>
    </div>

</body>
</html>