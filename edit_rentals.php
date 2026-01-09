<?php
session_start();
require_once('Database.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$rental_id = $_SESSION['rental_id'];

    $instance = Database::get_instance();
    $db = $instance->connection;

// Fetch the rental details
    $stmt = $db->prepare("SELECT * FROM accommodation WHERE id = :id AND host_id = :host_id");
    $stmt->execute([
            'id' => $rental_id,
            'host_id' => $_SESSION['user_id']
    ]);
    $rental = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$rental) {
        // Rental doesn't exist or doesn't belong to this host
        header('Location: host_dashboard.php');
        exit();
    }

// Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        $rental_type = $_POST['rental_type'];
        $location = $_POST['location'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $host_name = $_POST['host_name'];
//        $image = $_POST['image'] ?? $rental['image']; // Keep existing if no new image

        // Validate dates
        if (strtotime($end_date) <= strtotime($start_date)) {
            $error = "End date must be after start date";
        } else {
            // Update in database
            $update_stmt = $db->prepare("
            UPDATE accommodation 
            SET rental_type = :rental_type, 
                location = :location, 
                description = :description, 
                price = :price, 
                start_date = :start_date, 
                end_date = :end_date, 
                host_name = :host_name
               WHERE id = :id AND host_id = :host_id
        ");

            $success = $update_stmt->execute([
                    'rental_type' => $rental_type,
                    'location' => $location,
                    'description' => $description,
                    'price' => $price,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'host_name' => $host_name,
                    'id' => $rental_id,
                    'host_id' => $_SESSION['user_id']
            ]);

            if ($success) {
                $_SESSION['success_message'] = "Rental updated successfully!";
            } else {
                $error = "Failed to update rental. Please try again.";
            }
        }
    }
//
//header('Location: host_dashboard.php');
//exit();


// Get user info for header
$stmt = $db->prepare("SELECT role FROM users WHERE email = :email");
$stmt->execute(['email' => $_SESSION['user']]);
$user_role = $stmt->fetch(PDO::FETCH_COLUMN);

$userEmail = $_SESSION['user'] ?? 'Guest';

if (isset($_SESSION['user'])) {
    $email = $_SESSION['user'];
    $emailHash = md5(strtolower(trim($email)));
    $gravatarUrl = "https://www.gravatar.com/avatar/$emailHash?d=identicon&s=80";
} else {
    $gravatarUrl = "https://www.gravatar.com/avatar/?d=mp&s=80";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Rental - Host Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        fadeIn: 'fadeIn 0.3s ease-in-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(-10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-100 min-h-screen">

<!-- Header (same as dashboard) -->
<header>
    <div class="absolute right-[10rem] top-2">
        <div class="absolute right-20 top-2 flex gap-1 items-center">
            <p class=" text-black ml-auto mr-4 font-semibold">
                <?php echo htmlspecialchars($userEmail); ?>
            </p>
            <img src="<?= $gravatarUrl ?>" class=" rounded-full w-10 h-10" alt="User avatar">
            <div>
                <?php echo $user_role ?>
            </div>
        </div>
        <button id="logout-btn" class="absolute right-[22rem] top-3 text-red-400 bg-black pt-1 pb-1 pl-2 pr-2 rounded font-extrabold">
            Logout
        </button>
        <!-- Background Overlay -->
        <div id="logout-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
            <div id="logout-bg" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
            <div class="cont relative w-full max-w-sm rounded-2xl bg-white shadow-xl p-6 animate-fadeIn">
                <h2 class="text-lg font-semibold text-gray-800 text-center mb-6">
                    Do you want to logout from your account?
                </h2>
                <div class="flex gap-4">
                    <button id="cancel-logout" class="w-1/2 rounded-xl border border-gray-300 py-2 text-gray-700 font-medium
               transition-all duration-300 hover:bg-gray-100 hover:scale-[1.02]">
                        Cancel
                    </button>
                    <form action="signup.php" method="post" class="w-1/2">
                        <button type="submit" name="logout"
                                class="w-full rounded-xl bg-red-500 py-2 text-white font-semibold
                 transition-all duration-300 hover:bg-red-600 hover:scale-[1.02]">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Navbar -->
<nav class="bg-white shadow px-8 py-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-indigo-600">Edit Rental</h1>
    <a href="host_dashboard.php"
       class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-200">
        <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
    </a>
</nav>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-8 py-10">
    <!-- Success/Error Messages -->
    <?php if(isset($_SESSION['success_message'])): ?>
        <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
            <?php
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']);
            ?>
        </div>
    <?php endif; ?>

    <?php if(isset($error)): ?>
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <!-- Edit Form Card -->
    <div class="bg-white rounded-xl shadow p-8">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Edit Rental Details</h2>
            <p class="text-gray-500">Update your rental information below</p>
        </div>

        <form method="POST" action="edit_rentals.php" class="space-y-6">
            <input type="hidden" name="rental_id" value="<?php echo $rental['id']; ?>">

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Rental Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-home mr-2 text-indigo-600"></i>Rental Type
                        </label>
                        <select name="rental_type" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200">
                            <option value="House" <?php echo $rental['rental_type'] == 'House' ? 'selected' : ''; ?>>House</option>
                            <option value="Villa" <?php echo $rental['rental_type'] == 'Villa' ? 'selected' : ''; ?>>Villa</option>
                            <option value="Hotel" <?php echo $rental['rental_type'] == 'Hotel' ? 'selected' : ''; ?>>Hotel</option>
                            <option value="Apartment" <?php echo $rental['rental_type'] == 'Apartment' ? 'selected' : ''; ?>>Apartment</option>
                        </select>
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt mr-2 text-red-400"></i>Location
                        </label>
                        <input type="text" name="location" required
                               value="<?php echo htmlspecialchars($rental['location']); ?>"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200"
                               placeholder="Enter rental location">
                    </div>

                    <!-- Price -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-dollar-sign mr-2 text-green-600"></i>Price per night ($)
                        </label>
                        <input type="number" name="price" required min="0" step="0.01"
                               value="<?php echo $rental['price']; ?>"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200"
                               placeholder="0.00">
                    </div>

                    <!-- Image URL -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-image mr-2 text-purple-600"></i>Image URL
                        </label>
                        <input type="url" name="image"
                               value="<?php echo htmlspecialchars($rental['image'] ?? ''); ?>"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200"
                               placeholder="https://example.com/image.jpg">
                        <p class="text-xs text-gray-500 mt-1">Leave empty to use default image</p>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Host Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-indigo-600"></i>Host Name
                        </label>
                        <input type="text" name="host_name" required
                               value="<?php echo htmlspecialchars($rental['host_name']); ?>"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200"
                               placeholder="Enter your name">
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>Start Date
                            </label>
                            <input type="date" name="start_date" required
                                   value="<?php echo date('Y-m-d', strtotime($rental['start_date'])); ?>"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-check mr-2 text-green-600"></i>End Date
                            </label>
                            <input type="date" name="end_date" required
                                   value="<?php echo date('Y-m-d', strtotime($rental['end_date'])); ?>"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200">
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-align-left mr-2 text-gray-600"></i>Description
                        </label>
                        <textarea name="description" rows="4" required
                                  class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200"
                                  placeholder="Describe your rental property..."><?php echo htmlspecialchars($rental['description']); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="mt-8 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-700 mb-4">
                    <i class="fas fa-eye mr-2"></i>Preview
                </h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-home text-indigo-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-medium"><?php echo htmlspecialchars($rental['rental_type']); ?></p>
                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($rental['location']); ?></p>
                            <p class="text-sm font-bold text-indigo-600">$<?php echo number_format($rental['price'], 2); ?>/night</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-4">
                <a href="host_dashboard.php"
                   class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition duration-200">
                    Cancel
                </a>
                <button type="submit" name="update"
                        class="px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition duration-200">
                    <i class="fas fa-save mr-2"></i> Update Rental
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Footer -->
<div class="max-w-7xl mx-auto px-8 py-6 border-t border-gray-200 mt-10">
    <div class="flex flex-col md:flex-row justify-between items-center text-gray-500 text-sm">
        <p>© <?php echo date('Y'); ?> Host Dashboard. All rights reserved.</p>
        <div class="mt-2 md:mt-0">
            <a href="#" class="hover:text-indigo-600 mr-4">Privacy Policy</a>
            <a href="#" class="hover:text-indigo-600 mr-4">Terms of Service</a>
            <a href="#" class="hover:text-indigo-600">Contact Support</a>
        </div>
    </div>
</div>

<script>
    // Date validation
    document.addEventListener('DOMContentLoaded', function() {
        const startDate = document.querySelector('input[name="start_date"]');
        const endDate = document.querySelector('input[name="end_date"]');

        // Set minimum date to today for start date
        const today = new Date().toISOString().split('T')[0];
        startDate.min = today;

        // Update end date min when start date changes
        startDate.addEventListener('change', function() {
            endDate.min = this.value;
        });

        // Logout modal functionality
        const logoutBtn = document.getElementById('logout-btn');
        const logoutModal = document.getElementById('logout-modal');
        const cancelLogout = document.getElementById('cancel-logout');
        const logoutBg = document.getElementById('logout-bg');

        if (logoutBtn) {
            logoutBtn.addEventListener('click', function() {
                logoutModal.classList.remove('hidden');
            });
        }

        if (cancelLogout) {
            cancelLogout.addEventListener('click', function() {
                logoutModal.classList.add('hidden');
            });
        }

        if (logoutBg) {
            logoutBg.addEventListener('click', function() {
                logoutModal.classList.add('hidden');
            });
        }
    });
</script>

<script type="text/javascript" src="script.js"></script>
</body>
</html>