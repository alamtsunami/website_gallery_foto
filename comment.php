<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_foto_id']) && isset($_POST['comment_text'])) {
    $fotoID = $_POST['comment_foto_id'];
    $userID = $_SESSION['userID'];
    $commentText = $_POST['comment_text'];

    $insertCommentQuery = "INSERT INTO komentar (FotoID, UserID, IsiKomentar, TanggalKomentar) VALUES (?, ?, ?, NOW())";
    $commentStmt = $conn->prepare($insertCommentQuery);
    $commentStmt->bind_param("iis", $fotoID, $userID, $commentText);
    $commentStmt->execute();

    echo json_encode(['success' => true]);
    exit;
}
?>
