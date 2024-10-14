<?php
include 'config.php';
session_start();

// Cek apakah pengguna sudah login, jika belum, arahkan ke halaman login
if (!isset($_SESSION['username']) || !isset($_SESSION['userID'])) {
    header("Location: login.php");  // Arahkan ke halaman login jika belum login
    exit;
}

// Ambil userID dari session login
$userID = $_SESSION['userID'];

// Ambil album berdasarkan userID
$albumSql = "SELECT * FROM album WHERE UserID = '$userID'";
$albumResult = $conn->query($albumSql);

// Jika album kosong, tampilkan pesan untuk membuat album terlebih dahulu
if ($albumResult->num_rows == 0) {
    die("Tidak ada album. Silakan buat album terlebih dahulu.");
}

// Menambahkan foto ke album
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judulFoto = $conn->real_escape_string($_POST['judulFoto']);
    $deskripsiFoto = $conn->real_escape_string($_POST['deskripsiFoto']);
    
    // Cek apakah AlbumID di-set
    if (isset($_POST['albumID']) && !empty($_POST['albumID'])) {
        $albumID = $_POST['albumID'];
    } else {
        die("AlbumID tidak ditemukan. Silakan pilih album.");
    }
    
    // Pastikan folder uploads sudah ada
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);  // Membuat folder uploads jika belum ada
    }

    $filename = $_FILES['photo']['name'];
    $target_file = $upload_dir . basename($filename);

    // Mengecek apakah file berhasil diupload dari tmp folder ke folder tujuan
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
        // Masukkan data foto ke dalam tabel foto
        $sql = "INSERT INTO foto (JudulFoto, DeskripsiFoto, TanggalUnggah, LokasiFile, AlbumID, UserID) 
                VALUES ('$judulFoto', '$deskripsiFoto', NOW(), '$target_file', '$albumID', '$userID')";
        
        // Jalankan query dan cek hasilnya
        if ($conn->query($sql) === TRUE) {
            echo "<div class='text-green-600 text-center mt-4'>Foto berhasil diunggah!</div>";
        } else {
            echo "<div class='text-red-600 text-center mt-4'>Error: " . $sql . "<br>" . $conn->error . "</div>";
        }
    } else {
        echo "<div class='text-red-600 text-center mt-4'>Gagal mengunggah file!</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Unggah Foto</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Definisikan keyframes untuk fade-in */
        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        body {
            background-image: url('keren.jpg'); /* Ganti dengan path gambar latar belakang */
            background-size: cover;
            background-position: center;
            animation: fadeIn 1s ease-in; /* Terapkan animasi fade-in */
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="max-w-lg mx-auto mt-10 bg-white p-8 shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold mb-5 text-center text-blue-600">Unggah Foto</h2>
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block text-gray-700">Judul Foto:</label>
                <input type="text" name="judulFoto" placeholder="Judul Foto" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-gray-700">Deskripsi Foto:</label>
                <textarea name="deskripsiFoto" placeholder="Deskripsi Foto" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-500"></textarea>
            </div>
            <div>
                <label class="block text-gray-700">Pilih Album:</label>
                <select name="albumID" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
                    <?php
                    while ($album = $albumResult->fetch_assoc()) {
                        echo "<option value='" . $album['AlbumID'] . "'>" . $album['NamaAlbum'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label class="block text-gray-700">Unggah Foto:</label>
                <input type="file" name="photo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
            </div>
            <div>
                <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Unggah</button>
            </div>
        </form>
        <div class="mt-4 text-center">
            <a href="index.php" class="text-blue-500 hover:underline">Kembali</a>
        </div>
    </div>
</body>
</html>
