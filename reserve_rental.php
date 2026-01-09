<?php

session_start();

require_once "Database.php";

$instance = Database::get_instance();
$db = $instance->connection;

#[AllowDynamicProperties]
class  reserve_rental{
    public function  __construct($db){
        $this->db = $db;
    }

    public function reserveRental($rental_id, $start_date, $end_date, $location, $phone, $email, $name): void{
    $stmt  = $this->db->prepare("INSERT INTO reservations(rental_id, user_id, start_date, end_date, location, phone, email, name) VALUES(?, ?, ?, ?, ?, ? ,?, ?)");
        $stmt->execute([$rental_id, $_SESSION['user_id'], $start_date, $end_date, $location, $phone, $email, $name]);
        header("Location: booking_form.php");
    }
}

$reserve_rental = new reserve_rental($db);

$reserve_rental->reserveRental($_POST['rental_id'], $_POST['checkIn'], $_POST['checkOut'], $_POST['travelerLocation'], $_POST['phone'], $_POST['email'], $_POST['fullName']);