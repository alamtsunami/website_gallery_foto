<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fotoID = $_POST['fotoID'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];

    $query = "UPDATE foto SET JudulFoto = ?, DeskripsiFoto = ? WHERE FotoID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $judul, $deskripsi, $fotoID);

    if ($stmt->execute()) {
        header("Location: profile.php?success=1");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
