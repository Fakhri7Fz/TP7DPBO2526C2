<?php
// Ambil data rental dan pilihan customer/game untuk mengisi tabel serta dropdown.
$rental = new Rental($db);
$dataRentals = $rental->getAll()->fetchAll(PDO::FETCH_ASSOC);

// Mengambil data customer untuk dropdown
$stmtCustomer = $db->prepare("SELECT * FROM customers");
$stmtCustomer->execute();
$dataCustomers = $stmtCustomer->fetchAll(PDO::FETCH_ASSOC);

// Mengambil data game untuk dropdown
$stmtGame = $db->prepare("SELECT * FROM games");
$stmtGame->execute();
$dataGames = $stmtGame->fetchAll(PDO::FETCH_ASSOC);

// Jika URL memuat ID edit, ambil rental tersebut untuk mengisi form.
$editRental = null;

if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM rentals WHERE id = :id");
    $stmt->bindParam(":id", $_GET['edit']);
    $stmt->execute();

    $editRental = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<h2>Rental Management</h2>

<?php if (($_GET['error'] ?? '') === 'invalid_duration') { ?>
    <p>Durasi rental harus berupa angka minimal 1 hari.</p>
<?php } elseif (($_GET['error'] ?? '') === 'invalid_game') { ?>
    <p>Game yang dipilih tidak ditemukan. Silakan pilih game yang tersedia.</p>
<?php } ?>

<!-- Form mengirim pilihan customer, game, dan durasi ke index.php untuk diproses. -->
<form method="POST" action="index.php?page=rentals">

    <input type="hidden" name="id" value="<?= $editRental['id'] ?? '' ?>">

    <label>Customer</label>
    <select name="customer_id" required>
        <option value="">-- Pilih Customer --</option>

        <?php foreach ($dataCustomers as $c) { ?>
            <option value="<?= $c['id'] ?>" <?= ($editRental['customer_id'] ?? '') == $c['id']
                  ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['nama']) ?>
            </option>
        <?php } ?>
    </select>

    <br><br>

    <label>Game</label>
    <select name="game_id" required>
        <option value="">-- Pilih Game --</option>

        <?php foreach ($dataGames as $g) { ?>
            <option value="<?= $g['id'] ?>" <?= ($editRental['game_id'] ?? '') == $g['id']
                  ? 'selected' : '' ?>>
                <?= htmlspecialchars($g['nama']) ?>
            </option>
        <?php } ?>
    </select>

    <br><br>

    <label>Tanggal Sewa</label>
    <input type="date" name="tanggal_sewa" value="<?= $editRental['tanggal_sewa'] ?? '' ?>" required>

    <br><br>

    <label>Durasi (hari)</label>
    <input type="number" name="durasi" min="1" value="<?= $editRental['durasi'] ?? '' ?>" required>

    <br>
    <small>Biaya dihitung otomatis: harga sewa game per hari × durasi.</small>

    <br><br>

    <button type="submit" name="simpan_rental">
        <?= $editRental ? 'Update Rental' : 'Tambah Rental' ?>
    </button>
</form>

<hr>

<!-- Tampilkan data rental dan informasi relasi customer serta game. -->
<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Customer</th>
        <th>Game</th>
        <th>Tanggal Sewa</th>
        <th>Durasi</th>
        <th>Biaya</th>
        <th>Aksi</th>
    </tr>

    <?php foreach ($dataRentals as $r) { ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= htmlspecialchars($r['nama_customer']) ?></td>
            <td><?= htmlspecialchars($r['nama_game']) ?></td>
            <td><?= $r['tanggal_sewa'] ?></td>
            <td><?= $r['durasi'] ?> hari</td>
            <td>Rp<?= number_format($r['biaya'], 0, ',', '.') ?></td>

            <td>
                <a href="index.php?page=rentals&edit=<?= $r['id'] ?>">
                    Edit
                </a>

                <form method="POST" action="index.php?page=rentals" style="display:inline">
                    <input type="hidden" name="id" value="<?= $r['id'] ?>">

                    <button type="submit" name="hapus_rental" onclick="return confirm('Hapus rental ini?')">
                        Hapus
                    </button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>