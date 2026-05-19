<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Penghapusan File</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen font-sans">

    <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full mx-4 border border-slate-100 text-center">

        <?php
        if (isset($_GET['file'])) {
            $fileName = basename($_GET['file']); 
            $filePath = "uploads/" . $fileName;

            if (file_exists($filePath)) {
                if (unlink($filePath)) {
                    renderDeleteBox("success", "File Berhasil Dihapus", "Berkas <span class='font-semibold text-slate-700'>" . htmlspecialchars($fileName) . "</span> telah sukses dimusnahkan dari server.");
                } else {
                    renderDeleteBox("error", "Gagal Menghapus", "Terjadi kesalahan internal sistem saat mencoba menghapus file.");
                }
            } else {
                renderDeleteBox("warning", "File Tidak Ditemukan", "File tersebut sudah tidak ada atau telah dihapus sebelumnya.");
            }
        } else {
            renderDeleteBox("error", "Akses Ditolak", "Tidak ada file spesifik yang dipilih untuk dihapus.");
        }

        function renderDeleteBox($type, $title, $message) {
            $iconBg = "text-red-500 bg-red-50";
            $iconSvg = '<svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>';
            
            if ($type == "warning") {
                $iconBg = "text-amber-500 bg-amber-50";
                $iconSvg = '<svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
            }
            
            echo '
            <div class="w-16 h-16 ' . $iconBg . ' rounded-full flex items-center justify-center mx-auto mb-5 animate-pulse">
                ' . $iconSvg . '
            </div>
            <h2 class="text-xl font-bold text-slate-800 mb-2">' . $title . '</h2>
            <p class="text-sm text-slate-500 leading-relaxed mb-6">' . $message . '</p>
            ';
        }
        ?>

        <!-- Tombol Kembali -->
        <a href="index.php" class="inline-block w-full bg-slate-800 hover:bg-slate-900 text-white font-medium py-3 px-4 rounded-xl transition-all duration-200 shadow-md">
            Kembali ke Daftar File
        </a>
    </div>

</body>
</html>