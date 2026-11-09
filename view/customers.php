<?php
// Ambil daftar customer untuk tabel, lalu siapkan data jika halaman dalam mode edit.
$customer = new Customer($db);
$dataCustomers = $customer->getAll()->fetchAll(PDO::FETCH_ASSOC);

$editCustomer = null;

if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM customers WHERE id = :id");
    $stmt->bindParam(":id", $_GET['edit']);
    $stmt->execute();

    $editCustomer = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<h2>Customer Management</h2>

<?php if (($_GET['error'] ?? '') === 'invalid_phone') { ?>
    <!-- Tampilkan peringatan jika nomor telepon bukan angka. -->
    <p style="color: red;">Nomor telepon harus berupa angka.</p>
<?php } elseif (($_GET['error'] ?? '') === 'customer_in_use') { ?>
    <!-- Tampilkan peringatan jika customer masih digunakan oleh rental. -->
    <p style="color: red;">Customer tidak dapat dihapus karena masih digunakan pada data rental.</p>
<?php } ?>

<!-- Form ini digunakan untuk menambah customer baru atau mengubah customer terpilih. -->
<!-- Form tambah / edit pelanggan -->
<form method="POST" action="index.php?page=customers">
    <input type="hidden" name="id" value="<?= $editCustomer['id'] ?? '' ?>">

    <label>Nama Pelanggan</label>
    <input type="text" name="nama" value="<?= htmlspecialchars($editCustomer['nama'] ?? '') ?>" required>

    <label>No. HP</label>
    <!-- Batasi input di browser; validasi server juga dilakukan di index.php. -->
    <input type="tel" name="no_hp" inputmode="numeric" pattern="[0-9]+" title="Masukkan angka saja" value="<?= htmlspecialchars($editCustomer['no_hp'] ?? '') ?>" required>

    <button type="submit" name="simpan_customer">
        <?= $editCustomer ? 'Update Customer' : 'Tambah Customer' ?>
    </button>
</form>

<hr>

<!-- Tampilkan data customer beserta aksi edit dan hapus. -->
<!-- Tabel pelanggan -->
<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>No. HP</th>
        <th>Aksi</th>
    </tr>

    <?php foreach ($dataCustomers as $c) { ?>
        <tr>
            <td><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['nama']) ?></td>
            <td><?= htmlspecialchars($c['no_hp']) ?></td>
            <td>
                <a href="index.php?page=customers&edit=<?= $c['id'] ?>">
                    Edit
                </a>

                <form method="POST" action="index.php?page=customers" style="display:inline">
                    <input type="hidden" name="id" value="<?= $c['id'] ?>">

                    <button type="submit" name="hapus_customer" onclick="return confirm('Hapus customer ini?')">
                        Hapus
                    </button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>