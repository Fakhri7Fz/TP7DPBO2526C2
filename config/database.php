<?php
class Database
{
    // Pengaturan server dan database yang digunakan aplikasi.
    private $host = "localhost";
    private $db_name = "playzone";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection()
    {
        $this->conn = null;

        try {
            // Buat koneksi PDO agar query dapat dijalankan dengan prepared statement.
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );

            $this->conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

        } catch (PDOException $e) {
            // echo "Connection error: " . $e->getMessage();
            die("Koneksi database gagal."); // jelaskan kenapa gk pakai echo
        }

        return $this->conn;
    }
}
?>