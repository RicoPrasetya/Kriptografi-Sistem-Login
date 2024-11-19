<?php
// Fungsi untuk menghasilkan key square dari Playfair Cipher
function generateKeySquare($key) {
    $specialChars = '@0123456789'; // Simbol tambahan yang didukung
    $alphabet = 'abcdefghiklmnopqrstuvwxyz'; // Alfabet tanpa 'j'
    $allChars = $alphabet . $specialChars;

    $key = strtolower(str_replace('j', 'i', $key)); // Ganti 'j' dengan 'i'
    $key = preg_replace('/[^a-z0-9@]/', '', $key); // Hapus karakter yang tidak valid

    $keyArray = array_unique(str_split($key . $allChars)); // Hilangkan duplikat
    return array_chunk($keyArray, 6); // Matriks sekarang 6x6
}

// Fungsi untuk mencari posisi karakter dalam key square
function findPosition($matrix, $char) {
    foreach ($matrix as $row => $cols) {
        $col = array_search($char, $cols);
        if ($col !== false) {
            return [$row, $col];
        }
    }
    return null;
}

// Fungsi enkripsi dengan Playfair Cipher
function playfairEncrypt($text, $key) {
    $matrix = generateKeySquare($key);
    $text = strtolower(str_replace('j', 'i', $text)); // Ganti 'j' dengan 'i'
    $text = preg_replace('/[^a-z0-9@]/', '', $text); // Hapus karakter yang tidak valid

    if (strlen($text) % 2 != 0) $text .= 'x'; // Jika ganjil, tambahkan 'x'

    $encrypted = '';
    for ($i = 0; $i < strlen($text); $i += 2) {
        $pair = [$text[$i], $text[$i + 1]];

        if ($pair[0] === $pair[1]) $pair[1] = 'x'; // Jika sama, ganti karakter kedua dengan 'x'

        $pos1 = findPosition($matrix, $pair[0]);
        $pos2 = findPosition($matrix, $pair[1]);

        if ($pos1[0] === $pos2[0]) {
            // Baris yang sama
            $encrypted .= $matrix[$pos1[0]][($pos1[1] + 1) % 6];
            $encrypted .= $matrix[$pos2[0]][($pos2[1] + 1) % 6];
        } elseif ($pos1[1] === $pos2[1]) {
            // Kolom yang sama
            $encrypted .= $matrix[($pos1[0] + 1) % 6][$pos1[1]];
            $encrypted .= $matrix[($pos2[0] + 1) % 6][$pos2[1]];
        } else {
            // Bentuk persegi
            $encrypted .= $matrix[$pos1[0]][$pos2[1]];
            $encrypted .= $matrix[$pos2[0]][$pos1[1]];
        }
    }
    return $encrypted;
}

// Fungsi dekripsi dengan Playfair Cipher
function playfairDecrypt($text, $key) {
    $matrix = generateKeySquare($key);
    $text = strtolower(str_replace('j', 'i', $text)); // Ganti 'j' dengan 'i'
    $text = preg_replace('/[^a-z0-9@]/', '', $text); // Hapus karakter yang tidak valid

    $decrypted = '';
    for ($i = 0; $i < strlen($text); $i += 2) {
        $pair = [$text[$i], $text[$i + 1]];

        $pos1 = findPosition($matrix, $pair[0]);
        $pos2 = findPosition($matrix, $pair[1]);

        if ($pos1[0] === $pos2[0]) {
            // Baris yang sama
            $decrypted .= $matrix[$pos1[0]][($pos1[1] - 1 + 6) % 6];
            $decrypted .= $matrix[$pos2[0]][($pos2[1] - 1 + 6) % 6];
        } elseif ($pos1[1] === $pos2[1]) {
            // Kolom yang sama
            $decrypted .= $matrix[($pos1[0] - 1 + 6) % 6][$pos1[1]];
            $decrypted .= $matrix[($pos2[0] - 1 + 6) % 6][$pos2[1]];
        } else {
            // Bentuk persegi
            $decrypted .= $matrix[$pos1[0]][$pos2[1]];
            $decrypted .= $matrix[$pos2[0]][$pos1[1]];
        }
    }
    return $decrypted;
}

// Fungsi untuk mengenkripsi seluruh input user
function encryptInput($data, $key) {
    $encryptedData = [];
    foreach ($data as $field => $value) {
        $encryptedData[$field] = playfairEncrypt($value, $key);
    }
    return $encryptedData;
}

// Fungsi untuk mendekripsi input yang diterima dari client
function decryptInput($data, $key) {
    $decryptedData = [];
    foreach ($data as $field => $value) {
        $decryptedData[$field] = playfairDecrypt($value, $key);
    }
    return $decryptedData;
}
?>
