<?php
class Rental
{
    // Koneksi dan nama tabel digunakan oleh seluruh operasi rental.
    private $conn;
    private $table = "rentals";

    public $id;
    public $customer_id;
    public $game_id;
    public $tanggal_sewa;
    public $durasi;
    public $biaya;

    public function __construct($db)
    {
        // Terima koneksi dari index.php agar kelas tidak membuat koneksi sendiri.
        $this->conn = $db;
    }

    // Hitung biaya dari tarif game di database agar tidak bergantung pada input form.
    private function hitungBiaya()
    {
        $query = "SELECT harga_sewa FROM games WHERE id = :game_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":game_id" => $this->game_id]);
        $hargaSewa = $stmt->fetchColumn();

        if ($hargaSewa === false) {
            return false;
        }

        // Total biaya adalah tarif harian game dikalikan lama penyewaan.
        $this->biaya = (int) $hargaSewa * (int) $this->durasi;
        return true;
    }

    // READ: Mengambil rental beserta nama customer dan game
    public function getAll()
    {
        $query = "SELECT rentals.*, 
                     customers.nama AS nama_customer,
                     games.nama AS nama_game
              FROM rentals
              JOIN customers ON rentals.customer_id = customers.id
              JOIN games ON rentals.game_id = games.id
              ORDER BY rentals.id ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        // Hasil query berisi rental beserta nama customer dan game terkait.
        return $stmt;
    }

    // CREATE: Menambahkan rental
    public function create()
    {
        if (!$this->hitungBiaya()) {
            return false;
        }

        // Simpan rental bersama biaya yang sudah dihitung dari tarif game.
        $query = "INSERT INTO " . $this->table . "
                  (customer_id, game_id, tanggal_sewa, durasi, biaya)
                  VALUES (:customer_id, :game_id, :tanggal_sewa, :durasi, :biaya)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":customer_id", $this->customer_id);
        $stmt->bindParam(":game_id", $this->game_id);
        $stmt->bindParam(":tanggal_sewa", $this->tanggal_sewa);
        $stmt->bindParam(":durasi", $this->durasi);
        $stmt->bindParam(":biaya", $this->biaya);

        return $stmt->execute();
    }

    // UPDATE: Mengubah rental
    public function update()
    {
        if (!$this->hitungBiaya()) {
            return false;
        }

        $query = "UPDATE " . $this->table . "
                  SET customer_id = :customer_id,
                      game_id = :game_id,
                      tanggal_sewa = :tanggal_sewa,
                      durasi = :durasi,
                      biaya = :biaya
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":customer_id", $this->customer_id);
        $stmt->bindParam(":game_id", $this->game_id);
        $stmt->bindParam(":tanggal_sewa", $this->tanggal_sewa);
        $stmt->bindParam(":durasi", $this->durasi);
        $stmt->bindParam(":biaya", $this->biaya);

        return $stmt->execute();
    }

    // DELETE: Menghapus rental
    public function delete()
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }
}
?>