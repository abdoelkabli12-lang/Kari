<?php
require_once('Database.php');
require_once("reviews.php");

$instance = Database::get_instance();
$db = $instance->connection;

echo $_SESSION['user_id'];
$_SESSION['rental_id'] = $_POST['rental_id'];


$stmt = $db->prepare("SELECT a.*, r.user_id FROM accommodation a JOIN reservations r ON a.id = r.rental_id WHERE a.id = :rental_id");
$stmt->execute(array(":rental_id" => $_SESSION['rental_id']));
$rental = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rental) {
    die("Rental not found or invalid ID");
}
$reviews = new Reviews($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review']) && isset($_POST['rating'])) {
    $user_id = $_SESSION['user_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
$reviews->addReviews($_SESSION['rental_id'], $user_id, $rating, $comment);
}

$getReviews = $reviews->getReviews($_SESSION['rental_id']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .star-rating .fa-star {
            color: #fbbf24;
        }
        .star-rating .empty-star {
            color: #d1d5db;
        }
        .star-rating-hover .fa-star:hover {
            color: #fbbf24;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-gray-50">
<!-- Write Review Modal -->
<div id="reviewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Write a Review</h3>
                    <p class="text-gray-600 mt-1">Share your experience at this property</p>
                </div>
                <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <!-- Review Form -->
            <form id="reviewForm" method="POST">
                <input type="hidden" name="rental_id" value="<?php echo $_SESSION['rental_id']; ?>">
                <input type="hidden" name="submit_review" value="1">

                <!-- Star Rating -->
                <div class="mb-8">
                    <label class="block text-lg font-semibold text-gray-800 mb-4">Overall Rating</label>
                    <div class="flex items-center space-x-1 star-rating-hover text-4xl mb-2" id="starContainer">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <i class="far fa-star cursor-pointer hover:text-yellow-400 transition-colors"
                               data-rating="<?php echo $i; ?>"
                               onclick="setRating(<?php echo $i; ?>)"
                               onmouseover="hoverStars(<?php echo $i; ?>)"
                               onmouseout="resetStars()"></i>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="rating" id="ratingValue" value="5">
                    <div class="flex justify-between text-sm text-gray-600 mt-2">
                        <span>Poor</span>
                        <span id="ratingText">Excellent</span>
                    </div>
                </div>

                <!-- Review Text -->
                <div class="mb-8">
                    <label for="reviewComment" class="block text-lg font-semibold text-gray-800 mb-4">
                        Your Review
                    </label>
                    <textarea id="reviewComment"
                              name="comment"
                              rows="6"
                              required
                              class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                              placeholder="Tell us about your experience... What did you like? What could be improved?"></textarea>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-sm text-gray-500">Minimum 50 characters</span>
                        <span id="charCount" class="text-sm text-gray-500">0/500 characters</span>
                    </div>
                </div>

                <!-- Review Guidelines -->
                <div class="mb-8 p-4 bg-blue-50 rounded-xl border border-blue-100">
                    <h4 class="font-semibold text-blue-800 mb-2 flex items-center">
                        <i class="fas fa-lightbulb mr-2"></i> Writing a good review
                    </h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle mr-2 mt-1 text-sm"></i>
                            <span>Describe your overall experience</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle mr-2 mt-1 text-sm"></i>
                            <span>Mention specific amenities or features you enjoyed</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle mr-2 mt-1 text-sm"></i>
                            <span>Note any areas that could be improved</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle mr-2 mt-1 text-sm"></i>
                            <span>Keep it honest and respectful</span>
                        </li>
                    </ul>
                </div>

                <!-- Submit Button -->
                <div class="flex space-x-4">
                    <button type="button"
                            onclick="closeReviewModal()"
                            class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            id="submitReviewBtn"
                            class="flex-1 px-6 py-3 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-paper-plane mr-2"></i> Submit Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Header -->
    <header class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800">Luxury <?php echo $rental['rental_type'] ?>Kari</h1>
        <p class="text-gray-600 mt-2">Experience luxury and comfort in this beautiful property</p>
    </header>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Content -->
        <div class="lg:w-2/3">
            <!-- Image Gallery -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
                <!-- Main Image -->
                <div class="relative h-80 md:h-96">
                    <img id="mainImage" src="https://images.unsplash.com/photo-1613977257363-707ba9348227?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80"
                         alt="Rental Property" class="w-full h-full object-cover">
                    <div class="absolute top-4 right-4 bg-white px-3 py-1 rounded-full shadow-md">
                        <span class="text-sm font-semibold text-gray-800">Featured</span>
                    </div>
                </div>

                <!-- Thumbnail Images -->
                <div class="p-4 grid grid-cols-4 gap-3">
                    <div class="cursor-pointer border-2 border-transparent hover:border-blue-500 rounded-lg overflow-hidden transition-all duration-200"
                         onclick="changeImage('https://images.unsplash.com/photo-1613977257363-707ba9348227?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80')">
                        <img src="https://images.unsplash.com/photo-1613977257363-707ba9348227?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80"
                             alt="Living Room" class="w-full h-20 object-cover">
                    </div>
                    <div class="cursor-pointer border-2 border-transparent hover:border-blue-500 rounded-lg overflow-hidden transition-all duration-200"
                         onclick="changeImage('https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80')">
                        <img src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80"
                             alt="Bedroom" class="w-full h-20 object-cover">
                    </div>
                    <div class="cursor-pointer border-2 border-transparent hover:border-blue-500 rounded-lg overflow-hidden transition-all duration-200"
                         onclick="changeImage('https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80')">
                        <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80"
                             alt="Kitchen" class="w-full h-20 object-cover">
                    </div>
                    <div class="cursor-pointer border-2 border-transparent hover:border-blue-500 rounded-lg overflow-hidden transition-all duration-200"
                         onclick="changeImage('https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80')">
                        <img src="https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80"
                             alt="Exterior" class="w-full h-20 object-cover">
                    </div>
                </div>
            </div>

            <!-- Property Details -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Property Details</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Location Details -->
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="bg-blue-100 p-3 rounded-xl mr-4">
                                <i class="fas fa-map-marker-alt text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-700 mb-1">Location</h3>
                                <p class="text-gray-800"><?php echo $rental['location'] ?></p>
                                <p class="text-gray-600 text-sm mt-1">Prime location with easy access to shopping, dining, and attractions</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-green-100 p-3 rounded-xl mr-4">
                                <i class="fas fa-users text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-700 mb-1">Accommodation</h3>
                                <div class="flex flex-wrap gap-4 mt-2">
                                    <div class="flex items-center">
                                        <i class="fas fa-bed text-gray-500 mr-2"></i>
                                        <span class="text-gray-800">4 Bedrooms</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-bath text-gray-500 mr-2"></i>
                                        <span class="text-gray-800">3 Bathrooms</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Amenities -->
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="bg-purple-100 p-3 rounded-xl mr-4">
                                <i class="fas fa-star text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-700 mb-2">Top Amenities</h3>
                                <div class="flex flex-wrap gap-2">
                                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">Swimming Pool</span>
                                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">WiFi</span>
                                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">Air Conditioning</span>
                                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">Parking</span>
                                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">Kitchen</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="font-semibold text-gray-700 mb-3">Description</h3>
                    <p class="text-gray-600">
                        <?php echo $rental['description'] ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Booking Sidebar -->
        <div class="lg:w-1/3">
            <div class="sticky top-8">
                <!-- Price Card -->
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <p class="text-gray-600">Price per night</p>
                            <div class="flex items-baseline">
                                <span class="text-3xl font-bold text-gray-800">$<?php echo number_format($rental['price'], 2) ?></span>
                                <span class="text-gray-500 ml-1">/ night</span>
                            </div>
                        </div>
                        <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                            4.8 ★
                        </div>
                    </div>

                    <!-- People Counter -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-semibold text-gray-700">Guests</h3>
                            <span id="guestCount" class="text-xl font-bold text-blue-600">4</span>
                        </div>
                        <div class="flex items-center justify-between bg-gray-100 rounded-xl p-3">
                            <button onclick="adjustGuests(-1)" class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition-colors">
                                <i class="fas fa-minus text-gray-700"></i>
                            </button>
                            <div class="text-center">
                                <div class="text-lg font-semibold text-gray-800" id="peopleText">4 People</div>
                                <div class="text-sm text-gray-500">Max: 8 people</div>
                            </div>
                            <button onclick="adjustGuests(1)" class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition-colors">
                                <i class="fas fa-plus text-gray-700"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Total Price -->
                    <div class="border-t border-gray-200 pt-4 mb-6">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">$350 × 4 nights</span>
                            <span class="font-semibold text-gray-800">$1,400</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Cleaning fee</span>
                            <span class="font-semibold text-gray-800">$120</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Service fee</span>
                            <span class="font-semibold text-gray-800">$85</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-3 border-t border-gray-200">
                            <span class="text-gray-800">Total</span>
                            <span class="text-blue-600">$1,605</span>
                        </div>
                    </div>

                    <!-- Reserve Button -->
                    <button class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold py-4 rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 shadow-md hover:shadow-lg">
                        <i class="fas fa-calendar-check mr-2"></i> Reserve Now
                    </button>

                    <p class="text-center text-gray-500 text-sm mt-4">You won't be charged yet</p>
                </div>

                <!-- Host Info -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="font-semibold text-gray-700 mb-4">Hosted by</h3>
                    <div class="flex items-center">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80"
                             alt="Host" class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <h4 class="font-semibold text-gray-800">Michael Johnson</h4>
                            <p class="text-gray-600 text-sm">Superhost • 5 years hosting</p>
                        </div>
                    </div>
                    <div class="mt-4 flex space-x-4">
                        <button class="flex-1 bg-gray-100 text-gray-800 font-medium py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-envelope mr-2"></i> Contact
                        </button>
                        <button class="flex-1 bg-gray-100 text-gray-800 font-medium py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-share-alt mr-2"></i> Share
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="mt-12">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-8">Guest Reviews</h2>

            <!-- Overall Rating Summary -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
                <!-- Average Rating -->
                <div class="text-center lg:text-left">
                    <div class="flex items-center justify-center lg:justify-start mb-4">
                        <div class="text-5xl font-bold text-gray-800 mr-4">4.8</div>
                        <div>
                            <div class="flex star-rating text-2xl mb-1">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <p class="text-gray-600">Based on 25 reviews</p>
                        </div>
                    </div>
                    <button <?php if($rental['start_date'] >= date('Y-m-d') && $_SESSION['user_id'] === $rental['user_id']):
                        echo "onclick='openReviewModal()'" ?>
                    <?php endif;?> class="mt-4 px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors <?php if ($rental['start_date'] < date('Y-m-d')): echo "cursor-not-allowed"?> <?php endif; ?>">
                        <i class="fas fa-pen mr-2"></i> Write a Review
                    </button>
                </div>

                <!-- Rating Breakdown -->
                <div class="lg:col-span-2">
                    <div class="space-y-3">
                        <?php
                        $breakdowns = [
                                ['stars' => 5, 'count' => 18, 'percent' => 72],
                                ['stars' => 4, 'count' => 5, 'percent' => 20],
                                ['stars' => 3, 'count' => 1, 'percent' => 4],
                                ['stars' => 2, 'count' => 1, 'percent' => 4],
                                ['stars' => 1, 'count' => 0, 'percent' => 0]
                        ];
                        ?>

                        <?php foreach($breakdowns as $breakdown): ?>
                            <div class="flex items-center">
                                <div class="w-16 text-gray-700 font-medium">
                                    <?php echo $breakdown['stars']; ?> star<?php echo $breakdown['stars'] > 1 ? 's' : ''; ?>
                                </div>
                                <div class="flex-1 mx-4">
                                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-yellow-500 rounded-full" style="width: <?php echo $breakdown['percent']; ?>%"></div>
                                    </div>
                                </div>
                                <div class="w-12 text-right text-gray-600">
                                    <?php echo $breakdown['count']; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Review Filter/Sort -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 p-4 bg-gray-50 rounded-xl">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 md:mb-0">Latest Reviews</h3>
                <div class="flex space-x-4">
                    <select class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>Sort by: Most Recent</option>
                        <option>Sort by: Highest Rated</option>
                        <option>Sort by: Lowest Rated</option>
                    </select>
                    <button class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                </div>
            </div>

            <!-- Reviews List -->
            <div class="space-y-8">
                <?php
                $sample_reviews = [
                        [
                                'name' => 'Sarah Johnson',
                                'date' => '2 weeks ago',
                                'rating' => 5,
                                'comment' => 'Absolutely stunning property! The views were breathtaking and the amenities were top-notch. Perfect for our family vacation. Would definitely book again!',
                                'avatar' => 'https://images.unsplash.com/photo-1494790108755-2616b786d4d3?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80',
                                'travel_type' => 'Family vacation'
                        ],
                        [
                                'name' => 'Michael Chen',
                                'date' => '1 month ago',
                                'rating' => 4,
                                'comment' => 'Great location and very comfortable stay. The host was responsive and helpful. The kitchen was well-equipped. Only minor issue was the WiFi was a bit slow in the evenings.',
                                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80',
                                'travel_type' => 'Business trip'
                        ],
                        [
                                'name' => 'Emma Williams',
                                'date' => '2 months ago',
                                'rating' => 5,
                                'comment' => 'Perfect romantic getaway! The property was even better than the photos. Very clean, cozy, and private. Loved the fireplace and hot tub. Will be recommending to all our friends!',
                                'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80',
                                'travel_type' => 'Couples retreat'
                        ],
                        [
                                'name' => 'David Rodriguez',
                                'date' => '3 months ago',
                                'rating' => 3,
                                'comment' => 'The property was nice but there were some maintenance issues. The air conditioning wasn\'t working properly and one of the bathroom doors was stuck. Host was responsive but couldn\'t fix immediately.',
                                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80',
                                'travel_type' => 'Group trip'
                        ],
                        [
                                'name' => 'Lisa Thompson',
                                'date' => '4 months ago',
                                'rating' => 5,
                                'comment' => 'Best rental experience ever! Everything was perfect from check-in to check-out. The property manager was very helpful and the place was spotless. Can\'t wait to come back next year!',
                                'avatar' => 'https://images.unsplash.com/photo-1544725176-7c40e5a71c5e?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80',
                                'travel_type' => 'Family reunion'
                        ]
                ];
                ?>

                <?php foreach($getReviews as $review): ?>
                    <div class="border border-gray-200 rounded-xl p-6 hover:border-blue-300 transition-colors">
                        <div class="flex justify-between items-start mb-4">

                            <div class="text-right">
                                <div class="flex star-rating text-lg mb-1">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <?php if($i <= $review['rating']): ?>
                                            <i class="fas fa-star"></i>
                                        <?php else: ?>
                                            <i class="far fa-star empty-star"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                                <p class="text-sm text-gray-500"><?php echo $review['created_at']; ?></p>
                            </div>
                        </div>

                        <p class="text-gray-700 mb-4"><?php echo $review['comment']; ?></p>

                        <div class="flex items-center text-sm text-gray-500">
                            <button class="flex items-center mr-6 hover:text-blue-600 transition-colors">
                                <i class="far fa-thumbs-up mr-2"></i> Helpful (12)
                            </button>
                            <button class="flex items-center hover:text-blue-600 transition-colors">
                                <i class="far fa-comment mr-2"></i> Reply
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Load More Button -->
            <div class="text-center mt-10">
                <button class="px-8 py-3 border-2 border-blue-600 text-blue-600 font-medium rounded-lg hover:bg-blue-50 transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i> Load More Reviews
                </button>
            </div>

            <!-- Review Guidelines -->
            <div class="mt-10 p-6 bg-blue-50 rounded-xl border border-blue-100">
                <h4 class="font-semibold text-blue-800 mb-3 flex items-center">
                    <i class="fas fa-info-circle mr-2"></i> Review Guidelines
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-700">
                    <div class="flex items-start">
                        <i class="fas fa-check-circle mr-2 mt-1"></i>
                        <span>Reviews should be based on actual stay experiences</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle mr-2 mt-1"></i>
                        <span>Be respectful and constructive in your feedback</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle mr-2 mt-1"></i>
                        <span>Include specific details about what you liked or disliked</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle mr-2 mt-1"></i>
                        <span>Only guests who have stayed can leave reviews</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Note -->
    <div class="mt-8 text-center text-gray-500 text-sm">
        <p><i class="fas fa-shield-alt mr-1"></i> Your reservation is protected by our secure payment system</p>
    </div>
</div>

<script>
    // Rating system
    let currentRating = 5;
    const ratingTexts = ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];

    function setRating(rating) {
        currentRating = rating;
        document.getElementById('ratingValue').value = rating;
        document.getElementById('ratingText').textContent = ratingTexts[rating - 1];

        // Update stars
        const stars = document.querySelectorAll('#starContainer i');
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('far');
                star.classList.add('fas');
                star.style.color = '#fbbf24';
            } else {
                star.classList.remove('fas');
                star.classList.add('far');
                star.style.color = '#d1d5db';
            }
        });
    }

    function hoverStars(rating) {
        const stars = document.querySelectorAll('#starContainer i');
        stars.forEach((star, index) => {
            if (index < rating) {
                star.style.color = '#fbbf24';
            } else {
                star.style.color = '#d1d5db';
            }
        });
    }

    function resetStars() {
        const stars = document.querySelectorAll('#starContainer i');
        stars.forEach((star, index) => {
            if (index < currentRating) {
                star.style.color = '#fbbf24';
            } else {
                star.style.color = '#d1d5db';
            }
        });
    }

    // Character count for textarea
    const textarea = document.getElementById('reviewComment');
    const charCount = document.getElementById('charCount');
    const submitBtn = document.getElementById('submitReviewBtn');

    textarea.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length + '/500 characters';

        // Enable/disable submit button
        if (length >= 50 && length <= 500) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    });

    // Modal functions
    function openReviewModal() {
        document.getElementById('reviewModal').classList.remove('hidden');
        // Reset form
        setRating(5);
        textarea.value = '';
        charCount.textContent = '0/500 characters';
        submitBtn.disabled = true;
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('reviewModal').addEventListener('click', function(e) {
        if (e.target.id === 'reviewModal') {
            closeReviewModal();
        }
    });

    // Initialize rating
    document.addEventListener('DOMContentLoaded', function() {
        setRating(5);
    });

    // Existing functions
    let guestCount = 1;
    const maxGuests = <?php echo $rental['guest_count'];?>;
    const pricePerNight = <?php echo $rental['price']; ?>;
    const cleaningFee = 120;
    const serviceFee = 85;

    function changeImage(imageUrl) {
        document.getElementById('mainImage').src = imageUrl;
        document.querySelectorAll('.cursor-pointer').forEach(thumb => {
            thumb.classList.remove('border-blue-500');
            thumb.classList.add('border-transparent');
        });
        event.currentTarget.classList.remove('border-transparent');
        event.currentTarget.classList.add('border-blue-500');
    }

    function adjustGuests(change) {
        const newCount = guestCount + change;
        if (newCount >= 1 && newCount <= maxGuests) {
            guestCount = newCount;
            document.getElementById('guestCount').textContent = guestCount;
            document.getElementById('peopleText').textContent = guestCount + (guestCount === 1 ? ' Person' : ' People');
            const nights = 1;
            const subtotal = pricePerNight * nights;
            const total = subtotal + cleaningFee + serviceFee;
            document.getElementById('subtotal').textContent = '$' + subtotal.toLocaleString('en-US', {minimumFractionDigits: 2});
            document.getElementById('totalPrice').textContent = '$' + total.toLocaleString('en-US', {minimumFractionDigits: 2});
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.cursor-pointer').classList.remove('border-transparent');
        document.querySelector('.cursor-pointer').classList.add('border-blue-500');
    });
</script>
</body>
</html>