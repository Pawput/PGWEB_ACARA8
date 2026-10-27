
<?php
session_start();

// Hanya menerima permintaan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Memeriksa token keamanan
$token = $_POST['csrf_token'] ?? '';

if (
    !isset($_SESSION['csrf_token']) ||
    !is_string($token) ||
    !hash_equals($_SESSION['csrf_token'], $token)
) {
    http_response_code(403);
    exit("Permintaan tidak valid. Silakan kembali dan coba lagi.");
}

// Memeriksa ID data
$id = filter_var(
    $_POST['id'] ?? null,
    FILTER_VALIDATE_INT
);

if ($id === false || $id === null || $id < 1) {
    header("Location: index.php?status=gagal");
    exit;
}

// Konfigurasi database
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

if ($conn->connect_error) {
    http_response_code(500);
    exit("Koneksi database gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Menghapus data berdasarkan ID
$stmt = $conn->prepare(
    "DELETE FROM penduduk WHERE id = ?"
);

if (!$stmt) {
    $conn->close();
    http_response_code(500);
    exit("Perintah penghapusan gagal disiapkan.");
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Memastikan ada baris yang berhasil dihapus
    if ($stmt->affected_rows > 0) {
        $status = "dihapus";
    } else {
        $status = "tidak_ditemukan";
    }
} else {
    $status = "gagal";
}

$stmt->close();
$conn->close();

// Kembali ke halaman utama
header("Location: index.php?status=" . $status);
exit;
?>
