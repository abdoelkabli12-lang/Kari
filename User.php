<?php

// namespace App\User;

// require 'vendor/autoload.php';
require  'email_send.php';
//require 'otp_verify.php';
require_once 'Database.php';





$instance = Database::get_instance();
$db = $instance->connection;

class User {
    protected PDO $db;
    public string $name;
    public string $username;
    public string $phone;
    public string $password;
    public string $email;

    // Constructor to initialize the properties
    public function __construct(PDO $db) {
//        $this->name = $name;
//        $this->username = $username;
//        $this->phone = $phone;
//        $this->password = $password;
//        $this->email = $email;
           $this->db = $db;

    }

    public function UserLogin($email, $phone, $password):void {


        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return;
        }

        // Validate input
        if (empty($email) || empty($password)) {
            header("Location: User.php?error=login");
            exit;
        }

        // Fetch user
        $stmt = $this->db->prepare(
            "SELECT id, password, role FROM users WHERE email = :email and phone = :phone"
        );
        $stmt->execute(['email' => $email, 'phone' => $phone]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            header("Location: index.php?error=user_not_found");
            exit;
        }
        $user_id = $user['id'];
        $hashedPasswordFromDb = $user['password'];
        $_SESSION['role'] = $user['role'];


        // Verify password
        if (!password_verify($password, $hashedPasswordFromDb)) {
            header("Location: User.php?error=wrong_password");
            exit;
        }

        // Store TEMP session (OTP pending)
        $_SESSION['pending_user'] = $email;
        $_SESSION['pending_user_id'] = $user_id;



        // Send OTP
        if (!sendOTP($email)) {
            header("Location: otp_verify.php?error=otp_failed");
            exit;
        }

        // Redirect to OTP verification
        header("Location: otp_verify.php");
        exit;
    }



// class SignUp
// {
//     private mysqli $db;

//     public function __construct($db)
//     {
//         $this->db = $db;
//     }

    public function UserSignUp($name, $username, $phone, $email, $password): void
    {

        $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return;
        }

        $stmt = $this->db->prepare("SELECT id FROM users WHERE Email = :email and phone = :phone LIMIT 1");
        $stmt->execute(['email' => $email, 'phone' => $phone]);


        if ($stmt->fetch()) {
            // Email already exists
            header("Location: signup.php?error=email_exists");
            exit;
        }

        // 2️⃣ Insert new user
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, UserName, phone, email, password) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$name, $username, $phone, $email, $hashed_pass]);

        $_SESSION['role_user'] =  $email;


        header("Location: role_selection.php?success=signup");
        exit;
    }

    public function UserLogout(): void{
        $_SESSION = [];
        session_destroy();
            header("Location: signup.php");
            exit();

        }

}


$user = new User($db);



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // LOGIN
    if (isset($_POST['login'])) {
        $user->UserLogin($_POST['email'], $_POST['phone'], $_POST['password']);
    }

    // SIGNUP
    elseif (isset($_POST['signup'])) {
        $user->UserSignUp(
            $_POST['name'],
            $_POST['username'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['password']
        );
    }

    // LOGOUT
    elseif (isset($_POST['logout'])) {
        $user->UserLogout();
    }
}


?>
