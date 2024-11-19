// Playfair Cipher enkripsi (dengan dukungan angka dan simbol)
function generateKeySquare(key) {
    const specialChars = '@0123456789';
    const alphabet = 'abcdefghiklmnopqrstuvwxyz';
    const allChars = alphabet + specialChars;

    key = key.toLowerCase().replace(/j/g, 'i'); // mengganti 'j' dengan 'i'
    key = key.replace(/[^a-z0-9@]/g, ''); // hapus karakter selain yang diizinkan

    let keyArray = Array.from(new Set(key.split(''))); // Menghilangkan duplikat
    keyArray = keyArray.concat(allChars.split('').filter(letter => !keyArray.includes(letter)));
    return keyArray; // Matriks 6x6
}

function findPosition(matrix, char) {
    const size = 6; // Matriks sekarang 6x6
    const index = matrix.indexOf(char);
    return [Math.floor(index / size), index % size];
}

function encryptText(text, key) {
    const matrix = generateKeySquare(key);
    const size = 6;
    text = text.toLowerCase().replace(/j/g, 'i').replace(/[^a-z0-9@]/g, ''); // Normalisasi teks
    if (text.length % 2 !== 0) text += 'x'; // Tambahkan 'x' jika jumlah karakter ganjil

    let encrypted = '';
    for (let i = 0; i < text.length; i += 2) {
        let pair = [text[i], text[i + 1]];
        if (pair[0] === pair[1]) pair[1] = 'x'; // Jika kedua huruf sama, ganti dengan 'x'

        const pos1 = findPosition(matrix, pair[0]);
        const pos2 = findPosition(matrix, pair[1]);

        if (pos1[0] === pos2[0]) {
            encrypted += matrix[pos1[0] * size + (pos1[1] + 1) % size];
            encrypted += matrix[pos2[0] * size + (pos2[1] + 1) % size];
        } else if (pos1[1] === pos2[1]) {
            encrypted += matrix[((pos1[0] + 1) % size) * size + pos1[1]];
            encrypted += matrix[((pos2[0] + 1) % size) * size + pos2[1]];
        } else {
            encrypted += matrix[pos1[0] * size + pos2[1]];
            encrypted += matrix[pos2[0] * size + pos1[1]];
        }
    }
    return encrypted;
}
