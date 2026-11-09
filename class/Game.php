<?php
class Game
{
    // Koneksi dan nama tabel digunakan oleh seluruh operasi game.
    private $conn;
    private $table = "games";

    public $id;
    public $nama;
    public $platform;
    public $genre;
    public $harga_sewa;

    public function __construct($db)
    {
        // Terima koneksi dari index.php agar kelas tidak membuat koneksi sendiri.
        $this->conn = $db;
    }

    // READ: Mengambil semua data game
    public function getAll()
    {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        // Kembalikan statement agar view dapat mengambil daftar game.
        return $stmt;
    }

    // CREATE: Menambahkan game baru
    public function create()
    {
        $query = "INSERT INTO " . $this->table . "
                  (nama, platform, genre, harga_sewa)
                  VALUES (:nama, :platform, :genre, :harga_sewa)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nama", $this->nama);
        $stmt->bindParam(":platform", $this->platform);
        $stmt->bindParam(":genre", $this->genre);
        $stmt->bindParam(":harga_sewa", $this->harga_sewa);

        // Jalankan query setelah nilai properti diikat ke parameter.
        return $stmt->execute();
    }

    // UPDATE: Mengubah data game
    public function update()
    {
        $query = "UPDATE " . $this->table . "
                  SET nama = :nama,
                      platform = :platform,
                      genre = :genre,
                      harga_sewa = :harga_sewa
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":nama", $this->nama);
        $stmt->bindParam(":platform", $this->platform);
        $stmt->bindParam(":genre", $this->genre);
        $stmt->bindParam(":harga_sewa", $this->harga_sewa);

        return $stmt->execute();
    }

    // DELETE: Menghapus data game
    public function delete()
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }
}
?>