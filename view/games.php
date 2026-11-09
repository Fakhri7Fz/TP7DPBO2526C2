<?php
// Mengambil data game dari database
$game = new Game($db);
$dataGames = $game->getAll()->fetchAll(PDO::FETCH_ASSOC);

// Jika URL memuat ID edit, ambil data game tersebut untuk mengisi form.
$editGame = null;

if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM games WHERE id = :id");
    $stmt->bindParam(":id", $_GET['edit']);
    $stmt->execute();

    $editGame = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<h2>Game Catalog</h2>

<?php if (($_GET['error'] ?? '') === 'game_in_use') { ?>
    <!-- Tampilkan peringatan jika game masih digunakan oleh rental. -->
    <p style="color: red;">Game tidak dapat dihapus karena masih digunakan pada data rental.</p>
<?php } ?>

<!-- Form ini digunakan untuk menambah game baru atau mengubah game terpilih. -->
<!-- Form tambah / edit game -->
<form method="POST" action="index.php?page=games">
    <input type="hidden" name="id" value="<?= $editGame['id'] ?? '' ?>">

    <label>Nama Game</label>
    <input type="text" name="nama" value="<?= htmlspecialchars($editGame['nama'] ?? '') ?>" required>

    <label>Platform</label>
    <input type="text" name="platform" value="<?= htmlspecialchars($editGame['platform'] ?? '') ?>" required>

    <label>Genre</label>
    <input type="text" name="genre" value="<?= htmlspecialchars($editGame['genre'] ?? '') ?>" required>

    <label>Harga Sewa (Rp)</label>
    <input type="number" name="harga_sewa" value="<?= $editGame['harga_sewa'] ?? '' ?>" min="0" required>

    <button type="submit" name="simpan">
        <?= $editGame ? 'Update Game' : 'Tambah Game' ?>
    </button>
</form>

<hr>

<!-- Tampilkan katalog game beserta aksi edit dan hapus. -->
<!-- Tabel daftar game -->
<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Platform</th>
        <th>Genre</th>
        <th>Harga Sewa</th>
        <th>Aksi</th>
    </tr>

    <?php foreach ($dataGames as $g) { ?>
        <tr>
            <td><?= $g['id'] ?></td>
            <td><?= htmlspecialchars($g['nama']) ?></td>
            <td><?= htmlspecialchars($g['platform']) ?></td>
            <td><?= htmlspecialchars($g['genre']) ?></td>
            <td>Rp<?= number_format($g['harga_sewa'], 0, ',', '.') ?></td>
            <td>
                <a href="index.php?page=games&edit=<?= $g['id'] ?>">
                    Edit
                </a>

                <form method="POST" action="index.php?page=games" style="display:inline">
                    <input type="hidden" name="id" value="<?= $g['id'] ?>">
                    <button type="submit" name="hapus" onclick="return confirm('Hapus game ini?')">
                        Hapus
                    </button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>