<?php
session_start();

$_SESSION['rental_id'] = $_POST['rental_id'];
$rental_id = $_SESSION['rental_id'];

//$status = $_SESSION['status'];

require_once("Database.php");

$instance = Database::get_instance();
$db = $instance->connection;



$stmt = $db->prepare("SELECT * FROM accommodation WHERE id = ?");
$stmt->execute([$rental_id]);
$book = $stmt->fetch();

?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Your Stay - Vacation Rental</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        .datepicker-error {
            border-color: #ef4444 !important;
        }
        .success-message {
            animation: slideDown 0.5s ease-out;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Header -->
<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center">
                <i class="fas fa-home text-indigo-600 text-2xl mr-3"></i>
                <h1 class="text-2xl font-bold text-gray-800">Vacation<span class="text-indigo-600">Rentals</span></h1>
            </div>
            <a href="index.php" class="text-gray-600 hover:text-indigo-600 transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to Rentals
            </a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Title -->
    <div class="mb-10">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Book Your Stay</h2>
        <p class="text-gray-600">Complete the form below to reserve your dream vacation rental</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Rental Details -->
        <div class="lg:col-span-2">
            <!-- Booking Form Card -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                <h3 class="text-xl font-bold text-gray-800 mb-6 border-b pb-3">
                    <i class="fas fa-user-check text-indigo-600 mr-2"></i>Traveler Information
                </h3>

                <form action="reserve_rental.php" method = "POST" id="bookingForm" class="space-y-6">

                    <input  type="hidden" name="rental_id" value="<?php echo $rental_id?>">
<!--                    <input  type="hidden" name="status" value="--><?php //echo $status?><!--">-->
                    <!-- Full Name -->
                    <div>
                        <label for="fullName" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user text-indigo-500 mr-2"></i>Full Name *
                        </label>
                        <div class="relative">
                            <input type="text" id="fullName" name="fullName"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 input-focus focus:border-indigo-500 transition duration-200"
                                   placeholder="John Doe"
                                   required>
                            <div class="absolute right-3 top-3 text-green-500 hidden" id="nameValid">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="text-xs text-red-500 mt-1 hidden" id="nameError">
                                Please enter a valid name (minimum 2 characters)
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope text-indigo-500 mr-2"></i>Email Address *
                        </label>
                        <div class="relative">
                            <input type="email" id="email" name="email"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 input-focus focus:border-indigo-500 transition duration-200"
                                   placeholder="john@example.com"
                                   required>
                            <div class="absolute right-3 top-3 text-green-500 hidden" id="emailValid">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="text-xs text-red-500 mt-1 hidden" id="emailError">
                                Please enter a valid email address
                            </div>
                        </div>
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone text-indigo-500 mr-2"></i>Phone Number *
                        </label>
                        <div class="relative">
                            <input type="tel" id="phone" name="phone"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 input-focus focus:border-indigo-500 transition duration-200"
                                   placeholder="+1 (555) 123-4567"
                                   required>
                            <div class="absolute right-3 top-3 text-green-500 hidden" id="phoneValid">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="text-xs text-red-500 mt-1 hidden" id="phoneError">
                                Please enter a valid phone number
                            </div>
                        </div>
                    </div>

                    <!-- Traveler Location -->
                    <div>
                        <label for="travelerLocation" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt text-indigo-500 mr-2"></i>Your Location *
                        </label>
                        <div class="relative">
                            <input type="text" id="travelerLocation" name="travelerLocation"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 input-focus focus:border-indigo-500 transition duration-200"
                                   placeholder="City, Country"
                                   required>
                            <div class="absolute right-3 top-3 text-green-500 hidden" id="locationValid">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="text-xs text-red-500 mt-1 hidden" id="locationError">
                                Please enter your location
                            </div>
                        </div>
                    </div>

                    <!-- Dates Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Check-in Date -->
                        <div>
                            <label for="checkIn" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt text-indigo-500 mr-2"></i>Check-in Date *
                            </label>
                            <div class="relative">
                                <input type="text" id="checkIn" name="checkIn"
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 input-focus focus:border-indigo-500 transition duration-200"
                                       placeholder="Select start date"
                                       readonly
                                       required>
                                <div class="absolute right-3 top-3 text-green-500 hidden" id="checkInValid">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Check-out Date -->
                        <div>
                            <label for="checkOut" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-check text-indigo-500 mr-2"></i>Check-out Date *
                            </label>
                            <div class="relative">
                                <input type="text" id="checkOut" name="checkOut"
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 input-focus focus:border-indigo-500 transition duration-200"
                                       placeholder="Select end date"
                                       readonly
                                       required>
                                <div class="absolute right-3 top-3 text-green-500 hidden" id="checkOutValid">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-xs text-red-500 mt-1 hidden" id="dateError">
                        Check-out date must be after check-in date
                    </div>

                    <!-- Special Requests -->
                    <div>
                        <label for="specialRequests" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-comment-alt text-indigo-500 mr-2"></i>Special Requests (Optional)
                        </label>
                        <textarea id="specialRequests" name="specialRequests" rows="4"
                                  class="w-full px-4 py-3 rounded-lg border border-gray-300 input-focus focus:border-indigo-500 transition duration-200"
                                  placeholder="Any special requirements or requests..."></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">

                        <button name = "BTN" type="submit" id="submitBtn"
                                class="w-full bg-indigo-600 text-white font-semibold py-4 px-6 rounded-lg hover:bg-indigo-700 transition duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
                            <i class="fas fa-lock mr-2"></i>Confirm Booking
                        </button>
                    </div>
                </form>
            </div>

            <!-- Success Message (Initially Hidden) -->
            <div id="successMessage" class="hidden bg-green-50 border border-green-200 rounded-xl p-6 success-message">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full mr-4">
                        <i class="fas fa-check text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-green-800">Booking Confirmed!</h4>
                        <p class="text-green-600">Your reservation has been successfully submitted. A confirmation email has been sent to your inbox.</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-green-200">
                    <button id="newBookingBtn" class="text-indigo-600 hover:text-indigo-800 font-medium">
                        <i class="fas fa-plus mr-2"></i>Make Another Booking
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column: Booking Summary -->
        <div class="lg:col-span-1">
            <!-- Rental Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 sticky top-8">
                <div class="mb-6">
                    <div class="relative h-48 rounded-lg overflow-hidden mb-4">
                        <img src="https://source.unsplash.com/400x300/?villa,beach"
                             alt="Luxury Villa"
                             class="w-full h-full object-cover">
                        <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full">
                            <span class="text-sm font-bold text-indigo-700">$ <?php echo $book['price'] ?></span>
                            <span class="text-xs text-gray-600">/night</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo $book['rental_type'] ?></h3>
                    <div class="flex items-center text-gray-600 mb-3">
                        <i class="fas fa-map-marker-alt text-red-400 mr-2"></i>
                        <span class="text-sm"><?php echo $book['location'] ?></span>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-star text-yellow-400 mr-1"></i>
                        <span class="font-medium">4.9</span>
                        <span class="text-gray-500 text-sm ml-2">(128 reviews)</span>
                    </div>
                </div>

                <!-- Booking Summary -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4">Booking Summary</h4>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">$ <?php echo $book['price']?> × <span id="nightsCount">0</span> nights</span>
                            <span class="font-medium" id="subtotal">$0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Cleaning fee</span>
                            <span class="font-medium">$50</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Service fee</span>
                            <span class="font-medium">$30</span>
                        </div>
                        <div class="flex justify-between border-t border-gray-200 pt-3">
                            <span class="font-bold text-gray-800">Total</span>
                            <span class="text-xl font-bold text-indigo-600" id="totalAmount">$0</span>
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-medium text-gray-700 mb-2">Your Stay</h5>
                        <div class="text-sm text-gray-600">
                            <div class="flex justify-between mb-1">
                                <span>Check-in:</span>
                                <span id="summaryCheckIn" class="font-medium">--</span>
                            </div>
                            <div class="flex justify-between mb-1">
                                <span>Check-out:</span>
                                <span id="summaryCheckOut" class="font-medium">--</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Guests:</span>
                                <span class="font-medium">2 adults</span>
                            </div>
                        </div>
                    </div>

                    <!-- Cancellation Policy -->
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-2"></i>
                            <div>
                                <h6 class="font-medium text-blue-800 mb-1">Flexible Cancellation</h6>
                                <p class="text-xs text-blue-600">Free cancellation up to 24 hours before check-in</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-gray-800 text-white py-8 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <p class="text-gray-400">© 2024 VacationRentals. All rights reserved.</p>
            <p class="text-gray-500 text-sm mt-2">This is a frontend demonstration. No actual bookings are processed.</p>
        </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Price per night
        const PRICE_PER_NIGHT = <?php echo $book['price']?>;
        const CLEANING_FEE = 50;
        const SERVICE_FEE = 30;

        // Initialize date pickers
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);

        const checkInPicker = flatpickr("#checkIn", {
            minDate: "today",
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr) {
                validateDates();
                updateBookingSummary();
                showValidationIcon('checkIn');
            }
        });

        const checkOutPicker = flatpickr("#checkOut", {
            minDate: "today",
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr) {
                validateDates();
                updateBookingSummary();
                showValidationIcon('checkOut');
            }
        });

        // Real-time validation functions
        function validateName() {
            const nameInput = document.getElementById('fullName');
            const nameError = document.getElementById('nameError');
            const isValid = nameInput.value.trim().length >= 2;

            toggleValidation('name', isValid);
            nameError.classList.toggle('hidden', isValid);
            return isValid;
        }

        function validateEmail() {
            const emailInput = document.getElementById('email');
            const emailError = document.getElementById('emailError');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const isValid = emailRegex.test(emailInput.value);

            toggleValidation('email', isValid);
            emailError.classList.toggle('hidden', isValid);
            return isValid;
        }

        function validatePhone() {
            const phoneInput = document.getElementById('phone');
            const phoneError = document.getElementById('phoneError');
            const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
            const phoneValue = phoneInput.value.replace(/[^\d+]/g, '');
            const isValid = phoneRegex.test(phoneValue) && phoneValue.length >= 10;

            toggleValidation('phone', isValid);
            phoneError.classList.toggle('hidden', isValid);
            return isValid;
        }

        function validateLocation() {
            const locationInput = document.getElementById('travelerLocation');
            const locationError = document.getElementById('locationError');
            const isValid = locationInput.value.trim().length >= 2;

            toggleValidation('location', isValid);
            locationError.classList.toggle('hidden', isValid);
            return isValid;
        }

        function validateDates() {
            const checkIn = document.getElementById('checkIn').value;
            const checkOut = document.getElementById('checkOut').value;
            const dateError = document.getElementById('dateError');

            if (!checkIn || !checkOut) {
                dateError.classList.add('hidden');
                return false;
            }

            const checkInDate = new Date(checkIn);
            const checkOutDate = new Date(checkOut);
            const isValid = checkOutDate > checkInDate;

            dateError.classList.toggle('hidden', isValid);

            // Add error styling to datepickers
            document.getElementById('checkIn').classList.toggle('datepicker-error', !isValid);
            document.getElementById('checkOut').classList.toggle('datepicker-error', !isValid);

            return isValid;
        }

        function toggleValidation(field, isValid) {
            const validIcon = document.getElementById(field + 'Valid');
            const input = document.getElementById(field === 'name' ? 'fullName' :
                field === 'location' ? 'travelerLocation' : field);

            if (isValid) {
                validIcon.classList.remove('hidden');
                input.classList.remove('border-red-500');
                input.classList.add('border-green-500');
            } else {
                validIcon.classList.add('hidden');
                input.classList.remove('border-green-500');
                input.classList.add('border-red-500');
            }
        }

        function showValidationIcon(field) {
            const validIcon = document.getElementById(field + 'Valid');
            if (validIcon) {
                validIcon.classList.remove('hidden');
                setTimeout(() => {
                    if (document.getElementById(field === 'checkIn' ? 'checkIn' : 'checkOut').value) {
                        validIcon.classList.remove('hidden');
                    }
                }, 300);
            }
        }

        // Update booking summary
        function updateBookingSummary() {
            const checkIn = document.getElementById('checkIn').value;
            const checkOut = document.getElementById('checkOut').value;

            if (checkIn && checkOut) {
                const checkInDate = new Date(checkIn);
                const checkOutDate = new Date(checkOut);
                const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));

                if (nights > 0) {
                    document.getElementById('summaryCheckIn').textContent = formatDate(checkIn);
                    document.getElementById('summaryCheckOut').textContent = formatDate(checkOut);
                    document.getElementById('nightsCount').textContent = nights;

                    const subtotal = PRICE_PER_NIGHT * nights;
                    const total = subtotal + CLEANING_FEE + SERVICE_FEE;

                    document.getElementById('subtotal').textContent = `$${subtotal}`;
                    document.getElementById('totalAmount').textContent = `$${total}`;
                }
            }
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });
        }

        // Event listeners for real-time validation
        document.getElementById('fullName').addEventListener('input', validateName);
        document.getElementById('email').addEventListener('input', validateEmail);
        document.getElementById('phone').addEventListener('input', validatePhone);
        document.getElementById('travelerLocation').addEventListener('input', validateLocation);

        // Form submission
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            // e.preventDefault();

            // Validate all fields
            const isNameValid = validateName();
            const isEmailValid = validateEmail();
            const isPhoneValid = validatePhone();
            const isLocationValid = validateLocation();
            const areDatesValid = validateDates();

            if (isNameValid && isEmailValid && isPhoneValid && isLocationValid && areDatesValid) {
                // Show loading state
                const submitBtn = document.getElementById('submitBtn');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                submitBtn.disabled = true;

                // Simulate API call
                setTimeout(() => {
                    // Show success message
                    document.getElementById('bookingForm').classList.add('hidden');
                    document.getElementById('successMessage').classList.remove('hidden');

                    // Reset button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;

                    // Log booking data (in real app, this would go to backend)
                    const bookingData = {
                        name: document.getElementById('fullName').value,
                        email: document.getElementById('email').value,
                        phone: document.getElementById('phone').value,
                        location: document.getElementById('travelerLocation').value,
                        checkIn: document.getElementById('checkIn').value,
                        checkOut: document.getElementById('checkOut').value,
                        specialRequests: document.getElementById('specialRequests').value,
                        total: document.getElementById('totalAmount').textContent
                    };
                    console.log('Booking submitted:', bookingData);
                }, 1500);
            } else {
                // Show error message
                alert('Please fill in all required fields correctly.');
            }
        });

        // Reset form for new booking
        document.getElementById('newBookingBtn').addEventListener('click', function() {
            document.getElementById('bookingForm').reset();
            document.getElementById('bookingForm').classList.remove('hidden');
            document.getElementById('successMessage').classList.add('hidden');

            // Reset validation icons
            document.querySelectorAll('[id$="Valid"]').forEach(icon => {
                icon.classList.add('hidden');
            });

            // Reset input borders
            document.querySelectorAll('input, textarea').forEach(input => {
                input.classList.remove('border-green-500', 'border-red-500', 'datepicker-error');
            });

            // Reset summary
            document.getElementById('summaryCheckIn').textContent = '--';
            document.getElementById('summaryCheckOut').textContent = '--';
            document.getElementById('nightsCount').textContent = '0';
            document.getElementById('subtotal').textContent = '$0';
            document.getElementById('totalAmount').textContent = '$0';
        });

        // Initialize summary
        updateBookingSummary();
    });
</script>
</body>
</html>