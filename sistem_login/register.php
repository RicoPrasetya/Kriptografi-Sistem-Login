<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data terenkripsi dari form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Dekripsi data yang diterima di server
    include 'encrypt.php';
    $decryptedData = decryptInput([
        'username' => $username,
        'email' => $email,
        'password' => $password
    ], "securekey");

    // Masukkan data yang didekripsi ke dalam database
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $decryptedData['username'], $decryptedData['email'], $decryptedData['password']);

    if ($stmt->execute()) {
        // Beralih ke halaman login setelah berhasil registrasi
        header("Location: login.php");
        exit();
    } else {
        // Tampilkan error jika registrasi gagal (opsional untuk debugging)
        die("Error: " . $stmt->error);
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <form id="register-form" method="post" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <button type="submit">Register</button>
        </form>
    </div>

    <script src="encrypt.js"></script>
    <script>
        document.getElementById('register-form').onsubmit = function(event) {
            event.preventDefault(); // Cegah form dikirimkan sebelum enkripsi

            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            const encryptedUsername = encryptText(username, 'securekey');
            const encryptedEmail = encryptText(email, 'securekey');
            const encryptedPassword = encryptText(password, 'securekey');

            // Kirim data yang sudah dienkripsi
            const formData = new FormData();
            formData.append('username', encryptedUsername);
            formData.append('email', encryptedEmail);
            formData.append('password', encryptedPassword);

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    // Alihkan ke halaman login jika berhasil
                    window.location.href = 'login.php';
                } else {
                    // Tangani error jika ada (opsional)
                    console.error("Registration failed.");
                }
            })
            .catch(error => {
                console.error("Error:", error);
            });
        };
    </script>
</body>
</html>
