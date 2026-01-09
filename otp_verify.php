<?php

session_start();


echo $_SESSION['otp'];
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!isset($_SESSION['otp'])) {
      header("Location: otp_verify.php?error=missing");
        exit;
    }

    if ((string)$_POST['otp'] !== (string)$_SESSION['otp']) {
        header("Location: otp_verify.php?error=invalid");
        exit;
    }

    // OTP success → login user
    $_SESSION['user'] = $_SESSION['pending_user'];
    $_SESSION['user_id'] = $_SESSION['pending_user_id'];
    $_SESSION['login_success'] = true;



    // Cleanup
    unset($_SESSION['otp'], $_SESSION['user'], $_SESSION['user_id']);

    header("Location: Traveler.php?success=login");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Money Track</title>
  <link rel="shortcut icon" href="297-2977261_wallet-budget-tracker-budgetbakers-icon-app.png" type="image/png">
  <link rel="icon" href="297-2977261_wallet-budget-tracker-budgetbakers-icon-app.png" type="image/png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="/dist/styles/webawesome.css" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

</head>

<body class="m-0 font-sans antialiased font-normal dark:bg-slate-900 leading-default bg-gray-50 ">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            'press-start': ['"Press Start 2P"', 'cursive'],
            'K2D': ['"K2D"', 'cursive'],
          },
          colors: {
            myred: '#A63348',
            magenta: '#5F425F',
            myblue: '#3B4A6B',
            cyan: '#1B7C97',
            myblue: '#3B4A6B',
            gred: '#FF0000',
            legendary: '#FFD400',
            rare: '#0C0091',
            uncommon: '#00FF11',
            common: '#FFFFFF',
            tblue: '#1F2937',
            mygrey: '#6B7280',
            gblue: '#374151',
            stroke: '#4B5563',
            underbg: '#E5E7EB',
            grayish: '#374151',
            brownish: '#EEB76B',
            orangish: '#E2703A',
            redmagenta: '#9C3D54',
            dark_brown: '#310B0B',
          }
        }
      }
    }
  </script>

  <header class="relative">
    <div class=" flex gap-4 bg-gradient-to-r from-green-600 via-green-300 to-blue-600 h-14 w-full flex items-center">
      <h1 class="ml-4 font-semibold font-[Inter] text-2xl text-white">
        Smart Wallet
      </h1>
  </header>



  <div id="bg-auth" class="auth  fixed z-[1000] w-screen min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-800 px-4 sm:px-6 lg:px-8">
    <div id="auth-form" class="relative py-3 sm:max-w-xs sm:mx-auto">
      <div class="min-h-96 px-8 py-6 mt-4 text-left bg-white dark:bg-gray-900  rounded-xl shadow-lg">
        <div class="flex flex-col justify-center items-center h-full select-none">
          <div class="flex flex-col items-center justify-center gap-2 mb-8">
            <img src='' class="w-8 rounded-full" />
            <p class="m-0 text-[16px] font-semibold dark:text-white">please enter the code we sent to your email</p>
          </div>
          <div class="w-full flex flex-col gap-2">
            <form action="otp_verify.php" method="post">
              <input type="hidden" name="emailL" value="<?= htmlspecialchars($_SESSION['pending_user'] ?? '') ?>">
              <label class="font-semibold text-xs text-gray-400 ">Enter Code</label>
              <input name="otp" type="password" class="border rounded-lg px-3 py-2 mb-5 text-sm text-white w-full outline-none dark:border-gray-500 dark:bg-gray-900" placeholder="******" required />
              <div>
                <input type="submit" class="auth py-1 px-8 bg-blue-500 hover:bg-blue-800 focus:ring-offset-blue-200 text-white w-full transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-lg cursor-pointer select-none" value="Verify">
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>


<!--  <script type="text/javascript" src="script.js" defer></script>-->
</body>