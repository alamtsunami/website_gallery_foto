<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);  // Menggunakan md5 untuk hashing password
    $email = $_POST['email'];

    $query = "INSERT INTO user (Username, Password, Email) VALUES ('$username', '$password', '$email')";
    if ($conn->query($query)) {
        echo "Pendaftaran berhasil!";
        header('Location: login.php');
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background: url('keren.jpg'); 
            backdrop-filter: blur(5px); /* Efek blur pada background */
        }
        .register-container {
            background-color: rgba(0, 105, 92, 0.9); /* Warna biru gelap untuk form dengan transparansi */
            padding: 40px; /* Perbesar padding untuk kotak register */
            border-radius: 12px; /* Bulatkan sudut kotak */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Tambahkan bayangan untuk kesan mendalam */
        }
    </style>
</head>
<body class="flex items-center justify-center h-screen">
    <div class="w-full max-w-xs">
        <form method="POST" class="register-container shadow-lg">
            <h2 class="text-white text-xl font-bold mb-6 text-center">Daftar Akun Baru</h2>
            <div class="mb-4">
                <label class="block text-white text-sm font-bold mb-2" for="username">Username</label>
                <input type="text" name="username" class="shadow appearance-none border rounded w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-4">
                <label class="block text-white text-sm font-bold mb-2" for="email">Email</label>
                <input type="email" name="email" class="shadow appearance-none border rounded w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-6">
                <label class="block text-white text-sm font-bold mb-2" for="password">Password</label>
                <input type="password" name="password" class="shadow appearance-none border rounded w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">Daftar</button>
                <a class="inline-block align-baseline font-bold text-sm text-white hover:text-blue-300" href="login.php">Login</a>
            </div>
        </form>
    </div>
</body>
</html>
