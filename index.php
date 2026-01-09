
<?php

//require 'vendor/autoload.php';

session_start();

require_once 'database.php';


$instance = Database::get_instance();
$db = $instance->connection;


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


if (!$_SESSION['user']){
    header("Location:index.php");
}

$stmt = $db->prepare("SELECT * FROM accommodation");
$stmt->execute();
$rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Real Estate Listings</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-800">

<!-- 🔝 Navbar -->
<header class="bg-black text-white">
    <div class="absolute right-[20rem]">
    <div class="absolute right-20 top-2 flex gap-1 items-center">
        <p class=" text-white ml-auto mr-4 font-semibold">
            <?php echo htmlspecialchars($userEmail); ?>
        </p>
        <img src="<?= $gravatarUrl ?>" class=" rounded-full w-10 h-10" alt="User avatar">

        <div>
            <?php echo $user ?>
        </div>
    </div>

    <a href="signup.php"><button id="user" class="absolute w-5 h-12  right-5 top-1"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M144 128a80 80 0 1 1 160 0 80 80 0 1 1 -160 0zm208 0a128 128 0 1 0 -256 0 128 128 0 1 0 256 0zM48 480c0-70.7 57.3-128 128-128l96 0c70.7 0 128 57.3 128 128l0 8c0 13.3 10.7 24 24 24s24-10.7 24-24l0-8c0-97.2-78.8-176-176-176l-96 0C78.8 304 0 382.8 0 480l0 8c0 13.3 10.7 24 24 24s24-10.7 24-24l0-8z"/></svg>
        </button></a>
    <button id="logout-btn" class="absolute right-[22rem] top-3 text-danger pt-1 pb-1 pl-2 pr-2 bg-black rounded font-extrabold">
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
                    <button type="submit" name = "logout"
                            class="w-full rounded-xl bg-red-500 py-2 text-white font-semibold
                 transition-all duration-300 hover:bg-red-600 hover:scale-[1.02]">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
    </div>
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <h1 class="text-xl font-bold">Kari</h1>
    <nav class="flex gap-6 text-sm">
      <a href="#" class="hover:text-gray-300">Buy</a>
      <a href="#" class="hover:text-gray-300">Rent</a>
      <a href="show_reservations.php" class="hover:text-gray-300">My reservations</a>
      <a href="#" class="hover:text-gray-300">Loans</a>
    </nav>
  </div>
</header>

<!-- 🔍 Filters -->
<section class="bg-white shadow">
  <div class="max-w-7xl mx-auto px-6 py-4 flex flex-wrap gap-3">
    <input class="border rounded-full px-4 py-2 w-64" placeholder="Location..." />
    <select class="border rounded-full px-4 py-2">
      <option>For Rent</option>
      <option>For Sale</option>
    </select>
    <select class="border rounded-full px-4 py-2">
      <option>Price</option>
      <option>$100k+</option>
      <option>$300k+</option>
    </select>
    <select class="border rounded-full px-4 py-2">
      <option>Beds & Baths</option>
    </select>
  </div>
</section>

<!-- 🏘 Listings -->
<main class="max-w-7xl mx-auto px-6 py-10">
  <h2 class="text-2xl font-semibold mb-6">Los Angeles CA Rental Listings</h2>

  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

    <!-- Card -->
    <div class="bg-white rounded-xl shadow hover:shadow-xl transition overflow-hidden group">
      <div class="relative">
        <img src="https://images.unsplash.com/photo-1568605114967-8130f3a36994"
             class="w-full h-52 object-cover group-hover:scale-105 transition duration-500" />

        <!-- Rating -->
        <span class="absolute top-3 left-3 bg-white px-2 py-1 rounded-full text-sm font-semibold">
          ⭐ 5.0
        </span>

        <!-- Like -->
        <button onclick="toggleLike(this)"
          class="absolute top-3 right-3 bg-white p-2 rounded-full shadow hover:scale-110 transition">
          ❤️
        </button>
      </div>

      <div class="p-4">
        <h3 class="font-semibold text-lg">Highland Retreat</h3>
        <p class="text-sm text-gray-500">2345 Highland Ave, LA</p>

        <div class="flex justify-between items-center mt-3">
          <span class="text-xl font-bold">$400,000</span>
          <span class="text-sm text-gray-600">2 Beds • 1 Bath</span>
        </div>
      </div>
    </div>

    <!-- Duplicate cards -->
    <div class="bg-white rounded-xl shadow hover:shadow-xl transition overflow-hidden group">
      <img src="https://images.unsplash.com/photo-1570129477492-45c003edd2be"
           class="w-full h-52 object-cover group-hover:scale-105 transition duration-500" />
      <div class="p-4">
        <h3 class="font-semibold text-lg">Beverly Breeze</h3>
        <p class="text-sm text-gray-500">Beverly Dr, LA</p>
        <div class="flex justify-between mt-3">
          <span class="text-xl font-bold">$450,000</span>
          <span class="text-sm">3 Beds • 1 Bath</span>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow hover:shadow-xl transition overflow-hidden group">
      <img src="https://images.unsplash.com/photo-1599423300746-b62533397364"
           class="w-full h-52 object-cover group-hover:scale-105 transition duration-500" />
      <div class="p-4">
        <h3 class="font-semibold text-lg">Laurel Canyon Nest</h3>
        <p class="text-sm text-gray-500">Laurel Canyon Blvd</p>
        <div class="flex justify-between mt-3">
          <span class="text-xl font-bold">$475,000</span>
          <span class="text-sm">2 Beds • 1 Bath</span>
        </div>
      </div>
    </div>
  </div>

      <?php if(count($rentals) > 0): ?>
          <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8 mt-6">
              <?php foreach($rentals as $rental):
                  // Format dates
                  $start_date = date('d M Y', strtotime($rental['start_date']));
                  $end_date = date('d M Y', strtotime($rental['end_date']));

                  // Determine if rental is available

                  // Get rental_type icon
                  $type_icons = [
                          'House' => 'fa-house',
                          'Villa' => 'fa-house-chimney-window',
                          'Hotel' => 'fa-hotel',
                          'Apartment' => 'fa-building'
                  ];
                  $type_icon = $type_icons[$rental['rental_type']] ?? 'fa-home';
                  ?>
                  <!-- Rental Card -->
                  <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
                      <!-- Rental Image -->
                      <div class="relative h-64 overflow-hidden">
                          <img src="<?php echo !empty($rental['image']) ? $rental['image'] : 'https://source.unsplash.com/600x400/?' . urlencode($rental['rental_type']); ?>"
                               class="w-full h-full object-cover transform hover:scale-110 transition duration-700"
                               alt="<?php echo htmlspecialchars($rental['rental_type']); ?>">
                          <!-- Status Badge -->
                          <!-- Price Badge -->
                          <div class="absolute top-4 right-4">
                        <span class="px-4 py-2 rounded-full bg-white/95 backdrop-blur-sm text-lg font-bold text-indigo-700 shadow-md">
                            $<?php echo number_format($rental['price'], 2); ?><span class="text-sm font-normal">/night</span>
                        </span>
                          </div>
                      </div>

                      <div class="p-6">
                          <!-- Header -->
                          <div class="flex justify-between items-start mb-4">
                              <div class="pr-4">
                                  <h2 class="text-2xl font-bold text-gray-900 mb-1"><?php echo htmlspecialchars($rental['rental_type']); ?></h2>
                                  <div class="flex items-center text-gray-600">
                                      <i class="fas <?php echo $type_icon; ?> text-base mr-3"></i>
                                      <span class="text-base"><?php echo htmlspecialchars($rental['rental_type']); ?></span>
                                  </div>
                              </div>
                              <div class="text-right flex-shrink-0">
                                  <div class="flex items-center text-yellow-500 mb-1">
                                      <i class="fas fa-star text-base"></i>
                                      <span class="ml-2 text-base font-bold">4.8</span>
                                  </div>
                                  <span class="text-sm text-gray-500">(5 reviews)</span>
                              </div>
                          </div>

                          <!-- Host Info -->
                          <div class="mb-5 flex items-center bg-gray-50 rounded-xl p-4">
                              <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                                  <i class="fas fa-user text-indigo-600 text-lg"></i>
                              </div>
                              <div class="ml-4">
                                  <p class="text-base font-semibold text-gray-800"><?php echo htmlspecialchars($rental['host_name']); ?></p>
                                  <p class="text-sm text-gray-500">Host</p>
                              </div>
                          </div>

                          <!-- Location -->
                          <div class="mb-5 flex items-center text-gray-700">
                              <i class="fas fa-map-marker-alt text-red-500 text-lg mr-3"></i>
                              <span class="text-base font-medium truncate"><?php echo htmlspecialchars($rental['location']); ?></span>
                          </div>

                          <!-- Description -->
                          <div class="mb-6">
                              <p class="text-gray-700 text-base line-clamp-3 leading-relaxed">
                                  <?php echo htmlspecialchars($rental['description']); ?>
                              </p>
                          </div>

                          <!-- Dates -->
                          <div class="mb-6 pt-5 border-t border-gray-200">
                              <p class="text-sm text-gray-500 mb-3 font-medium">AVAILABILITY</p>
                              <div class="flex justify-between items-center bg-gray-50 rounded-xl p-4">
                                  <div class="text-center">
                                      <p class="text-xs text-gray-500 font-medium mb-1">CHECK-IN</p>
                                      <p class="text-lg font-bold text-gray-800"><?php echo $start_date; ?></p>
                                  </div>
                                  <div class="text-gray-300 mx-4">
                                      <i class="fas fa-arrow-right text-xl"></i>
                                  </div>
                                  <div class="text-center">
                                      <p class="text-xs text-gray-500 font-medium mb-1">CHECK-OUT</p>
                                      <p class="text-lg font-bold text-gray-800"><?php echo $end_date; ?></p>
                                  </div>
                              </div>
                          </div>

                          <!-- Action Buttons for Traveler -->
                          <form action  = "rental_details.php" method="POST">
                          <div class="flex justify-between pt-5 border-t border-gray-200">
                              <input type="hidden" name="rental_id" value="<?php echo $rental['id']; ?>">
                              <button class="text-gray-700 hover:text-gray-900 font-semibold text-base flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 transition duration-200">
                                  <i class="fas fa-info-circle text-lg mr-3"></i> Details
                              </button>
                          </form>
                              <form action = "booking_form.php" method="post">
                                  <input type="hidden" name="rental_id" value="<?php echo $rental['id']; ?>">
                              <button type="submit" name ="bookBTN" class="text-white bg-indigo-600 hover:bg-indigo-700 font-semibold text-base flex items-center px-6 py-3 rounded-lg transition duration-200">
                                  <i class="fas fa-calendar-check text-lg mr-3"></i> Book Now
                              </button>
                              </form>
                              <button class="text-gray-700 hover:text-red-600 font-semibold text-base flex items-center px-4 py-3 rounded-lg hover:bg-red-50 transition duration-200">
                                  <i class="fas fa-heart text-lg mr-3"></i> Save
                              </button>
                          </div>
                      </div>
                  </div>
              <?php endforeach; ?>
          </div>
      <?php else: ?>
          <!-- Empty State -->
          <div class="text-center bg-white p-16 rounded-2xl shadow-lg">
              <div class="max-w-lg mx-auto">
                  <div class="w-32 h-32 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-8">
                      <i class="fas fa-home text-gray-400 text-5xl"></i>
                  </div>
                  <h2 class="text-3xl font-bold text-gray-800 mb-4">No rentals available</h2>
                  <p class="text-gray-600 text-lg mb-8">
                      There are currently no rental properties available. Check back later for new listings!
                  </p>
                  <button class="text-gray-600 hover:text-gray-800 font-medium text-base">
                      <i class="fas fa-sync-alt mr-2"></i> Refresh Page
                  </button>
              </div>
          </div>
      <?php endif; ?>
</main>

<!-- ⚙ JS -->
<script>
function toggleLike(btn) {
  btn.classList.toggle("text-red-500");
}
</script>

<script type="text/javascript" src = "script.js"></script>

</body>
</html>
