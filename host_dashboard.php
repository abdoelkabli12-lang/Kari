<?php
session_start();
require_once ('Database.php');

$instance = Database::get_instance();
$db = $instance->connection;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rental_id = $_POST['rental_id'] ?? null;
    $action = $_POST['action'] ?? '';
    $_SESSION["rental_id"] = $rental_id;


    if ($rental_id && $action) {
        switch ($action) {
            case 'delete':
                header("Location: delete_rentals.php?id=$rental_id");
                exit();
            case 'view':
                header("Location: view_rentals.php?id=$rental_id");
                exit();
            case 'edit':
                header("Location: edit_rentals.php?id=$rental_id");
                exit();
        }
    }
}

// Fetch rentals for this host
$stmt = $db->prepare("SELECT * FROM accommodation WHERE host_id = :host_id");
$stmt->execute(['host_id' => $_SESSION['user_id']]);
$rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT role FROM users WHERE email = :email");
$stmt->execute(['email' => $_SESSION['user']]);
$user = $stmt->fetch(PDO::FETCH_COLUMN);

$userEmail = $_SESSION['user'] ?? 'Guest';

if (isset($_SESSION['user'])) {
    $email = $_SESSION['user'];
    $emailHash = md5(strtolower(trim($email)));
    $gravatarUrl = "https://www.gravatar.com/avatar/$emailHash?d=identicon&s=80";
} else {
    // default avatar when not logged in
    $gravatarUrl = "https://www.gravatar.com/avatar/?d=mp&s=80";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Host Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        // JavaScript for delete confirmation
        function confirmDelete(rentalId, rentalType) {
            return confirm(`Are you sure you want to delete "${rentalType}"? This action cannot be undone.`);
        }
    </script>
</head>

<body class="bg-gray-100 min-h-screen">

<header>
    <div class="absolute right-[10rem] top-2">
        <div class="absolute right-20 top-2 flex gap-1 items-center">
            <p class=" text-black ml-auto mr-4 font-semibold">
                <?php echo htmlspecialchars($userEmail); ?>
            </p>
            <img src="<?= $gravatarUrl ?>" class=" rounded-full w-10 h-10" alt="User avatar">
            <div>
                <?php echo $user ?>
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
    <h1 class="text-2xl font-bold text-indigo-600">Host Dashboard</h1>
    <a href="add_rentals.php"
       class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition duration-200">
        <i class="fas fa-plus mr-2"></i> Add Rental
    </a>
</nav>

<!-- Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-8 py-10">

    <!-- Stats Bar -->
    <div class="mb-8 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="bg-indigo-100 p-3 rounded-lg">
                    <i class="fas fa-home text-indigo-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Total Rentals</p>
                    <p class="text-2xl font-bold"><?php echo count($rentals); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-calendar-check text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Active</p>
                    <p class="text-2xl font-bold"><?php
                        $active = 0;
                        foreach($rentals as $rental) {
                            if(strtotime($rental['end_date']) > time()) {
                                $active++;
                            }
                        }
                        echo $active;
                        ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-dollar-sign text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Avg. Price</p>
                    <p class="text-2xl font-bold">$<?php
                        if(count($rentals) > 0) {
                            $total = 0;
                            foreach($rentals as $rental) {
                                $total += $rental['price'];
                            }
                            echo number_format($total / count($rentals), 2);
                        } else {
                            echo "0.00";
                        }
                        ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-map-marker-alt text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Locations</p>
                    <p class="text-2xl font-bold"><?php
                        $locations = array_unique(array_column($rentals, 'location'));
                        echo count($locations);
                        ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Rentals Grid -->
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">My Rentals</h2>
        <p class="text-gray-500">Showing <?php echo count($rentals); ?> properties</p>
    </div>

    <?php if(count($rentals) > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach($rentals as $rental):
                // Format dates
                $start_date = date('d M Y', strtotime($rental['start_date']));
                $end_date = date('d M Y', strtotime($rental['end_date']));

                // Determine if rental is active
                $is_available = strtotime($rental['end_date']) > time();
                $status = $rental['rental_status'];

                // Get rental type icon
                $type_icons = [
                        'House' => 'fa-house',
                        'Villa' => 'fa-house-chimney-window',
                        'Hotel' => 'fa-hotel',
                        'Apartment' => 'fa-building'
                ];
                $type_icon = $type_icons[$rental['rental_type']] ?? 'fa-home';
                ?>
                <!-- Rental Card -->
                <div class="bg-white rounded-xl shadow hover:shadow-lg transition duration-300 overflow-hidden">
                    <!-- Rental Image -->
                    <div class="relative h-48 overflow-hidden">
                        <img src="<?php echo !empty($rental['image']) ? $rental['image'] : 'https://source.unsplash.com/400x250/?' . urlencode($rental['rental_type']); ?>"
                             class="w-full h-full object-cover transform hover:scale-105 transition duration-500"
                             alt="<?php echo htmlspecialchars($rental['rental_type']); ?>">
                        <!-- Status Badge -->
                        <div class="absolute top-3 left-3">
                            <?php
                            $badge_class = match($status) {
                                'Active' => 'bg-green-100 text-green-800',
                                'Inactive' => 'bg-red-100 text-red-800',
                                'Pending' => 'bg-yellow-100 text-yellow-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                            ?>
                            <span class="px-3 py-1 rounded-full text-xs font-medium <?php echo $badge_class; ?>">
                            <?php echo $status; ?>
                            </span>
                        </div>
                        <!-- Price Badge -->
                        <div class="absolute top-3 right-3">
                            <span class="px-3 py-1 rounded-full bg-white/90 backdrop-blur-sm text-sm font-bold text-indigo-700">
                                $<?php echo number_format($rental['price'], 2); ?><span class="text-xs font-normal">/night</span>
                            </span>
                        </div>
                    </div>

                    <div class="p-5">
                        <!-- Header -->
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="text-xl font-bold text-gray-800 truncate"><?php echo htmlspecialchars($rental['rental_type']); ?></h2>
                                <div class="flex items-center mt-1 text-gray-500">
                                    <i class="fas <?php echo $type_icon; ?> text-sm mr-2"></i>
                                    <span class="text-sm"><?php echo htmlspecialchars($rental['rental_type']); ?></span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="flex items-center text-yellow-500">
                                    <i class="fas fa-star text-sm"></i>
                                    <span class="ml-1 text-sm font-medium">4.8</span>
                                </div>
                                <span class="text-xs text-gray-500">5 reviews</span>
                            </div>
                        </div>

                        <!-- Host Info -->
                        <div class="mt-3 flex items-center">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-indigo-600 text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium"><?php echo htmlspecialchars($rental['host_name']); ?></p>
                                <p class="text-xs text-gray-500">Host</p>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="mt-4 flex items-center text-gray-600">
                            <i class="fas fa-map-marker-alt text-red-400 mr-2"></i>
                            <span class="text-sm truncate"><?php echo htmlspecialchars($rental['location']); ?></span>
                        </div>

                        <!-- Description -->
                        <p class="mt-4 text-gray-600 text-sm line-clamp-2">
                            <?php echo htmlspecialchars($rental['description']); ?>
                        </p>

                        <!-- Dates -->
                        <div class="mt-6 pt-4 border-t border-gray-100">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-xs text-gray-500">From</p>
                                    <p class="text-sm font-medium"><?php echo $start_date; ?></p>
                                </div>
                                <div class="text-gray-300">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">To</p>
                                    <p class="text-sm font-medium"><?php echo $end_date; ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 flex justify-between pt-4 border-t border-gray-100">
                            <!-- Edit Button Form -->
                            <form method="POST" action="" style="display: inline;">
                                <input type="hidden" name="rental_id" value="<?php echo $rental['id']; ?>">
                                <input type="hidden" name="action" value="edit">
                                <button type="submit" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm flex items-center">
                                    <i class="fas fa-edit mr-2"></i> Edit
                                </button>
                            </form>

                            <!-- Delete Button Form -->
                            <form method="POST" action="" style="display: inline;"">
                                <input type="hidden" name="rental_id" value="<?php echo $rental['id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-sm flex items-center">
                                    <i class="fas fa-trash mr-2"></i> Delete
                                </button>
                            </form>

                            <!-- View Button Form -->
                            <form method="POST" action="rental_details.php" style="display: inline;">
                                <input type="hidden" name="rental_id" value="<?php echo $rental['id']; ?>">
                                <input type="hidden" name="action" value="view">
                                <button type="submit" class="text-gray-600 hover:text-gray-800 font-medium text-sm flex items-center">
                                    <i class="fas fa-eye mr-2"></i> View
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div class="text-center bg-white p-12 rounded-xl shadow">
            <div class="max-w-md mx-auto">
                <div class="w-24 h-24 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-home text-indigo-600 text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">No rentals yet</h2>
                <p class="text-gray-500 mb-6">
                    Start by adding your first rental property. List it to reach thousands of travelers.
                </p>
                <a href="add_rentals.php"
                   class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 transition duration-200 font-medium">
                    <i class="fas fa-plus mr-2"></i> Add Your First Rental
                </a>
            </div>
        </div>
    <?php endif; ?>
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
    // Add smooth hover effects for images
    document.addEventListener('DOMContentLoaded', function() {
        const rentalCards = document.querySelectorAll('.bg-white.rounded-xl.shadow');

        rentalCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
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