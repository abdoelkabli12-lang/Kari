<?php
session_start();

require_once("Database.php");

$instance = Database::get_instance();
$db = $instance->connection;


if (isset($_POST['update_user_status'])){
    $userId = $_POST['user_id'];
    $status = $_POST['status'];

    $stmt = $db->prepare("UPDATE users SET status = :status WHERE id = :user_id");
    $stmt->execute([":status" => $status, ":user_id" => $userId]);
}

$stmt = $db->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Status Modal -->
<div id="statusModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-96 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Change User Status</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="mb-4">
            <p id="userName" class="font-medium"></p>
            <p id="userEmail" class="text-gray-600 text-sm"></p>
        </div>

        <div class="space-y-3 mb-6">
            <form id="statusForm" method="POST">
                <input type="hidden" name="user_id" id="modalUserId" value = "<?php echo $userId?>">
                <input type="hidden" name="update_user_status" value="update_user_status">

                <button type="submit" name="status" value="active"
                        class="w-full p-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                    <i class="fas fa-check-circle mr-2"></i> Activate Account
                </button>

                <button type="submit" name="status" value="inactive"
                        class="w-full p-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    <i class="fas fa-times-circle mr-2"></i> Deactivate Account
                </button>
            </form>
        </div>

        <button onclick="closeModal()" class="w-full p-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Cancel
        </button>
    </div>
</div>

<!-- Main Container -->
<div class="flex">

    <!-- Sidebar -->
    <div class="w-64 bg-gray-900 text-white min-h-screen fixed">
        <!-- Logo -->
        <div class="p-6 border-b border-gray-800">
            <h1 class="text-2xl font-bold">
                <i class="fas fa-crown mr-2 text-yellow-400"></i>
                Admin<span class="text-blue-400">Panel</span>
            </h1>
            <p class="text-gray-400 text-sm mt-1">Dashboard v2.1</p>
        </div>

        <!-- Navigation -->
        <nav class="p-4 space-y-2">
            <a href="#" class="flex items-center space-x-3 p-3 rounded-lg bg-blue-600 text-white">
                <i class="fas fa-users w-5"></i>
                <span class="font-medium">Users</span>
            </a>
            <a href="rentals_admin_dashboard.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 text-gray-300">
                <i class="fas fa-home w-5"></i>
                <span class="font-medium">Rentals</span>
            </a>
            <a href="#" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 text-gray-300">
                <i class="fas fa-chart-bar w-5"></i>
                <span class="font-medium">Analytics</span>
            </a>
            <a href="#" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 text-gray-300">
                <i class="fas fa-cog w-5"></i>
                <span class="font-medium">Settings</span>
            </a>
            <a href="#" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 text-gray-300">
                <i class="fas fa-shield-alt w-5"></i>
                <span class="font-medium">Security</span>
            </a>
        </nav>

        <!-- User Profile -->
        <div class="absolute bottom-0 w-full p-4 border-t border-gray-800">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center">
                    <span class="font-bold">AD</span>
                </div>
                <div>
                    <p class="font-medium">Admin User</p>
                    <p class="text-xs text-gray-400">Super Administrator</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="ml-64 flex-1 p-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">User Management</h1>
                <p class="text-gray-600">Manage all user accounts and permissions</p>
            </div>
            <div class="flex space-x-4">
                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center">
                    <i class="fas fa-download mr-2"></i>
                    Export
                </button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Add User
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-sm">Total Users</p>
                        <p class="text-3xl font-bold mt-2">1</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 text-sm text-green-600">
                    <i class="fas fa-arrow-up mr-1"></i> 0% from last month
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-sm">Active Users</p>
                        <p class="text-3xl font-bold mt-2">1</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
                        <i class="fas fa-user-check text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm font-medium text-green-600">100% active rate</span>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-sm">Hosts</p>
                        <p class="text-3xl font-bold mt-2">0</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center">
                        <i class="fas fa-home text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-600">
                    0% of total users
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-sm">Travelers</p>
                        <p class="text-3xl font-bold mt-2">5</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-orange-100 flex items-center justify-center">
                        <i class="fas fa-suitcase-rolling text-orange-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-600">
                    100% of total users
                </div>
            </div>
        </div>

        <!-- Control Bar -->
        <div class="bg-white rounded-xl shadow mb-6 p-4">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="flex space-x-4">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        <input type="text" placeholder="Search users..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-64">
                    </div>
                    <select class="border border-gray-300 rounded-lg px-4 py-2">
                        <option>All Roles</option>
                        <option>Host</option>
                        <option>Traveler</option>
                        <option>Visitor</option>
                    </select>
                </div>
                <div class="flex space-x-4">
                    <select class="border border-gray-300 rounded-lg px-4 py-2">
                        <option>All Status</option>
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                    <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-filter"></i>
                        Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Users Table -->

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="py-4 px-6 text-left">
                            <div class="flex items-center">
                                <input type="checkbox" class="mr-3 rounded">
                                <span class="font-semibold text-gray-700">User</span>
                            </div>
                        </th>
                        <th class="py-4 px-6 text-left font-semibold text-gray-700">Role</th>
                        <th class="py-4 px-6 text-left font-semibold text-gray-700">Status</th>
                        <th class="py-4 px-6 text-left font-semibold text-gray-700">Joined Date</th>
                        <th class="py-4 px-6 text-left font-semibold text-gray-700">Properties</th>
                        <th class="py-4 px-6 text-left font-semibold text-gray-700">Actions</th>
                    </tr>
                    </thead>
                    <!-- Single User -->
                    <tr class="hover:bg-gray-50">
                    <?php foreach ($users as $user) : ?>
                        <tbody class="divide-y divide-gray-200">
                        <td class="py-4 px-6">
                            <div class="flex items-center">
                                <input type="checkbox" class="mr-3 rounded">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-400 to-cyan-400 flex items-center justify-center text-white font-bold mr-3">
                                    MS
                                </div>
                                <div>
                                    <p class="font-medium"><?php echo $user['name'] ?></p>
                                    <p class="text-sm text-gray-500"><?php echo $user['Email'] ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-suitcase mr-1"></i> <?php echo $user['role']; ?>
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <span id="userStatus" class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 flex items-center w-fit">
                                <i class="fas fa-circle text-xs mr-1"></i> <?php echo $user['status'] ?>
                            </span>
                        </td>
                        <td class="py-4 px-6 text-gray-700">Jan 15, 2024</td>
                        <td class="py-4 px-6">
                            <span class="text-gray-500 text-sm">None</span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex space-x-2">
                                <button onclick="openModal(<?php $userId = $user['id'];
                                                            echo $userId?>, '<?php echo $user['name'] ?>', '<?php echo $user['Email'] ?>', '<?php echo $user['status'] ?>')"
                                        class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="w-8 h-8 rounded-lg bg-red-100 text-red-600 hover:bg-red-200">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </td>
    <?php endforeach; ?>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            <div class="flex flex-col md:flex-row justify-between items-center p-4 border-t border-gray-200">
                <div class="text-gray-600 text-sm mb-4 md:mb-0">
                    Showing <?php echo count($users)?> of 2 user
                </div>
                <div class="flex items-center space-x-2">
                    <button class="w-10 h-10 rounded-lg border border-gray-300 hover:bg-gray-50 flex items-center justify-center" disabled>
                        <i class="fas fa-chevron-left text-gray-400"></i>
                    </button>
                    <button class="w-10 h-10 rounded-lg bg-blue-600 text-white flex items-center justify-center">1</button>
                    <button class="w-10 h-10 rounded-lg border border-gray-300 hover:bg-gray-50 flex items-center justify-center" disabled>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </button>
                </div>
                <div class="flex items-center space-x-2 mt-4 md:mt-0">
                    <span class="text-sm text-gray-600">Rows per page:</span>
                    <select class="border border-gray-300 rounded-lg px-2 py-1 text-sm">
                        <option>10</option>
                        <option selected>25</option>
                        <option>50</option>
                        <option>100</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="mt-8 bg-white rounded-xl shadow p-6">
            <h3 class="font-bold text-gray-800 mb-4">Status Legend</h3>
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                    <span class="text-sm text-gray-700">Active - User can access all features</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full bg-red-500 mr-2"></div>
                    <span class="text-sm text-gray-700">Inactive - Account suspended or disabled</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Open modal function
    function openModal(userId, userName, userEmail, currentStatus) {
        document.getElementById('modalUserId').value = userId;
        document.getElementById('userName').textContent = userName;
        document.getElementById('userEmail').textContent = userEmail;
        document.getElementById('statusModal').classList.remove('hidden');
    }

    // Close modal
    function closeModal() {
        document.getElementById('statusModal').classList.add('hidden');
    }

    // Handle form submission
    document.getElementById('statusForm').addEventListener('submit', function(e) {
        const formData = new FormData(this);
        const userId = formData.get('user_id');
        const newStatus = formData.get('status');

        // Update the status badge in the table
        const statusBadge = document.getElementById('userStatus');
        if (newStatus === 'active') {
            statusBadge.className = 'px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 flex items-center w-fit';
            statusBadge.innerHTML = '<i class="fas fa-circle text-xs mr-1"></i> Active';

            // Update stats
            document.querySelectorAll('.text-3xl.font-bold')[1].textContent = '1'; // Active users
            document.querySelectorAll('.text-3xl.font-bold')[0].textContent = '1'; // Total users
        } else {
            statusBadge.className = 'px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 flex items-center w-fit';
            statusBadge.innerHTML = '<i class="fas fa-circle text-xs mr-1"></i> Inactive';

            // Update stats
            document.querySelectorAll('.text-3xl.font-bold')[1].textContent = '0'; // Active users
        }

        // Close modal
        closeModal();

        // Show success message (optional)
        alert('User status updated successfully!');
    });

    // Close modal when clicking outside
    document.getElementById('statusModal').addEventListener('click', function(e) {
        if (e.target.id === 'statusModal') {
            closeModal();
        }
    });

    // Simple interactivity for checkboxes
    document.addEventListener('DOMContentLoaded', function() {
        const headerCheckbox = document.querySelector('thead input[type="checkbox"]');
        const rowCheckbox = document.querySelector('tbody input[type="checkbox"]');

        headerCheckbox.addEventListener('change', function() {
            if (rowCheckbox) {
                rowCheckbox.checked = this.checked;
            }
        });

        const row = document.querySelector('tbody tr');
        if (row) {
            row.addEventListener('click', function(e) {
                if (e.target.type !== 'checkbox' && !e.target.closest('button')) {
                    const checkbox = this.querySelector('input[type="checkbox"]');
                    if (checkbox) {
                        checkbox.checked = !checkbox.checked;
                    }
                }
            });
        }
    });
</script>
</body>
</html>