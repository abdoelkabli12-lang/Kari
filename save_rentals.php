<?php

require_once 'Database.php';

session_start();

$instance = Database::get_instance();
$db = $instance->connection;
class SaveRentals {
    protected PDO $db;
    public function __construct($db){
        $this->db = $db;
    }

    public function saveRentals(int $hostId, string $hostName, string $rentalType, string $startDate, string $endDate, string $location, float $price, string $description): void{
        $stmt = $this->db->prepare("INSERT INTO accommodation(host_id, host_name, rental_type, start_date, end_date, location, price, description) VALUES (:hostId, :hostName, :rentalType, :startDate, :endDate, :location, :price, :description)");
        $stmt->execute(['hostId' => $hostId, 'hostName' => $hostName, 'rentalType' => $rentalType, 'startDate' => $startDate, 'endDate' => $endDate, 'location' => $location, 'price' => $price , 'description' => $description]);
    }

    public function getAccId(int $hostId): int{
        $stmt = $this->db->prepare("SELECT id FROM accommodation WHERE host_id = :hostId");
        $stmt->execute(['hostId' => $hostId]);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }
    public function saveImages(int $accId, $image):void {
        $image =  $_FILES ['image'];
        foreach ($image as $file) {

        }
    }
}

$rental = new SaveRentals($db);

$accId = $rental->getAccId($_SESSION['user_id']);

$rental->saveRentals($_SESSION['user_id'], $_POST['hostName'], $_POST['rentalType'], $_POST['startDate'], $_POST['endDate'], $_POST['location'], $_POST['price'], $_POST['description']);
