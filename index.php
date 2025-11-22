<?php include('includes/header.php'); ?>

<!-- ================= HERO SECTION ================= -->
<section class="hero">
  <div class="hero-content">
    <h1>Explore Africa with <span>Crestline Journeys</span></h1>
    <p>Unforgettable safaris and authentic travel experiences across Rwanda, Uganda, and Tanzania.</p>
    <button class="btn" id="openEnquiryModal">ENQUIRE NOW</button>
  </div>
</section>

<!-- ================= ABOUT PREVIEW ================= -->
<section class="about-preview">
  <div class="container">
    <h2>Welcome to Crestline Journeys</h2>
    <p>We craft meaningful travel experiences that connect you with Africa’s incredible landscapes, cultures, and wildlife. From gorilla trekking in Rwanda to the Great Migration in the Serengeti, every journey is personalized and unforgettable.</p>
    <a href="pages/about.php" class="btn-secondary">Learn More</a>
  </div>
</section>

<!-- ================= SERVICES SECTION ================= -->
<section class="services">
  <div class="container">
    <h2>Our Services</h2>
    <div class="service-grid">
      <div class="service-item">
        <img src="assets/images/gorilla1.jpg" alt="">
        <h3>Itinerary Planning</h3>
        <p>Personalized travel itineraries that match your style and interests.</p>
      </div>
      <div class="service-item">
        <img src="assets/images/hotel.jpg" alt="">
        <h3>Hotel & AirBnB Booking</h3>
        <p>Comfortable accommodations chosen to fit your journey perfectly.</p>
      </div>
      <div class="service-item">
        <img src="assets/images/game.jpg" alt="">
        <h3>Safaris & Game Drives</h3>
        <p>Discover Africa’s wildlife in its natural habitat with expert guides.</p>
      </div>
    </div>
    <a href="pages/services.php" class="btn">View All Services</a>
  </div>
</section>

<!-- ================= FEATURED DESTINATIONS ================= -->
<section class="destinations">
  <div class="container">
    <h2>Popular Destinations</h2>
    
    <div class="destination-slideshow">
      <div class="destination-slide fade">
        <img src="assets/images/rwanda2.jpg" alt="Rwanda">
        <div class="destination-text">
          <h3>Rwanda</h3>
          <p>Home of the mountain gorillas, misty rainforests, and warm hospitality.</p>
          <a href="pages/rwanda.php" class="btn-secondary">Learn More</a>
        </div>
      </div>

      <div class="destination-slide fade">
        <img src="assets/images/uganda.jpg" alt="Uganda">
        <div class="destination-text">
          <h3>Uganda</h3>
          <p>The Pearl of Africa, rich in biodiversity and cultural experiences.</p>
          <a href="pages/uganda.php" class="btn-secondary">Learn More</a>
        </div>
      </div>

      <div class="destination-slide fade">
        <img src="assets/images/tanzania.png" alt="Tanzania">
        <div class="destination-text">
          <h3>Tanzania</h3>
          <p>Witness the Great Migration and the majesty of Mount Kilimanjaro.</p>
          <a href="pages/tanzania.php" class="btn-secondary">Learn More</a>
        </div>
      </div>
    </div>
  </div>
</section>
<script src="<?php echo $base; ?>assets/js/main.js" defer></script>
<?php include 'includes/enquiry_modal.php'; ?>


<?php include('includes/footer.php'); ?>
