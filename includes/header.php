<?php
// Detect base URL automatically (works for localhost and hosting)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$base = $protocol . $host . '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crestline Journeys | Explore Africa</title>
  <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
  <script src="<?php echo $base; ?>assets/js/emailjs-config.js"></script>
  <script src="<?php echo $base; ?>assets/js/main.js" defer></script>
</head>
<body>
     <!-- Header -->
  <header>
      <div class="logo">
         <img src="<?php echo $base; ?>assets/images/Pacific.png" alt="">  
        <a href="<?php echo $base; ?>index.php">Crestline <span>Journeys</span></a>
      </div>
        <button class="mobile-nav-toggle">☰</button>
        <nav>
            <button class="close-menu">×</button>
            <ul id="nav-menu">
                <li><a href="<?php echo $base; ?>index.php" class="active">Home</a></li>
                <li><a href="<?php echo $base; ?>pages/about.php">About</a></li>
                <li><a href="<?php echo $base; ?>pages/services.php">Services</a></li>
                <li><a href="<?php echo $base; ?>pages/destinations.php">Destinations</a></li>
                <li><a href="<?php echo $base; ?>pages/gallery.php">Gallery</a></li>
                <li><a href="<?php echo $base; ?>pages/contact.php">Contact</a></li>
                <span class="hover-indicator"></span>
            </ul>
        </nav>
  </header>
