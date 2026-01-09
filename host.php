<?php


require_once 'Database.php';
require 'Role.php';

$instance = Database::get_instance();
$db = $instance->connection;

class Host extends Role
{
    public function __construct(PDO $db)
    {
        parent::__construct($db);
    }

    public function HeadToDashboard()
    {
        $stmt = $this->db->prepare("SELECT role FROM users WHERE email = :email");
        $stmt->execute(['email' => $_SESSION['user']]);
        return $stmt->fetch(PDO::FETCH_COLUMN);

    }
}

$host = new Host($db);

if ($host->HeadToDashboard() === "host") {
    header("Location: host_dashboard.php");
    exit;
}
