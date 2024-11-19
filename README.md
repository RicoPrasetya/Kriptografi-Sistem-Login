Berikut adalah penjelasan lengkap dan terstruktur tentang kode PHP di atas yang mengimplementasikan Playfair Cipher untuk mengenkripsi dan mendekripsi data:

---

## **1. Fungsi untuk Membuat Key Square**
```php
function generateKeySquare($key) {
    $specialChars = '@0123456789'; // Simbol tambahan yang didukung
    $alphabet = 'abcdefghiklmnopqrstuvwxyz'; // Alfabet tanpa 'j'
    $allChars = $alphabet . $specialChars;

    $key = strtolower(str_replace('j', 'i', $key)); // Ganti 'j' dengan 'i'
    $key = preg_replace('/[^a-z0-9@]/', '', $key); // Hapus karakter yang tidak valid

    $keyArray = array_unique(str_split($key . $allChars)); // Hilangkan duplikat
    return array_chunk($keyArray, 6); // Matriks sekarang 6x6
}
```
### **Penjelasan:**
- **Input:** String kunci (`$key`).
- **Tujuan:** Membuat key square (matriks 6x6) untuk enkripsi/dekripsi.
- **Langkah-langkah:**
  1. Menambahkan alfabet (`abcdefghiklmnopqrstuvwxyz`, tanpa 'j') dan simbol (`@0123456789`).
  2. Mengubah semua huruf menjadi huruf kecil dan mengganti `j` dengan `i`.
  3. Menghapus karakter selain huruf, angka, dan `@`.
  4. Membuat array unik dari kunci gabungan dengan semua karakter yang valid.
  5. Membentuk matriks 6x6 dengan `array_chunk`.

### **Contoh:**
- Kunci: `securekey`
- Hasil Key Square:
```
s e c u r k
y a b d f g
h i l m n o
p q t v w x
z @ 0 1 2 3
4 5 6 7 8 9
```

---

## **2. Fungsi untuk Menemukan Posisi Karakter dalam Key Square**
```php
function findPosition($matrix, $char) {
    foreach ($matrix as $row => $cols) {
        $col = array_search($char, $cols);
        if ($col !== false) {
            return [$row, $col];
        }
    }
    return null;
}
```
### **Penjelasan:**
- **Input:** Matriks dan karakter yang dicari.
- **Tujuan:** Mencari posisi baris dan kolom dari sebuah karakter dalam matriks.
- **Langkah-langkah:**
  1. Loop melalui setiap baris matriks.
  2. Cari kolom di mana karakter muncul.
  3. Jika ditemukan, kembalikan posisi dalam bentuk array `[baris, kolom]`.

---

## **3. Fungsi Enkripsi dengan Playfair Cipher**
```php
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
```
### **Penjelasan:**
- **Input:** Teks yang akan dienkripsi dan kunci.
- **Tujuan:** Mengenkripsi teks dengan algoritma Playfair Cipher.
- **Langkah-langkah:**
  1. Membuat matriks dari kunci.
  2. Menyesuaikan teks dengan mengganti `j` menjadi `i` dan hanya menyisakan karakter yang valid.
  3. Menambahkan padding `x` jika jumlah karakter ganjil.
  4. Untuk setiap pasangan huruf:
     - Jika berada pada **baris yang sama**, ambil huruf di kanan (looping jika di ujung kanan).
     - Jika berada pada **kolom yang sama**, ambil huruf di bawah (looping jika di ujung bawah).
     - Jika **bentuk persegi**, ambil huruf diagonal di kolom lawan.

---

## **4. Fungsi Dekripsi dengan Playfair Cipher**
```php
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
```
### **Penjelasan:**
Dekripsi bekerja sama dengan enkripsi, tetapi arah langkahnya berlawanan:
- Pada **baris yang sama**, huruf diambil dari kiri.
- Pada **kolom yang sama**, huruf diambil dari atas.
- Pada **bentuk persegi**, huruf diambil secara diagonal.

---

## **5. Fungsi untuk Mengenkripsi atau Mendekripsi Input**
### **Mengenkripsi Input**
```php
function encryptInput($data, $key) {
    $encryptedData = [];
    foreach ($data as $field => $value) {
        $encryptedData[$field] = playfairEncrypt($value, $key);
    }
    return $encryptedData;
}
```

### **Mendekripsi Input**
```php
function decryptInput($data, $key) {
    $decryptedData = [];
    foreach ($data as $field => $value) {
        $decryptedData[$field] = playfairDecrypt($value, $key);
    }
    return $decryptedData;
}
```
### **Penjelasan:**
- Fungsi ini memproses seluruh data dari user:
  - `encryptInput`: Mengenkripsi setiap field menggunakan `playfairEncrypt`.
  - `decryptInput`: Mendekripsi setiap field menggunakan `playfairDecrypt`.

--- 

Kode ini memungkinkan penggunaan Playfair Cipher untuk enkripsi dan dekripsi data dengan dukungan angka (`0-9`) dan simbol khusus (`@`).
