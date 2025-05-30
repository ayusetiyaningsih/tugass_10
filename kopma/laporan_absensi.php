<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "absensi_db");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Mengambil semua data absensi
$sql = "SELECT * FROM absensi ORDER BY tanggal DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head> <!-- <--- Tag head sebelumnya belum ditutup -->
<body>
<div class="container mt-4">
    <h1 class="text-center mb-3">Laporan Absensi</h1>
    <p class="text-center">Tanggal: <?php echo date("d-m-Y"); ?></p>

    <!-- Tombol Cetak dan Kembali -->
    <div class="d-flex justify-content-between my-3">
        <a href="index.php" class="btn btn-secondary no-print">Kembali ke Absensi</a>
        <button class="btn btn-primary no-print" onclick="window.print()">Cetak Laporan</button>
    </div>

    <table class="table table-bordered table-striped text-center">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Prodi</th>
                <th>Semester</th>
                <th>Tanggal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['prodi']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['semester']) . "</td>";
                    echo "<td>" . $row['tanggal'] . "</td>";
                    echo "<td><span class='badge ";
                    echo ($row['status'] === 'Hadir') ? 'bg-success' :
                         (($row['status'] === 'Izin') ? 'bg-warning' :
                         (($row['status'] === 'Sakit') ? 'bg-primary' : 'bg-danger'));
                    echo "'>" . htmlspecialchars($row['status']) . "</span></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>Belum ada data absensi.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php
$conn->close();
?>
