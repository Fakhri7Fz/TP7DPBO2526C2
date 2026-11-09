<?php
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/class/Game.php";
require_once __DIR__ . "/class/Customer.php";
require_once __DIR__ . "/class/Rental.php";

// Siapkan satu koneksi database untuk dipakai seluruh kelas selama request ini.
$database = new Database();
$db = $database->getConnection();

// Bagian awal index.php menangani aksi POST sebelum HTML halaman ditampilkan.
// Proses tambah atau update game
if (isset($_POST['simpan'])) {
    $game = new Game($db);

    $game->nama = $_POST['nama'];
    $game->platform = $_POST['platform'];
    $game->genre = $_POST['genre'];
    $game->harga_sewa = $_POST['harga_sewa'];

    if (!empty($_POST['id'])) {
        $game->id = $_POST['id'];
        $game->update();
    } else {
        $game->create();
    }

    // Kembali ke daftar game setelah data berhasil diproses.
    header("Location: index.php?page=games");
    exit;
}

// Proses hapus game
if (isset($_POST['hapus'])) {
    $game = new Game($db);
    $game->id = $_POST['id'];

    // Cegah penghapusan game yang masih dipakai pada data rental.
    $stmt = $db->prepare("SELECT COUNT(*) FROM rentals WHERE game_id = :id");
    $stmt->execute([":id" => $game->id]);
    if ($stmt->fetchColumn() > 0) {
        header("Location: index.php?page=games&error=game_in_use");
        exit;
    }

    $game->delete();
    header("Location: index.php?page=games");
    exit;
}
?>

<?php
// CREATE / UPDATE CUSTOMER
if (isset($_POST['simpan_customer'])) {
    $customer = new Customer($db);

    $customer->nama = $_POST['nama'];
    $noHp = $_POST['no_hp'] ?? '';

    // Pastikan nomor telepon hanya berisi angka, termasuk saat validasi browser dilewati.
    if (!is_string($noHp) || !preg_match('/^[0-9]+$/', $noHp)) {
        header("Location: index.php?page=customers&error=invalid_phone");
        exit;
    }
    $customer->no_hp = $noHp;

    if (!empty($_POST['id'])) {
        $customer->id = $_POST['id'];
        $customer->update();
    } else {
        $customer->create();
    }

    // Kembali ke daftar customer setelah data berhasil diproses.
    header("Location: index.php?page=customers");
    exit;
}

// DELETE CUSTOMER
if (isset($_POST['hapus_customer'])) {
    $customer = new Customer($db);
    $customer->id = $_POST['id'];

    // Cegah penghapusan customer yang masih dipakai pada data rental.
    $stmt = $db->prepare("SELECT COUNT(*) FROM rentals WHERE customer_id = :id");
    $stmt->execute([":id" => $customer->id]);
    if ($stmt->fetchColumn() > 0) {
        header("Location: index.php?page=customers&error=customer_in_use");
        exit;
    }

    $customer->delete();
    header("Location: index.php?page=customers");
    exit;
}
?>

<?php
// CREATE / UPDATE RENTAL
if (isset($_POST['simpan_rental'])) {
    $rental = new Rental($db);

    $rental->customer_id = $_POST['customer_id'];
    $rental->game_id = $_POST['game_id'];
    $rental->tanggal_sewa = $_POST['tanggal_sewa'];
    $durasi = $_POST['durasi'] ?? '';

    // Pastikan durasi valid sebelum digunakan untuk menghitung biaya rental.
    if (filter_var($durasi, FILTER_VALIDATE_INT) === false || (int) $durasi < 1) {
        header("Location: index.php?page=rentals&error=invalid_duration");
        exit;
    }
    $rental->durasi = (int) $durasi;

    if (!empty($_POST['id'])) {
        $rental->id = $_POST['id'];
        $berhasil = $rental->update();
    } else {
        $berhasil = $rental->create();
    }

    if (!$berhasil) {
        header("Location: index.php?page=rentals&error=invalid_game");
        exit;
    }

    // Kembali ke daftar rental setelah data berhasil diproses.
    header("Location: index.php?page=rentals");
    exit;
}

// DELETE RENTAL
if (isset($_POST['hapus_rental'])) {
    $rental = new Rental($db);
    $rental->id = $_POST['id'];
    $rental->delete();

    header("Location: index.php?page=rentals");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>PlayZone</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header>
        <h1>🎮 PlayZone</h1>
        <p>Game & Console Rental</p>
    </header>

    <nav>
        <a href="index.php?page=games">Games</a>
        <a href="index.php?page=customers">Customers</a>
        <a href="index.php?page=rentals">Rentals</a>
    </nav>

    <main>
        <?php
        // Parameter page menentukan view yang ditampilkan; default-nya halaman games.
        $page = $_GET['page'] ?? 'games';

        if ($page === 'games') {
            include __DIR__ . "/view/games.php";
        } elseif ($page === 'customers') {
            include __DIR__ . "/view/customers.php";
        } elseif ($page === 'rentals') {
            include __DIR__ . "/view/rentals.php";
        } else {
            echo "<h2>Halaman tidak ditemukan</h2>";
        }
        ?>
    </main>

</body>

</html>