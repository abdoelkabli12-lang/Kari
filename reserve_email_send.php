<?php

require_once("Database.php");
require_once("reserve_email.php");

$instance = Database::get_instance();
$db = $instance->connection;

#[AllowDynamicProperties]
class ReserveEmail {
public function  __construct($db)
{
    $this->db = $db;
}

public function sendEmailRes ($userId, $rentalId, $email): void
{
    if (!sendRes($email, $rentalId)){
        header("Location: payment.php?error = email_missing");
        exit;
    }

    header("Location: index.php?success=email_sent");
}
}

$sendEmail = new ReserveEmail($db);

$sendEmail->sendEmailRes($_SESSION['user_id'], $_SESSION['reserve_id'], $_SESSION['user']);






