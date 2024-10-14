<?php
include 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Ambil nilai pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Ambil foto dan status like dengan pencarian
$query = "SELECT foto.*, user.Username, 
          IF(likefoto.UserID IS NOT NULL, 1, 0) AS liked,
          (SELECT COUNT(*) FROM likefoto WHERE FotoID = foto.FotoID) AS total_likes
          FROM foto 
          JOIN user ON foto.UserID = user.UserID 
          LEFT JOIN likefoto ON foto.FotoID = likefoto.FotoID AND likefoto.UserID = ? 
          WHERE foto.JudulFoto LIKE ? OR foto.DeskripsiFoto LIKE ?";
$stmt = $conn->prepare($query);
$searchTerm = "%$search%";
$stmt->bind_param("iss", $_SESSION['userID'], $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

// Proses like 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['foto_id'])) {
    $fotoID = $_POST['foto_id'];
    $userID = $_SESSION['userID'];

    $checkQuery = "SELECT * FROM likefoto WHERE FotoID = ? AND UserID = ?"; 
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ii", $fotoID, $userID);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 0) {
        $insertQuery = "INSERT INTO likefoto (FotoID, UserID) VALUES (?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ii", $fotoID, $userID);
        $insertStmt->execute();
        echo json_encode(['success' => true, 'liked' => true]);
    } else {
        $deleteQuery = "DELETE FROM likefoto WHERE FotoID = ? AND UserID = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("ii", $fotoID, $userID);
        $deleteStmt->execute();
        echo json_encode(['success' => true, 'liked' => false]);
    }
    exit;
}

// Proses komentar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_foto_id']) && isset($_POST['comment_text'])) {
    $fotoID = $_POST['comment_foto_id'];
    $userID = $_SESSION['userID'];
    $commentText = $_POST['comment_text'];

    $insertCommentQuery = "INSERT INTO komentarfoto (FotoID, UserID, IsiKomentar, TanggalKomentar) VALUES (?, ?, ?, NOW())";
    $commentStmt = $conn->prepare($insertCommentQuery);
    $commentStmt->bind_param("iis", $fotoID, $userID, $commentText);
    $commentStmt->execute();

    echo json_encode(['success' => true]);
    exit;
}

// Ambil komentar untuk setiap foto
$comments = [];
while ($foto = $result->fetch_assoc()) {
    $fotoID = $foto['FotoID'];
    $commentQuery = "SELECT user.Username, IsiKomentar, TanggalKomentar FROM komentarfoto JOIN user ON komentarfoto.UserID = user.UserID WHERE FotoID = ?";
    $commentStmt = $conn->prepare($commentQuery);
    $commentStmt->bind_param("i", $fotoID);
    $commentStmt->execute();
    $commentResult = $commentStmt->get_result();
    $foto['comments'] = $commentResult->fetch_all(MYSQLI_ASSOC);
    $comments[$fotoID] = $foto;
}

// Fungsi untuk mengonversi waktu ke WIB
function toWIB($datetime) {
    $dateTime = new DateTime($datetime, new DateTimeZone('UTC'));
    $dateTime->setTimezone(new DateTimeZone('Asia/Jakarta'));
    return $dateTime->format('d-m-Y H:i');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Galeri Foto</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .liked {
            color: red; /* Warna saat liked */
        }
    </style>
</head>
<body class="bg-cover" style="background-image: url('keren.jpg');">

<header class="bg-blue-200 shadow-md py-4 mb-6">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-3xl font-bold text-black">Galeri Foto</h1>
        <div class="flex space-x-4 items-center">
            <form method="GET" action="" class="flex items-center">
                <input type="text" name="search" placeholder="Cari postingan..." value="<?php echo htmlspecialchars($search); ?>" class="px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring focus:ring-blue-500">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r-md hover:bg-blue-700 transition duration-300">Cari</button>
            </form>
            <a href="upload.php" class="text-black hover:text-black flex items-center">
                <i class="fas fa-upload fa-2x"></i> Unggah
            </a>
            <a href="create.album.php" class="text-black hover:text-black flex items-center">
                <i class="fas fa-folder-plus fa-2x"></i> Tambah Album
            </a>
            <a href="admin.php" class="text-black hover:text-black flex items-center">
                <i class="fas fa-tachometer-alt fa-2x"></i> Dashboard
            </a>
            <div class="relative dropdown">
                <button class="text-black hover:text-black flex items-center" onclick="toggleDropdown()">
                    <i class="fas fa-user-circle fa-2x"></i> Profile
                </button>
                <div id="dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-md z-10">
                    <a href="profile.php" class="block px-4 py-2 text-black hover:bg-gray-200">Lihat Profile</a>
                    <a href="logout.php" class="block px-4 py-2 text-black hover:bg-gray-200">Logout</a>
                </div>
            </div>
        </div>
    </div>
</header>


<div class="container mx-auto p-6 text-center">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
        <?php foreach ($comments as $foto) { ?>
            <div class="bg-green-200 shadow-md rounded-lg overflow-hidden hover:-translate-y-4 transition transform cursor-pointer">
                <img src="<?php echo $foto['LokasiFile']; ?>" alt="<?php echo $foto['JudulFoto']; ?>" class="w-full h-48 object-cover">
                <div class="p-4">       
                    <h2 class="text-xl font-bold text-blue-600"><?php echo $foto['JudulFoto']; ?></h2>
                    <p class="text-gray-600"><?php echo $foto['DeskripsiFoto']; ?></p>
                    <p class="text-sm text-gray-500">Diunggah oleh <?php echo $foto['Username']; ?></p>
                    <p class="text-sm text-gray-500">Tanggal Upload: <?php echo toWIB($foto['TanggalUnggah']); ?></p>
                    <div class="mt-4 flex justify-center space-x-4 items-center">
                        <button id="like-btn-<?php echo $foto['FotoID']; ?>" class="text-gray-500 <?php echo $foto['liked'] ? 'liked' : ''; ?>" onclick="likePhoto(<?php echo $foto['FotoID']; ?>)">
                            <i class="fa fa-heart"></i>
                        </button>
                        <span id="total-likes-<?php echo $foto['FotoID']; ?>" class="text-gray-500"><?php echo $foto['total_likes']; ?></span>
                    </div>
                    <div class="mt-4">
                        <form onsubmit="return submitComment(<?php echo $foto['FotoID']; ?>)">
                            <input type="text" id="comment-input-<?php echo $foto['FotoID']; ?>" class="px-2 py-1 border border-gray-300 rounded  mb-2" placeholder="Tinggalkan komentar..." required>
                            <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded">Kirim</button>
                        </form>
                    </div>
                    <div id="comments-<?php echo $foto['FotoID']; ?>" class="mt-2 text-left pl-2">
                        <?php foreach ($foto['comments'] as $comment) { ?>
                            <p class="text-gray-700"><strong><?php echo htmlspecialchars($comment['Username']); ?></strong>: <?php echo htmlspecialchars($comment['IsiKomentar']); ?> <span class="text-xs text-gray-500"><?php echo toWIB($comment['TanggalKomentar']); ?></span></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script>
function likePhoto(fotoID) {
    fetch('', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ 'foto_id': fotoID })
    })
    .then(response => response.json())
    .then(data => {
        const likeBtn = document.getElementById('like-btn-' + fotoID);
        const totalLikes = document.getElementById('total-likes-' + fotoID);
        if (data.success) {
            likeBtn.classList.toggle('liked', data.liked);
            totalLikes.textContent = parseInt(totalLikes.textContent) + (data.liked ? 1 : -1);
        }
    })
    .catch(error => console.error('Error:', error));
}

function submitComment(fotoID) {
    const commentInput = document.getElementById('comment-input-' + fotoID);
    const commentText = commentInput.value;

    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            'comment_foto_id': fotoID,
            'comment_text': commentText
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const commentsDiv = document.querySelector(`#comments-${fotoID}`);
            const newComment = document.createElement('p');
            newComment.classList.add('text-gray-700');
            newComment.innerHTML = `<strong><?php echo $_SESSION['username']; ?></strong>: ${commentText}`;
            commentsDiv.appendChild(newComment);
            commentInput.value = '';
        }
    })
    .catch(error => console.error('Error:', error));

    return false; // prevent default form submission
}

function toggleDropdown() {
    const dropdown = document.getElementById('dropdown');
    dropdown.classList.toggle('hidden');
}
    
</script>

</body>
</html>
