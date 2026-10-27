
<?php
// Memastikan data dikirim melalui formulir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.html");
    exit;
}

// Mengambil data dari formulir
$kecamatan = trim($_POST['kecamatan'] ?? '');
$longitude = $_POST['longitude'] ?? '';
$latitude = $_POST['latitude'] ?? '';
$luas = $_POST['luas'] ?? '';
$jumlah_penduduk = $_POST['jumlah_penduduk'] ?? '';

// Memeriksa nama kecamatan
if ($kecamatan === '' || strlen($kecamatan) > 100) {
    exit("Nama kecamatan wajib diisi dan maksimal 100 karakter.");
}

// Memvalidasi koordinat, luas, dan jumlah penduduk
if (
    $longitude === '' || !is_numeric($longitude) ||
    $latitude === '' || !is_numeric($latitude) ||
    $luas === '' || !is_numeric($luas) ||
    $jumlah_penduduk === '' ||
    filter_var($jumlah_penduduk, FILTER_VALIDATE_INT) === false
) {
    exit("Data tidak valid. Periksa kembali seluruh isian.");
}

// Mengubah data ke tipe numerik
$longitude = (float) $longitude;
$latitude = (float) $latitude;
$luas = (float) $luas;
$jumlah_penduduk = (int) $jumlah_penduduk;

// Memeriksa rentang nilai
if (
    $longitude < -180 || $longitude > 180 ||
    $latitude < -90 || $latitude > 90 ||
    $luas <= 0 ||
    $jumlah_penduduk < 0
) {
    exit("Koordinat, luas, atau jumlah penduduk tidak sesuai ketentuan.");
}

// Konfigurasi koneksi MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "latihan";

// Membuat koneksi
$conn = new mysqli(
    $servername,
    $username,
    $password,
    $dbname
);

// Memeriksa koneksi database
if ($conn->connect_error) {
    exit("Koneksi database gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Menyiapkan perintah untuk menyimpan data
$sql = "INSERT INTO penduduk
        (kecamatan, longitude, latitude, luas, jumlah_penduduk)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    $conn->close();
    exit("Gagal menyiapkan perintah penyimpanan data.");
}

// Mengikat nilai ke parameter SQL
$stmt->bind_param(
    "sdddi",
    $kecamatan,
    $longitude,
    $latitude,
    $luas,
    $jumlah_penduduk
);

// Menyimpan data dan kembali ke halaman utama
if ($stmt->execute()) {
    $stmt->close();
    $conn->close();

    header("Location: ../index.php?status=berhasil");
    exit;
}

// Menampilkan pesan jika penyimpanan gagal
$stmt->close();
$conn->close();

exit("Data gagal disimpan. Periksa kembali struktur tabel database.");
?>
