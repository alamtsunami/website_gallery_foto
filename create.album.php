<?php
include 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Proses pembuatan album baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nama_album']) && isset($_POST['deskripsi'])) {
    $namaAlbum = $_POST['nama_album'];
    $deskripsi = $_POST['deskripsi'];
    $userID = $_SESSION['userID'];

    $insertAlbumQuery = "INSERT INTO album (NamaAlbum, Deskripsi, TanggalDibuat, UserID) VALUES (?, ?, NOW(), ?)";
    $albumStmt = $conn->prepare($insertAlbumQuery);
    $albumStmt->bind_param("ssi", $namaAlbum, $deskripsi, $userID);

    if ($albumStmt->execute()) {
        echo "<script>alert('Album berhasil dibuat!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat membuat album. Silakan coba lagi.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Album Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

<div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center">Buat Album Baru</h2>
    <form method="POST" action="">
        <div class="mb-4">
            <label for="nama_album" class="block text-sm font-medium text-gray-700">Nama Album</label>
            <input type="text" id="nama_album" name="nama_album" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div class="mb-4">
            <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="4" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-700 transition duration-300">Buat Album</button>
    </form>
</div>

</body>
</html>
