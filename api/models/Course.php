<?php
class Course {
    private $conn;
    private $table = 'courses';
    
    public $id;
    public $name;
    public $description;
    public $preview;
    public $main_category_name;
    public $created_at;
    public $updated_at;

    
    public function __construct() {
        $this->conn = Database::getConnection();
    }
    
    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    }
