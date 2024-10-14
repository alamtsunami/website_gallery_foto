<?php
include 'config.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit;
}

$fotoID = $_GET['id'];

// Ambil data foto berdasarkan ID
$query = "SELECT * FROM foto WHERE FotoID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $fotoID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: admin.php");
    exit;
}

$foto = $result->fetch_assoc();

// Proses pembaruan foto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judulFoto = $_POST['judul_foto'];
    $deskripsiFoto = $_POST['deskripsi_foto'];

    // Proses unggah file
    if (isset($_FILES['lokasi_file']) && $_FILES['lokasi_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['lokasi_file']['tmp_name'];
        $fileName = $_FILES['lokasi_file']['name'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = uniqid() . '.' . $fileExtension;
        $uploadFileDir = 'uploads/';
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $lokasiFile = $dest_path;

            // Update data foto
            $updateQuery = "UPDATE foto SET JudulFoto = ?, DeskripsiFoto = ?, LokasiFile = ? WHERE FotoID = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("sssi", $judulFoto, $deskripsiFoto, $lokasiFile, $fotoID);
            $updateStmt->execute();

            header("Location: admin.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Foto</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <header class="bg-blue-200 shadow-md py-4 mb-6">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold text-black">Edit Foto</h1>
            <a href="logout.php" class="font-bold hover:text-black">Logout</a>
        </div>
    </header>

    <div class="container mx-auto p-6">
        <a href="admin.php" class="font-bold text-black hover:text-black-700 mb-4 inline-block">
            Kembali ke Admin Panel
        </a>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="judul_foto" class="block text-sm font-bold mb-2">Judul Foto:</label>
                <input type="text" name="judul_foto" id="judul_foto" value="<?php echo htmlspecialchars($foto['JudulFoto']); ?>" class="border border-gray-300 rounded p-2 w-full" required>
            </div>
            <div class="mb-4">
                <label for="deskripsi_foto" class="block text-sm font-bold mb-2">Deskripsi Foto:</label>
                <textarea name="deskripsi_foto" id="deskripsi_foto" class="border border-gray-300 rounded p-2 w-full" required><?php echo htmlspecialchars($foto['DeskripsiFoto']); ?></textarea>
            </div>
            <div class="mb-4">
                <label for="lokasi_file" class="block text-sm font-bold mb-2">Unggah File Foto:</label>
                <input type="file" name="lokasi_file" id="lokasi_file" class="border border-gray-300 rounded p-2 w-full" required>
            </div>
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">Simpan Perubahan</button>
        </form>
    </div>

</body>
</html>
