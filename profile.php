<?php
include 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Ambil informasi pengguna
$username = $_SESSION['username'];
$query = "SELECT * FROM user WHERE Username = '$username'";
$userResult = $conn->query($query);

if ($userResult->num_rows > 0) {
    $user = $userResult->fetch_assoc();
} else {
    die("Pengguna tidak ditemukan.");
}

// Ambil foto-foto pengguna
$userID = $user['UserID']; 
$queryFotos = "SELECT * FROM foto WHERE UserID = $userID";
$resultFotos = $conn->query($queryFotos);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil - <?php echo htmlspecialchars($user['Username']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-cover bg-center min-h-screen" style="background-image: url('keren.jpg');">

    <!-- Container for centering the content -->
    <div class="container mx-auto p-4 bg-white rounded-lg shadow-md max-w-3xl">
        <!-- Header -->
        <header class="bg-white shadow-md py-4 mb-6 text-center">
            <h1 class="text-3xl font-bold text-blue-600">Profil</h1>
            <a href="index.php" class="text-blue-500 hover:underline">Kembali ke Galeri</a> <!-- Tautan ke index.php -->
        </header>

        <div class="flex flex-col items-center text-center">
            <img src="<?php echo isset($user['FotoProfil']) && !empty($user['FotoProfil']) ? htmlspecialchars($user['FotoProfil']) : 'default_profile.png'; ?>" alt="Foto Profil" class="rounded-full w-36 h-36 object-cover mb-4">
            <h2 class="text-2xl font-bold text-blue-600"><?php echo htmlspecialchars($user['Username']); ?></h2>
            <p class="text-gray-600">Email: <?php echo htmlspecialchars($user['Email']); ?></p>
            <?php if (isset($user['Nama'])) { ?>
                <p class="text-gray-600">Nama: <?php echo htmlspecialchars($user['Nama']); ?></p>
            <?php } ?>
            <?php if (isset($user['Bio'])) { ?>
                <p class="text-gray-600">Bio: <?php echo htmlspecialchars($user['Bio']); ?></p>
            <?php } ?>
            <a href="edit_profile.php" class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Edit Profile</a>
        </div>

        

    <!-- Modal untuk Mengedit Foto -->
    <div id="editModal" class="fixed z-50 left-0 top-0 w-full h-full bg-black bg-opacity-50 hidden flex justify-center items-center">
        <div class="modal-content bg-white p-6 rounded shadow-lg w-4/5 md:w-1/2">
            <span class="close cursor-pointer" onclick="closeEditModal()">&times;</span>
            <form id="editForm" action="update_foto.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="fotoID" name="fotoID">
                <div class="mb-4">
                    <label for="judul" class="block text-gray-700">Judul:</label>
                    <input type="text" id="judul" name="judul" class="mt-1 block w-full p-2 border border-gray-300 rounded">
                </div>
                <div class="mb-4">
                    <label for="deskripsi" class="block text-gray-700">Deskripsi:</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" class="mt-1 block w-full p-2 border border-gray-300 rounded"></textarea>
                </div>
                <div class="mb-4">
                    <label for="foto" class="block text-gray-700">Ganti Foto:</label>
                    <input type="file" id="foto" name="foto" accept="image/*" class="mt-1 block w-full">
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(fotoID, judul, deskripsi) {
            document.getElementById('fotoID').value = fotoID;
            document.getElementById('judul').value = judul;
            document.getElementById('deskripsi').value = deskripsi;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('editModal')) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>
