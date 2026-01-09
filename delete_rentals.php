<?php


session_start();
require_once ('Database.php');


$instance = Database::get_instance();
$db = $instance->connection;
class Delete_rentals{
    public function __construct($db){
        $this->db = $db;
    }

    public function delete_rentals($rentalID): void{
    $stmt = $this->db->prepare("DELETE FROM accommodation WHERE id = :rentalID");
    $stmt->execute(['rentalID' => $rentalID]);
    }
}

$delete_rental = new Delete_rentals($db);

$delete_rental->delete_rentals($_SESSION['rental_id']);

header('Location: host_dashboard.php');