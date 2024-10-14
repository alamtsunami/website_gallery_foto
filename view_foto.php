<?php
include 'config.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit;
}

$fotoID = $_GET['id'];

// Ambil data foto berdasarkan ID
$query = "SELECT foto.*, user.Username FROM foto JOIN user ON foto.UserID = user.UserID WHERE FotoID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $fotoID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: admin.php");
    exit;
}

$foto = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>View Foto</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('keren.jpg');
            background-size: cover;
            background-position: center;
        }
    </style>
    <script>
        function openModal() {
            document.getElementById('fotoModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('fotoModal').classList.add('hidden');
        }
    </script>
</head>
<body class="bg-gray-100">

    <header class="bg-blue-200 shadow-md py-4 mb-6">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold text-black">View Foto</h1>
            <a href="admin.php" class="font-bold hover:text-black">Kembali</a>
        </div>
    </header>

    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($foto['JudulFoto']); ?></h2>
        <p class="mb-4"><?php echo htmlspecialchars($foto['DeskripsiFoto']); ?></p>
        <p class="mb-4"><strong>Uploaded by:</strong> <?php echo htmlspecialchars($foto['Username']); ?></p>
        <button onclick="openModal()" class="bg-blue-500 text-white py-2 px-4 rounded">Lihat Gambar</button>

        <!-- Modal -->
        <div id="fotoModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex justify-center items-center">
            <div class="bg-white rounded-lg overflow-hidden shadow-lg max-w-lg w-full">
                <div class="relative">
                    <img src="<?php echo htmlspecialchars($foto['LokasiFile']); ?>" alt="Foto" class="w-full h-auto">
                    <button onclick="closeModal()" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1">X</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
