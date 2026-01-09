<?php

require 'User.php';
require_once 'Database.php';


$instance = Database::get_instance();
$db = $instance->connection;

class UserROle extends User
{
    public function __construct(PDO $db)
    {
        parent::__construct($db);
    }

    public function HeadtoDashboard()
    {
        $stmt = $this->db->prepare("SELECT role FROM users WHERE email = :email");
        $stmt->execute(['email' => $_SESSION['user']]);
        return $stmt->fetch(PDO::FETCH_COLUMN);

    }
}

$UserRole = new UserROle($db);

if ($UserRole->HeadtoDashboard() === "traveler") {
    header("Location: index.php");
    exit;
}elseif ($UserRole->HeadtoDashboard() === "host") {
    header("Location: host_dashboard.php");
    exit;
}elseif ($UserRole->HeadtoDashboard() === "admin") {
    header("Location: admin_dashboard.php");
    exit;
}



?>
