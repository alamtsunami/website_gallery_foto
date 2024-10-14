<?php
include 'config.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Ambil semua foto
$queryFoto = "SELECT foto.*, user.Username FROM foto JOIN user ON foto.UserID = user.UserID";
$resultFoto = $conn->query($queryFoto);

// Ambil semua album
$queryAlbum = "SELECT * FROM album";
$resultAlbum = $conn->query($queryAlbum);

// Proses hapus foto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_foto_id'])) {
    $fotoID = $_POST['delete_foto_id'];

    // Matikan foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS=0");

    // Hapus semua komentar terkait foto tersebut
    $deleteCommentsQuery = "DELETE FROM komentarfoto WHERE FotoID = ?";
    $deleteCommentsStmt = $conn->prepare($deleteCommentsQuery);
    $deleteCommentsStmt->bind_param("i", $fotoID);
    $deleteCommentsStmt->execute();

    // Hapus semua like terkait foto tersebut
    $deleteLikesQuery = "DELETE FROM likefoto WHERE FotoID = ?";
    $deleteLikesStmt = $conn->prepare($deleteLikesQuery);
    $deleteLikesStmt->bind_param("i", $fotoID);
    $deleteLikesStmt->execute();

    // Hapus foto dari tabel
    $deleteQuery = "DELETE FROM foto WHERE FotoID = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $fotoID);
    $deleteStmt->execute();

    // Aktifkan kembali foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS=1");

    header("Location: admin.php"); // Arahkan kembali setelah hapus
    exit;
}

// Proses hapus album
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_album_id'])) {
    $albumID = $_POST['delete_album_id'];

    // Matikan foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS=0");

    // Hapus semua foto terkait album tersebut
    $deleteFotosQuery = "DELETE FROM foto WHERE AlbumID = ?";
    $deleteFotosStmt = $conn->prepare($deleteFotosQuery);
    $deleteFotosStmt->bind_param("i", $albumID);
    $deleteFotosStmt->execute();

    // Hapus album dari tabel
    $deleteAlbumQuery = "DELETE FROM album WHERE AlbumID = ?";
    $deleteAlbumStmt = $conn->prepare($deleteAlbumQuery);
    $deleteAlbumStmt->bind_param("i", $albumID);
    $deleteAlbumStmt->execute();

    // Aktifkan kembali foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS=1");

    header("Location: admin.php"); // Arahkan kembali setelah hapus
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('keren.jpg');
            background-size: cover;
            background-position: center;
        }
    </style>
    <script>
        function printTable(tableId) {
            const printContent = document.getElementById(tableId).outerHTML;
            const originalContent = document.body.innerHTML;
            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = originalContent;
            location.reload(); // Reload halaman setelah mencetak
        }
    </script>
</head>
<body class="bg-gray-100">

    <header class="bg-blue-200 shadow-md py-4 mb-6">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold text-black">Admin Panel</h1>
            <a href="logout.php" class="font-bold hover:text-black">Logout</a>
        </div>
    </header>

    <div class="container mx-auto p-6">
        <a href="index.php" class="font-bold flex items-center text-black hover:text-black-700 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Beranda
        </a>

        <h2 class="text-2xl font-bold mb-4">Daftar Foto</h2>
        <button onclick="printTable('fotoTable')" class="bg-blue-500 text-white py-2 px-4 rounded mb-4">Cetak Daftar Foto</button>
        <table id="fotoTable" class="min-w-full bg-white border border-gray-300 mb-8">
            <thead>
                <tr>
                    <th class="py-2 border-b text-center font-bold">ID Foto</th>
                    <th class="py-2 border-b text-center font-bold">Judul Foto</th>
                    <th class="py-2 border-b text-center font-bold">Deskripsi Foto</th>
                    <th class="py-2 border-b text-center font-bold">Tanggal Unggah</th>
                    <th class="py-2 border-b text-center font-bold">Foto</th>
                    <th class="py-2 border-b text-center font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($foto = $resultFoto->fetch_assoc()) { ?>
                    <tr>
                        <td class="py-2 border-b text-center font-bold"><?php echo htmlspecialchars($foto['FotoID']); ?></td>
                        <td class="py-2 border-b text-center font-bold"><?php echo htmlspecialchars($foto['JudulFoto']); ?></td>
                        <td class="py-2 border-b text-center font-bold"><?php echo htmlspecialchars($foto['DeskripsiFoto']); ?></td>
                        <td class="py-2 border-b text-center font-bold"><?php echo htmlspecialchars($foto['TanggalUnggah']); ?></td>
                        <td class="py-2 border-b text-center">
                            <img src="<?php echo htmlspecialchars($foto['LokasiFile']); ?>" alt="Foto" class="h-16 w-16 object-cover mx-auto">
                        </td>
                        <td class="py-2 border-b text-center">
                            <form method="POST" action="" class="inline">
                                <input type="hidden" name="delete_foto_id" value="<?php echo $foto['FotoID']; ?>">
                                <button type="submit" class="bg-red-500 text-white py-1 px-3 rounded">Delete</button>
                            </form>
                            <a href="view_foto.php?id=<?php echo $foto['FotoID']; ?>" class="bg-blue-500 text-white py-1 px-3 rounded inline-block">View</a>
                            <a href="edit_foto.php?id=<?php echo $foto['FotoID']; ?>" class="bg-yellow-500 text-white py-1 px-3 rounded inline-block">Edit</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <h2 class="text-2xl font-bold mb-4">Daftar Album</h2>
        <button onclick="printTable('albumTable')" class="bg-blue-500 text-white py-2 px-4 rounded mb-4">Cetak Daftar Album</button>
        <table id="albumTable" class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr>
                    <th class="py-2 border-b text-center font-bold">AlbumID</th>
                    <th class="py-2 border-b text-center font-bold">Nama Album</th>
                    <th class="py-2 border-b text-center font-bold">Deskripsi</th>
                    <th class="py-2 border-b text-center font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($album = $resultAlbum->fetch_assoc()) { ?>
                    <tr>
                        <td class="py-2 border-b text-center font-bold"><?php echo htmlspecialchars($album['AlbumID']); ?></td>
                        <td class="py-2 border-b text-center font-bold"><?php echo htmlspecialchars($album['NamaAlbum']); ?></td>
                        <td class="py-2 border-b text-center font-bold"><?php echo htmlspecialchars($album['Deskripsi']); ?></td>
                        <td class="py-2 border-b text-center">
                            <form method="POST" action="" class="inline">
                                <input type="hidden" name="delete_album_id" value="<?php echo $album['AlbumID']; ?>">
                                <button type="submit" class="bg-red-500 text-white py-1 px-3 rounded">Delete</button>
                            </form>
                            <a href="view_album.php?id=<?php echo $album['AlbumID']; ?>" class="bg-blue-500 text-white py-1 px-3 rounded inline-block">View</a>
                            <a href="edit_album.php?id=<?php echo $album['AlbumID']; ?>" class="bg-yellow-500 text-white py-1 px-3 rounded inline-block">Edit</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</body>
</html>
