<?php

require_once('Database.php');

$instance = Database::get_instance();
$db = $instance->connection;


#[AllowDynamicProperties]
class ShowRentalsAdmin {
    public function  __construct($db) {
        $this->db = $db;
    }

    public function getRentals(): array {
        $stmt = $this->db->prepare("SELECT * FROM accommodation");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateRental($id, $status) {
        $stmt = $this->db->prepare("UPDATE accommodation SET status = :status WHERE id = :id");
        $stmt->execute([":status" => $status, ":id" => $id]);
    }

}