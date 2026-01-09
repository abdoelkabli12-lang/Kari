<?php
// reservations.php
session_start();

// Sample reservation data (in real app, this would come from database)
require_once("Database.php");
//require ("reserve_email_send.php");


$instance = Database::get_instance();
$db = $instance->connection;



$stmt = $db->prepare("SELECT * FROM reservations WHERE user_id = :user_id");
$stmt->execute([":user_id" => $_SESSION['user_id']]);
$reservations = $stmt->fetchAll();



// Filter reservations by status if requested
$statusFilter = $_GET['reservation_status'] ?? 'all';
$filteredReservations = array_filter($reservations, function($res) use ($statusFilter) {
    return $statusFilter === 'all' || $res['reservation_status'] === strtolower($statusFilter);
});

// Get counts for tabs
$counts = [
    'all' => count($reservations),
    'confirmed' => count(array_filter($reservations, fn($r) => strtolower(['reservation_status'] == 'Confirmed'))),
    'pending' => count(array_filter($reservations, fn($r) => strtolower($r['reservation_status'] === 'Pending'))),
    'cancelled' => count(array_filter($reservations, fn($r) => strtolower($r['reservation_status'] === 'Cancelled')))
];

function formatDate($date) {
    return date('M j, Y', strtotime($date));
}

function getStatusBadgeClass($status) {
    return match($status) {
        'confirmed' => 'bg-green-100 text-green-800',
        'pending' => 'bg-yellow-100 text-yellow-800',
        'cancelled' => 'bg-red-100 text-red-800',
        default => 'bg-gray-100 text-gray-800'
    };
}

function getStatusText($status) {
    return $status;
}

if (isset($_GET['reservation_status'])) {}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Reservations</h1>
                    <p class="text-gray-600 mt-1">Manage your upcoming and past stays</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-2"></i>New Booking
                    </button>
                    <div class="relative">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                            JD
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-blue-50 text-blue-600">
                        <i class="fas fa-calendar-alt text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Bookings</p>
                        <p class="text-2xl font-bold"><?= $counts['all'] ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-green-50 text-green-600">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Confirmed</p>
                        <p class="text-2xl font-bold"><?= $counts['confirmed'] ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-yellow-50 text-yellow-600">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Pending</p>
                        <p class="text-2xl font-bold"><?= $counts['pending'] ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-red-50 text-red-600">
                        <i class="fas fa-times-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Cancelled</p>
                        <p class="text-2xl font-bold"><?= $counts['cancelled'] ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-xl shadow-sm mb-6 border border-gray-100">
            <div class="border-b border-gray-200">
                <nav class="flex flex-wrap -mb-px">
                    <a href="?reservation_status=all"
                       class="<?= $statusFilter === 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> inline-flex items-center px-4 py-3 border-b-2 font-medium text-sm transition">
                       <i class="fas fa-list mr-2"></i> All Reservations
                       <span class="ml-2 bg-gray-100 text-gray-800 text-xs font-medium px-2 py-1 rounded-full"><?= $counts['all'] ?></span>
                    </a>
                    <a href="?reservation_status=Confirmed"
                       class="<?= $statusFilter === 'confirmed' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> inline-flex items-center px-4 py-3 border-b-2 font-medium text-sm transition ml-4">
                       <i class="fas fa-check-circle mr-2"></i> Confirmed
                       <span class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full"><?= $counts['confirmed'] ?></span>
                    </a>
                    <a href="?reservation_status=Pending"
                       class="<?= $statusFilter === 'pending' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> inline-flex items-center px-4 py-3 border-b-2 font-medium text-sm transition ml-4">
                       <i class="fas fa-clock mr-2"></i> Pending
                       <span class="ml-2 bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded-full"><?= $counts['pending'] ?></span>
                    </a>
                    <a href="?reservation_status=Cancelled"
                       class="<?= $statusFilter === 'cancelled' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> inline-flex items-center px-4 py-3 border-b-2 font-medium text-sm transition ml-4">
                       <i class="fas fa-times-circle mr-2"></i> Cancelled
                       <span class="ml-2 bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full"><?= $counts['cancelled'] ?></span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Search and Filter Bar -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
                <div class="relative flex-1 md:max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text"
                           class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                           placeholder="Search reservations...">
                </div>
                <div class="flex space-x-3">
                    <select class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option>Sort by: Date</option>
                        <option>Sort by: Price</option>
                        <option>Sort by: Name</option>
                    </select>
                    <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-filter mr-2"></i>Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Reservations List -->
        <div class="space-y-6">
            <?php if (empty($filteredReservations)): ?>
                <div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100">
                    <div class="max-w-md mx-auto">
                        <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-6">
                            <i class="fas fa-calendar-times text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">No reservations found</h3>
                        <p class="text-gray-600 mb-6">You don't have any <?= $statusFilter ?> reservations at the moment.</p>
                        <a href="#" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition inline-block">
                            <i class="fas fa-plus mr-2"></i>Book a Stay
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($filteredReservations as $reservation): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row lg:items-start">
                            <!-- Property Image -->
                            <div class="lg:w-1/4 mb-4 lg:mb-0 lg:mr-6">
                                <div class="relative overflow-hidden rounded-lg">
<!--                                    <img src="--><?php //= $reservation['property_image'] ?><!--"-->
<!--                                         alt="--><?php //= htmlspecialchars($reservation['name']) ?><!--"-->
<!--                                         class="w-full h-48 object-cover">-->
                                    <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-xs font-medium <?= getStatusBadgeClass($reservation['reservation_status']) ?>">
                                        <?= getStatusText($reservation['reservation_status']) ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Reservation Details -->
                            <div class="lg:w-2/4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-1"><?= htmlspecialchars($reservation['name']) ?></h3>
                                        <p class="text-gray-600 mb-3">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            <?= htmlspecialchars($reservation['location']) ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-gray-900">$<?= number_format(30000, 2) ?></p>
                                        <p class="text-gray-600 text-sm">Total</p>
                                    </div>
                                </div>

                                <!-- Dates and Info -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-blue-50 rounded-lg text-blue-600 mr-3">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Check-in</p>
                                            <p class="font-medium"><?= formatDate($reservation['start_date']) ?></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="p-2 bg-blue-50 rounded-lg text-blue-600 mr-3">
                                            <i class="fas fa-calendar-times"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Check-out</p>
                                            <p class="font-medium"><?= formatDate($reservation['end_date']) ?></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="p-2 bg-blue-50 rounded-lg text-blue-600 mr-3">
                                            <i class="fas fa-moon"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Duration</p>
                                            <p class="font-medium">4 nights</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Info -->
                                <div class="flex flex-wrap items-center gap-4 mt-4 pt-4 border-t border-gray-100">
                                    <span class="text-sm text-gray-600">
                                        <i class="fas fa-user-friends mr-1"></i> 5 guests
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        <i class="fas fa-calendar-day mr-1"></i> Booked on <?= formatDate($reservation['start_date']) ?>
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        <i class="fas fa-hashtag mr-1"></i> Booking #<?= str_pad($reservation['id'], 6, '0', STR_PAD_LEFT) ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="lg:w-1/4 lg:pl-6 mt-4 lg:mt-0 border-t lg:border-t-0 lg:border-l border-gray-100 pt-4 lg:pt-0">
                                <div class="space-y-3">
                                    <?php if ($reservation['reservation_status'] === 'Confirmed'): ?>
                                        <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                                            <i class="fas fa-file-invoice mr-2"></i> View Details
                                        </button>
                                        <button class="w-full px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition flex items-center justify-center">
                                            <i class="fas fa-times mr-2"></i> Cancel Booking
                                        </button>
                                        <button class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center justify-center">
                                            <i class="fas fa-download mr-2"></i> Download Invoice
                                        </button>
                                    <?php elseif ($reservation['reservation_status'] === 'Pending'): ?>
                                        <form action="payment.php"  method = "POST">
                                            <input type="hidden" name="reserve_id" value="<?php echo $reservation['rental_id'] ?>">
                                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center justify-center">
                                            <i class="fas fa-check mr-2"></i> Confirm Payment
                                        </button>
                                        </form>
                                        <button class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center justify-center">
                                            <i class="fas fa-question-circle mr-2"></i> Need Help?
                                        </button>
                                    <?php else: ?>
                                        <button class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center justify-center">
                                            <i class="fas fa-redo mr-2"></i> Rebook
                                        </button>
                                        <button class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center justify-center">
                                            <i class="fas fa-comment mr-2"></i> Leave Feedback
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if (!empty($filteredReservations)): ?>
        <div class="mt-8 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Showing <span class="font-medium">1</span> to <span class="font-medium"><?= count($filteredReservations) ?></span> of <span class="font-medium"><?= count($filteredReservations) ?></span> results
            </div>
            <div class="flex space-x-2">
                <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <i class="fas fa-chevron-left mr-2"></i> Previous
                </button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    1
                </button>
                <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    2
                </button>
                <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Next <i class="fas fa-chevron-right ml-2"></i>
                </button>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="mt-12 border-t border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p class="text-gray-600">© 2024 BookStay. All rights reserved.</p>
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-600 hover:text-blue-600 transition">Help Center</a>
                    <a href="#" class="text-gray-600 hover:text-blue-600 transition">Terms</a>
                    <a href="#" class="text-gray-600 hover:text-blue-600 transition">Privacy</a>
                    <a href="#" class="text-gray-600 hover:text-blue-600 transition">Contact</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Simple confirmation for cancellation
        document.addEventListener('click', function(e) {
            if (e.target.closest('button') && e.target.closest('button').textContent.includes('Cancel Booking')) {
                if (!confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
                    e.preventDefault();
                }
            }
        });
    </script>
</body>
</html>