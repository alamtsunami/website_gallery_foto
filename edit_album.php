<?php
include 'config.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Periksa apakah ada parameter ID album
if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit;
}

$albumID = $_GET['id'];

// Ambil data album berdasarkan ID
$query = "SELECT * FROM album WHERE AlbumID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $albumID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: admin.php");
    exit;
}

$album = $result->fetch_assoc();

// Proses update album
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaAlbum = $_POST['nama_album'];
    $deskripsiAlbum = $_POST['deskripsi_album'];

    $updateQuery = "UPDATE album SET NamaAlbum = ?, Deskripsi = ? WHERE AlbumID = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ssi", $namaAlbum, $deskripsiAlbum, $albumID);
    $updateStmt->execute();

    header("Location: admin.php"); // Kembali ke halaman admin setelah update
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Album</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <div class="container mx-auto p-6">
        <a href="admin.php" class="font-bold flex items-center text-black hover:text-black-700 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Admin Panel
        </a>

        <h2 class="text-2xl font-bold mb-4">Edit Album</h2>

        <form method="POST" action="">
            <div class="mb-4">
                <label for="nama_album" class="block text-sm font-bold mb-2">Nama Album:</label>
                <input type="text" id="nama_album" name="nama_album" value="<?php echo htmlspecialchars($album['NamaAlbum']); ?>" class="w-full p-2 border border-gray-300 rounded" required>
            </div>

            <div class="mb-4">
                <label for="deskripsi_album" class="block text-sm font-bold mb-2">Deskripsi Album:</label>
                <textarea id="deskripsi_album" name="deskripsi_album" class="w-full p-2 border border-gray-300 rounded" required><?php echo htmlspecialchars($album['Deskripsi']); ?></textarea>
            </div>

            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">Update Album</button>
        </form>
    </div>

</body>
</html>
