<?php

require_once 'Database.php';
require 'User.php';

$instance = Database::get_instance();
$db = $instance->connection;
class Role extends User
{
    public function __construct($db)
    {
        parent::__construct($db);
    }

    public function RoleSelection(string $role, $email): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET role = :role WHERE email = :email'
        );
        $stmt->execute(['role' => $role, 'email' => $email]);
    }
}

$role = new Role($db);

if (isset($_POST['role'])) {
        if (!isset($_SESSION['role_user'])) {
            die('Unauthorized');
        }
        $role->RoleSelection($_POST['role'], $_SESSION['role_user']);
    }
header("Location: signup.php");