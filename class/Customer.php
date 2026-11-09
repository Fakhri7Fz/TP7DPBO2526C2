<?php
class Customer
{
    // Koneksi dan nama tabel digunakan oleh seluruh operasi customer.
    private $conn;
    private $table = "customers";

    public $id;
    public $nama;
    public $no_hp;

    public function __construct($db)
    {
        // Terima koneksi dari index.php agar kelas tidak membuat koneksi sendiri.
        $this->conn = $db;
    }

    // READ: Mengambil seluruh pelanggan
    public function getAll()
    {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        // Kembalikan statement agar view dapat mengambil daftar customer.
        return $stmt;
    }

    // CREATE: Menambahkan pelanggan
    public function create()
    {
        $query = "INSERT INTO " . $this->table . "
                  (nama, no_hp)
                  VALUES (:nama, :no_hp)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nama", $this->nama);
        $stmt->bindParam(":no_hp", $this->no_hp);

        // Jalankan query setelah nilai properti diikat ke parameter.
        return $stmt->execute();
    }

    // UPDATE: Mengubah data pelanggan
    public function update()
    {
        $query = "UPDATE " . $this->table . "
                  SET nama = :nama,
                      no_hp = :no_hp
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":nama", $this->nama);
        $stmt->bindParam(":no_hp", $this->no_hp);

        return $stmt->execute();
    }

    // DELETE: Menghapus pelanggan
    public function delete()
    {
        $query = "DELETE FROM " . $this->table . "
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }
}
?>