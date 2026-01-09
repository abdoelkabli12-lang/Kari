<?php

session_start();


$traveller  = $_POST["role"] ?? 'guest';
echo $traveller;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Your Role</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-600 to-purple-700">

<div class="bg-white w-full max-w-5xl rounded-2xl shadow-2xl p-10">
    <h1 class="text-3xl font-bold text-center text-gray-800">Select Your Role</h1>
    <p class="text-center text-gray-500 mt-2 mb-10">
        Choose how you want to use the platform
    </p>

    <form action="Role.php" method="POST">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Traveler -->
            <label class="cursor-pointer group">
                <input type="radio" name="role" value="traveler" class="hidden peer" required>
                <div class="h-full rounded-xl border-2 border-gray-200 p-6 text-center
                            transition-all duration-300
                            peer-checked:border-indigo-600 peer-checked:bg-indigo-50
                            group-hover:shadow-xl group-hover:-translate-y-1">
                    <div class="text-5xl mb-4">🌍</div>
                    <h3 class="text-xl font-semibold text-gray-800">Traveler</h3>
                    <p class="text-sm text-gray-500 mt-2">
                        Explore destinations and book experiences
                    </p>
                </div>
            </label>

            <!-- Host -->
            <label class="cursor-pointer group">
                <input type="radio" name="role" value="host" class="hidden peer">
                <div class="h-full rounded-xl border-2 border-gray-200 p-6 text-center
                            transition-all duration-300
                            peer-checked:border-indigo-600 peer-checked:bg-indigo-50
                            group-hover:shadow-xl group-hover:-translate-y-1">
                    <div class="text-5xl mb-4">🏠</div>
                    <h3 class="text-xl font-semibold text-gray-800">Host</h3>
                    <p class="text-sm text-gray-500 mt-2">
                        List properties and manage reservations
                    </p>
                </div>
            </label>

            <!-- Admin -->
            <label class="cursor-pointer group">
                <input type="radio" name="role" value="admin" class="hidden peer">
                <div class="h-full rounded-xl border-2 border-gray-200 p-6 text-center
                            transition-all duration-300
                            peer-checked:border-red-600 peer-checked:bg-red-50
                            group-hover:shadow-xl group-hover:-translate-y-1">
                    <div class="text-5xl mb-4">🛡️</div>
                    <h3 class="text-xl font-semibold text-gray-800">Admin</h3>
                    <p class="text-sm text-gray-500 mt-2">
                        Manage users and platform settings
                    </p>
                </div>
            </label>

        </div>

        <button type="submit"
                class="mt-10 w-full py-4 rounded-xl bg-indigo-600 text-white text-lg font-semibold
                   hover:bg-indigo-700 transition duration-300">
            Continue
        </button>
    </form>
</div>

</body>
</html>
