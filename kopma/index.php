<?php
// Koneksi ke database
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'absensi_db';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Handle tambah data absensi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'tambah') {
    $nama = $_POST['nama'];
    $prodi = $_POST['prodi'];
    $semester = $_POST['semester'];
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];

    $sql = "INSERT INTO absensi (nama, prodi, semester, tanggal, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nama, $prodi, $semester, $tanggal, $status);

    if ($stmt->execute()) {
        echo "<script>alert('Absensi berhasil ditambahkan.');</script>";
    } else {
        echo "<script>alert('Gagal menambahkan absensi: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle update data absensi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'edit') {
    $id = intval($_POST['id']);
    $nama = $_POST['nama'];
    $prodi = $_POST['prodi'];
    $semester = $_POST['semester'];
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];

    $sql = "UPDATE absensi SET nama=?, prodi=?, semester=?, tanggal=?, status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nama, $prodi, $semester, $tanggal, $status, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Absensi berhasil diperbarui.');</script>";
    } else {
        echo "<script>alert('Gagal mengupdate absensi: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Hapus data absensi
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM absensi WHERE id = $id");
    header("Location: index.php");
    exit;
}

// Ambil semua data absensi
$sql = "SELECT * FROM absensi ORDER BY tanggal DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sistem Absensi Kopma Amanah</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>



<div class="container mt-5">
    <h1 class="text-center mb-4">Sistem Absensi Kopma Amanah</h1>

    <!-- Tombol Tambah Data -->
    <div class="d-flex justify-content-between mb-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Absensi</button>
        <a href="laporan_absensi.php" class="btn btn-secondary">Cetak Laporan</a>
    </div>

    <!-- Tabel Data Absensi -->
    <table class="table table-striped table-bordered text-center">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Prodi</th>
                <th>Semester</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['prodi']) ?></td>
                    <td><?= htmlspecialchars($row['semester']) ?></td>
                    <td><?= $row['tanggal'] ?></td>
                    <td>
                        <span class="badge 
                            <?= $row['status'] === 'Hadir' ? 'bg-success' : 
                                ($row['status'] === 'Izin' ? 'bg-warning' : 
                                ($row['status'] === 'Sakit' ? 'bg-primary' : 'bg-danger')) ?>">
                            <?= htmlspecialchars($row['status']) ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm btn-edit" 
                            data-id="<?= $row['id'] ?>"
                            data-nama="<?= htmlspecialchars($row['nama']) ?>"
                            data-prodi="<?= htmlspecialchars($row['prodi']) ?>"
                            data-semester="<?= htmlspecialchars($row['semester']) ?>"
                            data-tanggal="<?= $row['tanggal'] ?>"
                            data-status="<?= $row['status'] ?>"
                            data-bs-toggle="modal" data-bs-target="#modalEdit">Edit</button>

                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7" class="text-center">Belum ada data absensi.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah Absensi -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="index.php">
                <input type="hidden" name="aksi" value="tambah" />
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahLabel">Tambah Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama:</label>
                        <input type="text" id="nama" name="nama" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="prodi" class="form-label">Prodi:</label>
                        <input type="text" id="prodi" name="prodi" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="semester" class="form-label">Semester:</label>
                        <input type="text" id="semester" name="semester" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal:</label>
                        <input type="date" id="tanggal" name="tanggal" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status Kehadiran:</label>
                        <select id="status" name="status" class="form-select" required>
                            <option value="Hadir">Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Alpa">Alpa</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Absensi -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="index.php">
                <input type="hidden" name="aksi" value="edit" />
                <input type="hidden" id="edit-id" name="id" />
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel">Edit Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit-nama" class="form-label">Nama:</label>
                        <input type="text" id="edit-nama" name="nama" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="edit-prodi" class="form-label">Prodi:</label>
                        <input type="text" id="edit-prodi" name="prodi" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="edit-semester" class="form-label">Semester:</label>
                        <input type="text" id="edit-semester" name="semester" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="edit-tanggal" class="form-label">Tanggal:</label>
                        <input type="date" id="edit-tanggal" name="tanggal" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="edit-status" class="form-label">Status Kehadiran:</label>
                        <select id="edit-status" name="status" class="form-select" required>
                            <option value="Hadir">Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Alpa">Alpa</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Ketika tombol Edit diklik, isi modal edit dengan data dari baris
document.querySelectorAll('.btn-edit').forEach(button => {
    button.addEventListener('click', () => {
        document.getElementById('edit-id').value = button.dataset.id;
        document.getElementById('edit-nama').value = button.dataset.nama;
        document.getElementById('edit-prodi').value = button.dataset.prodi;
        document.getElementById('edit-semester').value = button.dataset.semester;
        document.getElementById('edit-tanggal').value = button.dataset.tanggal;
        document.getElementById('edit-status').value = button.dataset.status;
    });
});
</script>

</body>
</html>

<?php
$conn->close();
?>



