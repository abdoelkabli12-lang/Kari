<?php



// payment.php
session_start();

// In a real app, you'd get the reservation details from the database
// For demo, we'll use session or GET parameters
require_once ("Database.php");
//require ("reserve_email_send.php");

$instance = Database::get_instance();
$db = $instance->connection;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reserve_id'])){
        $reserve_id = $_POST['reserve_id'];
        $_SESSION['reserve_id'] = $reserve_id;
    }
//    echo $_POST['reserve_id'];

}


//    if ( isset($_POST['sent'])){
//        header("Location: rserve_email_send.php");
//        exit;
//    }

$stmt = $db->prepare("SELECT r.*, a.price FROM reservations r JOIN accommodation a ON r.rental_id = a.id WHERE r.rental_id = ?");
$stmt->execute([$_POST['reserve_id']]);
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

function formatDate($date) {
    return date('M j, Y', strtotime($date));
}

function formatPrice($price) {
    return number_format($price, 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Payment - Kari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }

        /* Card number formatting */
        .card-number::placeholder { letter-spacing: normal; }
        .card-number:focus::placeholder { letter-spacing: 0.2em; }

        /* Smooth transitions */
        .transition-all { transition: all 0.3s ease; }

        /* Custom checkbox */
        .custom-checkbox:checked { background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e"); }

        /* Payment method tabs */
        .payment-tab.active {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold text-blue-600">BookStay</a>
                    <div class="ml-6 flex items-center space-x-1 text-sm text-gray-600">
                        <span>1. Reservation</span>
                        <i class="fas fa-chevron-right text-xs"></i>
                        <span>2. Guest Info</span>
                        <i class="fas fa-chevron-right text-xs"></i>
                        <span class="font-semibold text-blue-600">3. Payment</span>
                    </div>
                </div>
                <div class="text-sm text-gray-600">
                    <i class="fas fa-lock mr-1"></i> Secure Payment
                </div>
            </div>
        </div>
    </header>

    <!-- Progress Bar -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full" style="width: 100%"></div>
        </div>
        <div class="flex justify-between mt-2 text-sm text-gray-600">
            <span>Reservation Details</span>
            <span>Guest Information</span>
            <span class="font-semibold text-blue-600">Payment</span>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Payment Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Form Header -->
                    <div class="border-b border-gray-200 p-6">
                        <h2 class="text-2xl font-bold text-gray-900">Payment Method</h2>
                        <p class="text-gray-600 mt-1">Complete your reservation with secure payment</p>
                    </div>

                    <!-- Payment Methods Tabs -->
                    <div class="border-b border-gray-200">
                        <div class="flex">
                            <button class="payment-tab active flex-1 py-4 px-6 text-center border-b-2 border-blue-500 bg-blue-50">
                                <i class="fas fa-credit-card mr-2"></i> Credit/Debit Card
                            </button>
                            <button class="payment-tab flex-1 py-4 px-6 text-center border-b-2 border-transparent hover:bg-gray-50">
                                <i class="fab fa-paypal mr-2"></i> PayPal
                            </button>
                            <button class="payment-tab flex-1 py-4 px-6 text-center border-b-2 border-transparent hover:bg-gray-50">
                                <i class="fas fa-university mr-2"></i> Bank Transfer
                            </button>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form id="paymentForm" class="p-6 space-y-6">
                        <!-- Contact Information -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Email Address *
                                    </label>
                                    <input type="email"
                                           required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                           placeholder="your.email@example.com">
                                    <p class="text-xs text-gray-500 mt-1">Booking confirmation will be sent here</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Phone Number *
                                    </label>
                                    <input type="tel"
                                           required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                           placeholder="+1 (555) 123-4567">
                                    <p class="text-xs text-gray-500 mt-1">For urgent updates about your stay</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card Information -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Card Information</h3>
                            <div class="space-y-6">
                                <!-- Card Number -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Card Number *
                                    </label>
                                    <div class="relative">
                                        <input type="text"
                                               id="cardNumber"
                                               required
                                               maxlength="19"
                                               class="card-number w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all pl-12"
                                               placeholder="1234 5678 9012 3456">
                                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2">
                                            <i class="fas fa-credit-card text-gray-400 text-lg"></i>
                                        </div>
                                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 flex space-x-2">
                                            <img src="https://img.icons8.com/color/24/000000/visa.png" alt="Visa" class="w-8 h-6 object-contain">
                                            <img src="https://img.icons8.com/color/24/000000/mastercard.png" alt="Mastercard" class="w-8 h-6 object-contain">
                                            <img src="https://img.icons8.com/color/24/000000/amex.png" alt="Amex" class="w-8 h-6 object-contain">
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Expiry Date -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Expiry Date *
                                        </label>
                                        <div class="flex space-x-3">
                                            <select required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                                <option value="">Month</option>
                                                <?php for($i = 1; $i <= 12; $i++): ?>
                                                    <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>">
                                                        <?= str_pad($i, 2, '0', STR_PAD_LEFT) ?> - <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                            <select required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                                <option value="">Year</option>
                                                <?php for($i = date('Y'); $i <= date('Y') + 10; $i++): ?>
                                                    <option value="<?= $i ?>"><?= $i ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- CVV -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            CVV *
                                        </label>
                                        <div class="relative">
                                            <input type="text"
                                                   required
                                                   maxlength="4"
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all pr-12"
                                                   placeholder="123">
                                            <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                                <button type="button" class="text-gray-400 hover:text-gray-600" title="What is CVV?">
                                                    <i class="fas fa-question-circle"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">3 or 4 digits on back of card</p>
                                    </div>
                                </div>

                                <!-- Cardholder Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Cardholder Name *
                                    </label>
                                    <input type="text"
                                           required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                           placeholder="As shown on card">
                                </div>

                                <!-- Billing Address -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Billing Address
                                    </label>
                                    <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                              rows="2"
                                              placeholder="Optional billing address"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex items-start">
                                <input type="checkbox"
                                       id="terms"
                                       required
                                       class="custom-checkbox h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mt-1">
                                <label for="terms" class="ml-3 text-sm text-gray-700">
                                    I agree to the <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">Terms & Conditions</a> and <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">Cancellation Policy</a>. I understand that my card will be charged the total amount shown.
                                </label>
                            </div>
                            <div class="flex items-start mt-4">
                                <input type="checkbox"
                                       id="newsletter"
                                       class="custom-checkbox h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mt-1">
                                <label for="newsletter" class="ml-3 text-sm text-gray-700">
                                    Send me special offers and travel tips from BookStay (optional)
                                </label>
                            </div>
                        </div>
                        </form>
                    <!-- Submit Button -->
                    <div class="pt-6">
                        <form action="reserve_email_send.php"  method="post">
                            <!--                                <input type="hidden" name="sent" value="1">-->
                            <button type="submit"
                                    id="submitBtn"
                                    class="w-full py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl flex items-center justify-center">
                                <i class="fas fa-lock mr-3"></i> Pay Securely - $<?= formatPrice($reservation['price']) ?>
                            </button>
                        </form>
                        <p class="text-center text-sm text-gray-600 mt-4">
                            <i class="fas fa-shield-alt mr-2"></i> Your payment is secured with 256-bit SSL encryption
                        </p>
                    </div>
                    </form>
                </div>



                <!-- Security Badges -->
                <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
                        <div class="text-gray-400 text-2xl mb-2"><i class="fas fa-shield-alt"></i></div>
                        <p class="text-xs font-medium text-gray-700">SSL Secured</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
                        <div class="text-gray-400 text-2xl mb-2"><i class="fas fa-user-shield"></i></div>
                        <p class="text-xs font-medium text-gray-700">PCI Compliant</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
                        <div class="text-gray-400 text-2xl mb-2"><i class="fas fa-heart"></i></div>
                        <p class="text-xs font-medium text-gray-700">24/7 Support</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
                        <div class="text-gray-400 text-2xl mb-2"><i class="fas fa-money-bill-wave"></i></div>
                        <p class="text-xs font-medium text-gray-700">Money Back Guarantee</p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="lg:col-span-1">
                <div class="sticky top-8">
                    <!-- Order Summary -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6">
                            <h2 class="text-xl font-bold text-white">Order Summary</h2>
                            <p class="text-blue-100 mt-1">Booking #<?= str_pad($reservation['id'], 6, '0', STR_PAD_LEFT) ?></p>
                        </div>

                        <div class="p-6">
                            <!-- Property Info -->
                            <div class="flex items-start mb-6">
                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-home text-gray-400 text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900"><?= htmlspecialchars($reservation['name']) ?></h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        <?= formatDate($reservation['start_date']) ?> - <?= formatDate($reservation['end_date']) ?>
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <i class="fas fa-moon mr-1"></i>
                                        <?= $reservation['user_id'] ?> night<?= $reservation['user_id'] > 1 ? 's' : '' ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Price Breakdown -->
                            <div class="space-y-3 border-t border-gray-200 pt-6">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Nightly rate × <?= $reservation['user_id'] ?></span>
                                    <span>$<?= formatPrice($reservation['price'] / $reservation['user_id']) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Cleaning fee</span>
                                    <span>$50.00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Service fee</span>
                                    <span>$25.00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Taxes</span>
                                    <span>$45.00</span>
                                </div>

                                <div class="border-t border-gray-300 pt-3 mt-3">
                                    <div class="flex justify-between text-lg font-bold">
                                        <span>Total</span>
                                        <span class="text-blue-600">$<?= formatPrice($reservation['price'] + 50 + 25 + 45) ?></span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">Charged in USD</p>
                                </div>
                            </div>

                            <!-- Cancellation Policy -->
                            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                                    <div>
                                        <p class="text-sm font-medium text-blue-800">Free cancellation</p>
                                        <p class="text-xs text-blue-700 mt-1">Cancel up to 24 hours before check-in for a full refund</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Need Help Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="font-bold text-gray-900 mb-4">Need Help?</h3>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 mr-3">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Call Us</p>
                                    <p class="text-sm text-gray-600">1-800-BOOKSTAY</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 mr-3">
                                    <i class="fas fa-comment"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Live Chat</p>
                                    <p class="text-sm text-gray-600">Available 24/7</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 mr-3">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Email</p>
                                    <p class="text-sm text-gray-600">help@bookstay.com</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-12 border-t border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p class="text-gray-600">© 2024 BookStay. All rights reserved.</p>
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-600 hover:text-blue-600 transition">Privacy Policy</a>
                    <a href="#" class="text-gray-600 hover:text-blue-600 transition">Terms of Service</a>
                    <a href="#" class="text-gray-600 hover:text-blue-600 transition">Refund Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Success Modal (hidden by default) -->
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-8 transform transition-all">
            <div class="text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-check text-green-600 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful!</h3>
                <p class="text-gray-600 mb-6">Your reservation has been confirmed. A receipt has been sent to your email.</p>
                <div class="space-y-3">
                    <form action="show_reservations.php" method = "POST">
                    <button onclick="closeModal()" class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        View Booking Details
                    </button>
                    <button onclick="closeModal()" class="w-full py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Back to Home
                    </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Card number formatting
        document.getElementById('cardNumber').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formatted = value.replace(/(\d{4})/g, '$1 ').trim();
            e.target.value = formatted.substring(0, 19);
        });

        // CVV formatting
        document.querySelector('input[placeholder="123"]').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 4);
        });

        // Payment method tabs
        document.querySelectorAll('.payment-tab').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.payment-tab').forEach(t => {
                    t.classList.remove('active', 'border-blue-500', 'bg-blue-50');
                    t.classList.add('border-transparent');
                });
                this.classList.add('active', 'border-blue-500', 'bg-blue-50');
                this.classList.remove('border-transparent');
            });
        });

        // Form submission
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            // e.preventDefault();

            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i> Processing...';
            submitBtn.disabled = true;

            // Simulate API call
            setTimeout(() => {
                // Show success modal
                document.getElementById('successModal').classList.remove('hidden');

                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2000);
        });

        // Close modal function
        function closeModal() {
            document.getElementById('successModal').classList.add('hidden');
        }

        // Close modal on outside click
        document.getElementById('successModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Tooltip for CVV
        document.querySelector('button[title="What is CVV?"]').addEventListener('click', function() {
            alert("CVV is the 3 or 4 digit security code on the back of your credit/debit card (front for American Express).");
        });
    </script>
</body>
</html>