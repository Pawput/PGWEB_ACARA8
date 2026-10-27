
<?php
session_start();

// Membuat token keamanan untuk proses penghapusan
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
    die("Koneksi database gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Mengambil seluruh data penduduk
$sql = "SELECT * FROM penduduk ORDER BY id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Penduduk Kecamatan</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        header {
            background: #123b63;
            color: white;
            padding: 30px 6%;
        }

        header h1 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        header p {
            margin: 0;
            color: #dbeafe;
            font-size: 14px;
        }

        main {
            width: 94%;
            max-width: 1400px;
            margin: 30px auto;
        }

        .panel {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(15, 23, 42, 0.06);
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 22px;
        }

        .panel-header h2 {
            margin: 0;
            font-size: 21px;
        }

        .button {
            display: inline-block;
            padding: 10px 15px;
            border: none;
            border-radius: 7px;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
        }

        .add-button {
            background: #167d62;
            color: white;
        }

        .add-button:hover {
            background: #11634e;
        }

        .delete-button {
            background: #dc2626;
            color: white;
        }

        .delete-button:hover {
            background: #b91c1c;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
        }

        th, td {
            padding: 13px 12px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        th {
            background: #eaf1f8;
            color: #123b63;
            font-size: 13px;
            white-space: nowrap;
        }

        td {
            font-size: 14px;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        .number {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .empty {
            text-align: center;
            padding: 25px;
            color: #64748b;
        }

        .message {
            padding: 13px 16px;
            margin-bottom: 18px;
            border-radius: 7px;
            font-size: 14px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-cell {
            text-align: center;
        }

        .action-cell form {
            margin: 0;
        }

        .delete-button {
            padding: 9px 12px;
        }

        footer {
            text-align: center;
            padding: 25px;
            color: #64748b;
            font-size: 13px;
        }

        @media (max-width: 600px) {
            header h1 {
                font-size: 22px;
            }

            .panel {
                padding: 16px;
            }
        }
    </style>
</head>

<body>

<header>
    <h1>Data Penduduk Kecamatan</h1>
    <p>Sistem Informasi Data Wilayah dan Kependudukan</p>
</header>

<main>
    <section class="panel">

        <div class="panel-header">
            <h2>Daftar Data Penduduk</h2>

            <a href="input/index.html" class="button add-button">
                + Tambah Data
            </a>
        </div>

        <?php if (isset($_GET['status'])): ?>

            <?php if ($_GET['status'] === 'berhasil'): ?>
                <div class="message success">
                    Data penduduk berhasil ditambahkan.
                </div>
            <?php elseif ($_GET['status'] === 'dihapus'): ?>
                <div class="message success">
                    Data penduduk berhasil dihapus.
                </div>
            <?php elseif ($_GET['status'] === 'gagal'): ?>
                <div class="message error">
                    Data gagal dihapus. Silakan coba kembali.
                </div>
            <?php elseif ($_GET['status'] === 'tidak_ditemukan'): ?>
                <div class="message error">
                    Data tidak ditemukan atau sudah dihapus.
                </div>
            <?php endif; ?>

        <?php endif; ?>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kecamatan</th>
                        <th>Longitude</th>
                        <th>Latitude</th>
                        <th>Luas (km²)</th>
                        <th>Jumlah Penduduk</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($result && $result->num_rows > 0): ?>

                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?= (int) $row['id'] ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $row['kecamatan'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>

                            <td class="number">
                                <?= htmlspecialchars(
                                    (string) $row['longitude'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>

                            <td class="number">
                                <?= htmlspecialchars(
                                    (string) $row['latitude'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>

                            <td class="number">
                                <?= number_format(
                                    (float) $row['luas'],
                                    2,
                                    '.',
                                    ','
                                ) ?>
                            </td>

                            <td class="number">
                                <?= number_format(
                                    (int) $row['jumlah_penduduk'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </td>

                            <td class="action-cell">
                                <form
                                    action="delete.php"
                                    method="post"
                                    onsubmit="return confirm(
                                        'Apakah Anda yakin ingin menghapus data kecamatan ini?'
                                    );"
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $row['id'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= htmlspecialchars(
                                            $_SESSION['csrf_token'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="button delete-button"
                                    >
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="7" class="empty">
                            Belum terdapat data penduduk.
                        </td>
                    </tr>
                <?php endif; ?>

                </tbody>
            </table>
        </div>

    </section>
</main>

<footer>
    Latihan 8 — PHP dan MySQL
</footer>

</body>
</html>

<?php
$conn->close();
?>
