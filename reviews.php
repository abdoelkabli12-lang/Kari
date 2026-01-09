<?php

use JetBrains\PhpStorm\NoReturn;

require_once ("Database.php");

session_start();

$instance = Database::get_instance();
$db = $instance->connection;

#[AllowDynamicProperties]
class  Reviews {

    public function __construct($db) {
        $this->db = $db;
    }
    #[NoReturn]
    public function addReviews($rentalId, $user_id, $rating, $comment): void {
        $stmt = $this->db->prepare("INSERT INTO reviews (rental_id, user_id, rating, comment, created_at) VALUES (:rental_id, :user_id, :rating, :comment, NOW())");
        $stmt->execute([
            ':rental_id' => $rentalId,
            ':user_id' => $user_id,
            ':rating' => $rating,
            ':comment' => $comment
        ]);


        header("Location: rental_details.php");
        exit;
    }

    public function getReviews($rentalId): array {
        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE rental_id = :rental_id");
        $stmt->execute([":rental_id" => $rentalId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
//        header("Location: rental_details.php");
    }
}


