<?php

use JetBrains\PhpStorm\NoReturn;

require_once ("Database.php");

$instance = Database::get_instance();
$db = $instance->connection;

class  ManageRentals
{
    public function __construct($db){
        $this->db = $db;
    }

    public function EditRental($rental_id, $action): void{
        $_SESSION["rental_id"] = $rental_id;
        header("Location: edit_rentals.php");
        exit();
    }
    public function DeleteRental($rental_id, $action): void{
        $_SESSION["rental_id"] = $rental_id;
        header("Location: delete_rentals.php");
        exit();
    }

    public function ViewRental($rental_id, $action): void{
        $_SESSION["rental_id"] = $rental_id;
        header("Location: view_rentals.php");
        exit();
    }

}

$manage = new ManageRentals($db);

$rental_id = $_POST['rental_id'] ;
$action = $_POST['action'] ?? '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($rental_id && $action) {
        switch ($action) {
            case 'delete':
                $manage->DeleteRental($rental_id, $action);
                break;
            case 'view':
                $manage->ViewRental($rental_id, $action);
                break;
            case 'edit':
                $manage->EditRental($rental_id, $action);
                break;
        }
    }
}