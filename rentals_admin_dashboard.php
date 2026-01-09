<?php
session_start();

require_once ("Database.php");
require ("show_rentals_admin.php");

$instance = Database::get_instance();
$db = $instance->connection;

$showRentals = new showRentalsAdmin($db);
$rentals = $showRentals->getRentals();

// Calculate stats from PHP data
$totalRentals = count($rentals);
$confirmedCount = 0;
$pendingCount = 0;
$cancelledCount = 0;

foreach ($rentals as $rental) {
    switch ($rental['status']) {
        case 'confirmed':
            $confirmedCount++;
            break;
        case 'pending':
            $pendingCount++;
            break;
        case 'cancelled':
            $cancelledCount++;
            break;
    }
}

$status = $_POST['status'] ?? null;
$rental_id = $_POST['rental_id'] ?? null;

if ($status && $rental_id) {
    $showRentals->updateRental($rental_id, $status);
    // Refresh the page to show updated data
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Rental Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
<!-- Admin Header -->
<header class="bg-white shadow-sm border-b">
    <div class="container mx-auto px-4 py-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <div class="bg-primary-600 text-white p-2 rounded-lg">
                    <i class="fas fa-building text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Rental Management</h1>
                    <p class="text-gray-600 text-sm">Admin Dashboard</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                            <i class="fas fa-search text-gray-400"></i>
                        </span>
                    <input type="text" placeholder="Search rentals..."
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <p class="font-medium text-gray-800">Admin User</p>
                        <p class="text-sm text-gray-600">Super Admin</p>
                    </div>
                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-shield text-primary-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<form class="container mx-auto px-4 py-8">
    <!-- Dashboard Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Rentals</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $totalRentals; ?></p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-home text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <span class="text-sm text-green-600">
                    <i class="fas fa-arrow-up mr-1"></i> All active rentals
                </span>
            </div>
        </div>

        <form action="" method = "post">
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <input type="hidden" name="status" value="confirmed">
                    <p class="text-gray-600 text-sm">Confirmed</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $confirmedCount; ?></p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <span class="text-sm text-green-600">
                    <?php echo $totalRentals > 0 ? round(($confirmedCount / $totalRentals) * 100, 0) : 0; ?>% of total rentals
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <input type="hidden" name="status" value="pending">
                    <p class="text-gray-600 text-sm">Pending</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $pendingCount; ?></p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <span class="text-sm text-yellow-600">
                    <?php echo $totalRentals > 0 ? round(($pendingCount / $totalRentals) * 100, 0) : 0; ?>% of total rentals
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <input type="hidden" name="status" value="cancelled">
                    <p class="text-gray-600 text-sm">Cancelled</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $cancelledCount; ?></p>
                </div>
                <div class="bg-red-100 p-3 rounded-lg">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <span class="text-sm text-gray-600">
                    <?php echo $totalRentals > 0 ? round(($cancelledCount / $totalRentals) * 100, 0) : 0; ?>% of total rentals
                </span>
            </div>
        </div>
    </div>
</form>

    <!-- Action Bar -->
    <div class="bg-white rounded-xl shadow p-6 mb-8">
        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-xl font-bold text-gray-800">All Rentals</h2>
                <p class="text-gray-600">Manage and monitor all rental properties</p>
            </div>
            <div class="flex flex-wrap gap-3 action-buttons">
                <button onclick="showAll()" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-list mr-2"></i> All Rentals
                </button>
                <button onclick="showConfirmed()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-check-circle mr-2"></i> Confirmed Only
                </button>
                <button onclick="showPending()" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-clock mr-2"></i> Pending Only
                </button>
                <button onclick="showCancelled()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-times-circle mr-2"></i> Cancelled Only
                </button>
                <button onclick="addNewRental()" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors">
                    <i class="fas fa-plus mr-2"></i> Add New
                </button>
            </div>
        </div>

        <!-- Filter Options -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <select class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option>All Property Types</option>
                <option>Apartment</option>
                <option>House</option>
                <option>Villa</option>
                <option>Cabin</option>
            </select>
            <select class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option>All Locations</option>
                <option>New York</option>
                <option>Los Angeles</option>
                <option>Miami</option>
                <option>Chicago</option>
            </select>
            <select class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option>Sort By: Newest First</option>
                <option>Sort By: Price: Low to High</option>
                <option>Sort By: Price: High to Low</option>
                <option>Sort By: Name A-Z</option>
            </select>
            <button class="bg-gray-100 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fas fa-filter mr-2"></i> Apply Filters
            </button>
        </div>
    </div>

    <!-- Rentals Table -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Rental Property
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type & Location
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Price/Night
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Last Updated
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="rentalsTable">
                <?php
                // Status badge configuration
                $statusConfig = [
                        'Confirmed' => [
                                'text' => 'Confirmed',
                                'bg' => 'bg-green-100',
                                'textColor' => 'text-green-800',
                                'icon' => 'fa-check-circle'
                        ],
                        'Pending' => [
                                'text' => 'Pending',
                                'bg' => 'bg-yellow-100',
                                'textColor' => 'text-yellow-800',
                                'icon' => 'fa-clock'
                        ],
                        'Cancelled' => [
                                'text' => 'Cancelled',
                                'bg' => 'bg-red-100',
                                'textColor' => 'text-red-800',
                                'icon' => 'fa-times-circle'
                        ]
                ];

                if (!empty($rentals)):
                    foreach ($rentals as $rental):
                        $status = $rental['status'] ?? 'Confirmed';
                        $config = $statusConfig[$status] ?? $statusConfig['pending'];

                        // Prepare data for JavaScript
                        $jsRentalData = [
                                'id' => $rental['id'] ?? 0,
                                'name' => $rental['rental_type'] ?? 'Unknown Property',
                                'image' => $rental['image'] ?? 'https://images.unsplash.com/photo-1613977257363-707ba9348227?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80',
                                'type' => $rental['rental_type'] ?? 'Unknown Type',
                                'location' => $rental['location'] ?? 'Unknown Location',
                                'price' => $rental['price'] ?? 0,
                                'status' => $rental['status'] ?? 'Unknown Status',
                                'maxGuests' => $rental['max_guests'] ?? $rental['maxGuests'] ?? 0,
                                'bookings' => $rental['bookings'] ?? 0,
                                'lastUpdated' => isset($rental['last_updated']) ? $rental['last_updated'] : (isset($rental['lastUpdated']) ? $rental['lastUpdated'] : date('Y-m-d'))
                        ];
                        ?>
                        <tr class="rental-row hover:bg-gray-50" data-status="<?php echo $status; ?>">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-16 w-16">
                                        <img class="h-16 w-16 rounded-lg object-cover"
                                             src="<?php echo htmlspecialchars($jsRentalData['image']); ?>"
                                             alt="<?php echo htmlspecialchars($jsRentalData['name']); ?>">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($jsRentalData['name']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            ID: #<?php echo str_pad($jsRentalData['id'], 3, '0', STR_PAD_LEFT); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?php echo htmlspecialchars($jsRentalData['type']); ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>
                                    <?php echo htmlspecialchars($jsRentalData['location']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    $<?php echo number_format($jsRentalData['price'], 0); ?>/night
                                </div>
                                <div class="text-sm text-gray-500">
                                    Max <?php echo htmlspecialchars($jsRentalData['maxGuests']); ?> guests
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $config['bg']; ?> <?php echo $config['textColor']; ?>">
                                <i class="fas <?php echo $config['icon']; ?> mr-1"></i>
                                <?php echo $config['text']; ?>
                            </span>
                                <div class="text-xs text-gray-500 mt-1">
                                    <?php echo htmlspecialchars($jsRentalData['bookings']); ?> bookings
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo htmlspecialchars($jsRentalData['lastUpdated']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="editRental(<?php echo $jsRentalData['id']; ?>)"
                                            class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="toggleStatus(<?php echo $jsRentalData['id']; ?>, '<?php echo htmlspecialchars(addslashes($jsRentalData['name'])); ?>')"
                                            class="text-gray-600 hover:text-gray-900" title="Change Status">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                    <button onclick="viewDetails(<?php echo $jsRentalData['id']; ?>)"
                                            class="text-gray-600 hover:text-gray-900" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="deleteRental(<?php echo $jsRentalData['id']; ?>)"
                                            class="text-gray-400 hover:text-red-600" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-home text-4xl text-gray-300 mb-2"></i>
                            <p class="text-lg">No rentals found</p>
                            <p class="text-sm">Add your first rental to get started</p>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-8 flex justify-between items-center">
        <div class="text-gray-600">
            Showing <span id="showingFrom">1</span> to <span id="showingTo"><?php echo min(count($rentals), 10); ?></span> of <span id="totalRentals"><?php echo count($rentals); ?></span> rentals
        </div>
        <div class="flex space-x-2">
            <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="px-4 py-2 bg-primary-600 text-white rounded-lg">1</button>
            <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">2</button>
            <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">3</button>
            <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<!-- Rental Status Modal -->
<div id="statusModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                <i class="fas fa-exchange-alt text-blue-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-800 text-center mb-2">Update Rental Status</h3>
            <form method="post" id="statusForm">
                <input type="hidden" name="rental_id" id="modalRentalId">
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-600 text-center mb-4" id="modalRentalName"></p>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 border rounded-lg cursor-pointer bg-green-50 border-green-200" onclick="selectStatus('Confirmed')">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-green-500 mr-3"></div>
                                <span class="font-medium text-green-800">Confirmed</span>
                            </div>
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                        <div class="flex items-center justify-between p-3 border rounded-lg cursor-pointer bg-yellow-50 border-yellow-200" onclick="selectStatus('Pending')">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-yellow-500 mr-3"></div>
                                <span class="font-medium text-yellow-800">Pending</span>
                            </div>
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                        <div class="flex items-center justify-between p-3 border rounded-lg cursor-pointer bg-red-50 border-red-200" onclick="selectStatus('Cancelled')">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-red-500 mr-3"></div>
                                <span class="font-medium text-red-800">Cancelled</span>
                            </div>
                            <i class="fas fa-times text-red-600"></i>
                        </div>
                    </div>
                    <input type="hidden" name="status" id="selectedStatus">
                </div>
                <div class="items-center px-4 py-3 flex space-x-2">
                    <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Convert PHP rentals to JavaScript array
    const rentals = <?php echo json_encode($rentals ?? []); ?>;
    const allRentalRows = document.querySelectorAll('.rental-row');

    let currentFilter = 'all';
    let currentRentalId = null;

    // Status badge configuration
    const statusConfig = {
        confirmed: {
            text: 'Confirmed',
            color: 'green',
            icon: 'fa-check-circle',
            bg: 'bg-green-100',
            textColor: 'text-green-800'
        },
        pending: {
            text: 'Pending',
            color: 'yellow',
            icon: 'fa-clock',
            bg: 'bg-yellow-100',
            textColor: 'text-yellow-800'
        },
        cancelled: {
            text: 'Cancelled',
            color: 'red',
            icon: 'fa-times-circle',
            bg: 'bg-red-100',
            textColor: 'text-red-800'
        }
    };

    // Filter functions
    function showAll() {
        currentFilter = 'all';
        allRentalRows.forEach(row => {
            row.style.display = '';
        });
        updateCounters();
        updateActiveButton('all');
    }

    function showConfirmed() {
        currentFilter = 'Confirmed';
        allRentalRows.forEach(row => {
            if (row.getAttribute('data-status') === 'confirmed') {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        updateCounters();
        updateActiveButton('Confirmed');
    }

    function showPending() {
        currentFilter = 'Pending';
        allRentalRows.forEach(row => {
            if (row.getAttribute('data-status') === 'pending') {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        updateCounters();
        updateActiveButton('Pending');
    }

    function showCancelled() {
        currentFilter = 'Cancelled';
        allRentalRows.forEach(row => {
            if (row.getAttribute('data-status') === 'cancelled') {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        updateCounters();
        updateActiveButton('Cancelled');
    }

    function updateCounters() {
        let visibleCount = 0;
        allRentalRows.forEach(row => {
            if (row.style.display !== 'none') {
                visibleCount++;
            }
        });

        document.getElementById('showingFrom').textContent = visibleCount > 0 ? 1 : 0;
        document.getElementById('showingTo').textContent = visibleCount;
        document.getElementById('totalRentals').textContent = allRentalRows.length;
    }

    function updateActiveButton(filter) {
        // Remove all active states
        document.querySelectorAll('.action-buttons button').forEach(btn => {
            btn.classList.remove('ring-2', 'ring-offset-2');
        });

        // Add active state to current filter button
        const buttons = document.querySelectorAll('.action-buttons button');
        const buttonIndex = ['all', 'confirmed', 'pending', 'cancelled'].indexOf(filter);

        if (buttonIndex >= 0 && buttons[buttonIndex]) {
            const button = buttons[buttonIndex];
            const colorClass = filter === 'all' ? 'ring-primary-500' :
                filter === 'confirmed' ? 'ring-green-500' :
                    filter === 'pending' ? 'ring-yellow-500' : 'ring-red-500';
            button.classList.add('ring-2', colorClass, 'ring-offset-2');
        }
    }

    // Action functions
    function toggleStatus(id, name) {
        currentRentalId = id;
        document.getElementById('modalRentalId').value = id;
        document.getElementById('modalRentalName').textContent = name;
        document.getElementById('statusModal').classList.remove('hidden');
    }

    function selectStatus(status) {
        document.getElementById('selectedStatus').value = status;

        // Visual feedback
        document.querySelectorAll('#statusForm div[onclick^="selectStatus"]').forEach(div => {
            div.classList.remove('ring-2', 'ring-offset-2');
        });

        const selectedDiv = document.querySelector(`[onclick="selectStatus('${status}')"]`);
        if (selectedDiv) {
            const colorClass = status === 'confirmed' ? 'ring-green-500' :
                status === 'pending' ? 'ring-yellow-500' : 'ring-red-500';
            selectedDiv.classList.add('ring-2', colorClass, 'ring-offset-2');
        }
    }

    function closeModal() {
        document.getElementById('statusModal').classList.add('hidden');
        document.getElementById('selectedStatus').value = '';
        // Reset visual feedback
        document.querySelectorAll('#statusForm div[onclick^="selectStatus"]').forEach(div => {
            div.classList.remove('ring-2', 'ring-offset-2');
        });
    }

    function editRental(id) {
        showNotification(`Editing rental #${id}`, 'info');
    }

    function viewDetails(id) {
        showNotification(`Viewing details for rental #${id}`, 'info');
    }

    function deleteRental(id) {
        if (confirm('Are you sure you want to delete this rental? This action cannot be undone.')) {
            showNotification(`Rental #${id} deleted successfully`, 'error');
        }
    }

    function addNewRental() {
        showNotification('Opening new rental form...', 'info');
    }

    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        const bgColor = type === 'info' ? 'bg-blue-600' : type === 'error' ? 'bg-red-600' : 'bg-green-600';
        const icon = type === 'info' ? 'fa-info-circle' : type === 'error' ? 'fa-times-circle' : 'fa-check-circle';

        notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
        notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${icon} mr-3"></i>
                    <span>${message}</span>
                </div>
            `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
            notification.classList.add('translate-x-0');
        }, 10);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.remove('translate-x-0');
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', () => {
        updateActiveButton('all');
        updateCounters();
    });

    // Close modal on outside click
    document.getElementById('statusModal').addEventListener('click', (e) => {
        if (e.target.id === 'statusModal') {
            closeModal();
        }
    });
</script>
</body>
</html>