<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data terenkripsi dari form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Dekripsi data yang diterima di server
    include 'encrypt.php';
    $decryptedData = decryptInput([
        'username' => $username,
        'password' => $password
    ], "securekey");

    // Cek username dan password yang sudah didekripsi
    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $decryptedData['username'], $decryptedData['password']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Beralih ke halaman dashboard setelah login berhasil
        header("Location: dashboard.php");
        exit();
    } else {
        // Jika gagal login, arahkan kembali ke halaman login
        header("Location: login.php?error=invalid");
        exit();
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <form id="login-form" method="post" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <button type="submit">Login</button>
        </form>
    </div>

    <script src="encrypt.js"></script>
    <script>
        document.getElementById('login-form').onsubmit = function(event) {
            event.preventDefault(); // Cegah form dikirimkan sebelum enkripsi

            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            const encryptedUsername = encryptText(username, 'securekey');
            const encryptedPassword = encryptText(password, 'securekey');

            // Kirim data yang sudah dienkripsi
            const formData = new FormData();
            formData.append('username', encryptedUsername);
            formData.append('password', encryptedPassword);

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    // Alihkan ke halaman dashboard jika login berhasil
                    window.location.href = 'dashboard.php';
                } else {
                    // Jika gagal, arahkan ulang ke halaman login
                    window.location.href = 'login.php?error=invalid';
                }
            })
            .catch(error => {
                console.error("Error:", error);
            });
        };
    </script>
</body>
</html>
