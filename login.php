<?php
include 'config.php';
session_start(); // Pastikan ini ada di bagian paling atas

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);  // Menggunakan md5 untuk hashing password

    // Query untuk mengambil data pengguna berdasarkan username dan password
    $query = "SELECT * FROM user WHERE Username='$username' AND Password='$password'"; // tiga. mengambil data dari database untuk mencocokan username dan password 
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['userID'] = $user['UserID'];     // empat. jika login berhasil, maka informasi pengguna disimpan ke dalam session 
        $_SESSION['username'] = $user['Username'];
        $_SESSION['role'] = $user['role'];         //

        // Debug untuk memastikan sesi tersimpan
        echo "Session userID: " . $_SESSION['userID'];
        echo "Session username: " . $_SESSION['username'];
        echo "Session role: " . $_SESSION['role'];

        if ($_SESSION['role'] == 'admin') {         // lima. pengguna akan diarahkan ke halaman admin.php jika role-nya adalah 'admin', atau ke halaman index.php jika bukan:
            header('Location: admin.php'); 
        } else {
            header('Location: index.php'); 
        }
        exit;                                       //
    } else {
        echo "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background: url('keren.jpg') no-repeat center center fixed; /* Ganti 'keren.jpg' dengan lokasi gambar Anda */
            background-size: cover; /* Mengatur agar gambar memenuhi seluruh layar */
            backdrop-filter: blur(10px);
        }
        .login-container {
            background-color: rgba(0, 105, 92, 0.9); 
            padding: 40px; /* Perbesar padding untuk kotak login */
            border-radius: 12px; /* Bulatkan sudut kotak */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Tambahkan bayangan untuk kesan mendalam */
        }
    </style>
</head>
<body class="flex items-center justify-center h-screen">
    <div class="w-full max-w-xs">
        <form method="POST" class="login-container shadow-lg">
            <h2 class="text-white text-xl font-bold mb-6 text-center">Masuk ke Akun Anda</h2>
            <div class="mb-4">
                <label class="block text-white text-sm font-bold mb-2" for="username">Username</label>
                <input type="text" name="username" class="shadow appearance-none border rounded w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-6">
                <label class="block text-white text-sm font-bold mb-2" for="password">Password</label>
                <input type="password" name="password" class="shadow appearance-none border rounded w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">Login</button>
                <a class="inline-block align-baseline font-bold text-sm text-white hover:text-blue-300" href="register.php">Daftar</a>
            </div>
        </form>
    </div>      
</body>
</html>
