<?php 
include('../includes/header.php');
include('../includes/destinations-data.php');

// Get destination slug from URL
$destination_slug = isset($_GET['destination']) ? $_GET['destination'] : '';

// Check if destination exists
if (!isset($destinations[$destination_slug])) {
    // Redirect to destinations page if destination not found
    header('Location: ' . $base . 'pages/destinations.php');
    exit;
}

$destination = $destinations[$destination_slug];
?>

<!-- ================= DESTINATION DETAIL HERO WITH VIDEO ================= -->
<section class="destination-detail-hero">
  <?php if (!empty($destination['video'])): ?>
  <div class="hero-video-wrapper">
    <video autoplay muted loop playsinline>
      <source src="<?php echo $base . htmlspecialchars($destination['video']); ?>" type="video/mp4">
      Your browser does not support the video tag.
    </video>
    <div class="hero-overlay"></div>
  </div>
  <?php endif; ?>
  <div class="hero-content animate-fade-in">
    <h1 class="animate-slide-up"><?php echo htmlspecialchars($destination['name']); ?></h1>
    <p class="destination-location animate-slide-up" style="animation-delay: 0.2s;"><?php echo htmlspecialchars($destination['country']); ?></p>
  </div>
</section>

<!-- ================= DESTINATION DETAIL CONTENT ================= -->
<section class="destination-detail-content">
  <div class="container">
    
    <!-- Mixed Content Layout: Text and Images Combined -->
    <div class="destination-mixed-content">
      
      <!-- Full Description Section -->
      <div class="content-section animate-fade-in-up">
        <div class="content-text">
          <h2>About <?php echo htmlspecialchars($destination['name']); ?></h2>
          <div class="description-text">
            <?php echo nl2br(htmlspecialchars($destination['full_description'])); ?>
          </div>
        </div>
        <?php if (!empty($destination['images']) && isset($destination['images'][0])): ?>
        <div class="content-image" style="height: 1000px;">
          <img src="<?php echo $base . htmlspecialchars($destination['images'][0]); ?>" 
               alt="<?php echo htmlspecialchars($destination['name']); ?>"
               onclick="openImageModal(this.src, this.alt)" style="height: 1000px;">
        </div>
        <?php endif; ?>
      </div>

      <!-- Highlights Section with Image -->
      <?php if (!empty($destination['highlights'])): ?>
      <div class="content-section content-reverse animate-fade-in-up">
        <div class="content-text">
          <h2>Highlights & Activities</h2>
          <ul class="highlights-list">
            <?php foreach ($destination['highlights'] as $index => $highlight): ?>
              <li class="animate-fade-in-up" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                <i class="fas fa-check-circle"></i> 
                <span><?php echo htmlspecialchars($highlight); ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php if (!empty($destination['images']) && isset($destination['images'][1])): ?>
        <div class="content-image">
          <img src="<?php echo $base . htmlspecialchars($destination['images'][1]); ?>" 
               alt="<?php echo htmlspecialchars($destination['name']); ?> - Activity"
               onclick="openImageModal(this.src, this.alt)">
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- Additional Information Section -->
      <div class="content-section animate-fade-in-up">
        <div class="content-text">
          <h2>Planning Your Visit</h2>
          <div class="destination-info-grid">
            <?php if (!empty($destination['best_time'])): ?>
            <div class="info-item animate-fade-in-up" style="animation-delay: 0.2s;">
              <i class="fas fa-calendar-alt"></i>
              <div>
                <h3>Best Time to Visit</h3>
                <p><?php echo htmlspecialchars($destination['best_time']); ?></p>
              </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($destination['duration'])): ?>
            <div class="info-item animate-fade-in-up" style="animation-delay: 0.3s;">
              <i class="fas fa-clock"></i>
              <div>
                <h3>Recommended Duration</h3>
                <p><?php echo htmlspecialchars($destination['duration']); ?></p>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <?php if (!empty($destination['images']) && isset($destination['images'][2])): ?>
        <div class="content-image">
          <img src="<?php echo $base . htmlspecialchars($destination['images'][2]); ?>" 
               alt="<?php echo htmlspecialchars($destination['name']); ?> - Planning"
               onclick="openImageModal(this.src, this.alt)">
        </div>
        <?php endif; ?>
      </div>

      <!-- Additional Images Gallery -->
      <?php if (!empty($destination['images']) && count($destination['images']) > 3): ?>
      <div class="additional-gallery-section animate-fade-in-up">
        <h2>More Images from <?php echo htmlspecialchars($destination['name']); ?></h2>
        <div class="gallery-grid-mixed">
          <?php for ($i = 3; $i < count($destination['images']); $i++): ?>
            <div class="gallery-item animate-fade-in-up" style="animation-delay: <?php echo ($i - 3) * 0.15; ?>s;">
              <img src="<?php echo $base . htmlspecialchars($destination['images'][$i]); ?>" 
                   alt="<?php echo htmlspecialchars($destination['name']); ?> - Image <?php echo $i + 1; ?>"
                   onclick="openImageModal(this.src, this.alt)">
            </div>
          <?php endfor; ?>
        </div>
      </div>
      <?php endif; ?>

    </div>

    <!-- Call to Action -->
    <div class="destination-cta animate-fade-in-up">
      <h2>Ready to Visit <?php echo htmlspecialchars($destination['name']); ?>?</h2>
      <p>Let us create a custom itinerary for your journey</p>
      <button class="btn" id="openEnquiryModal">Plan Your Journey</button>
    </div>

  </div>
</section>

<!-- Image Modal -->
<div id="imageModal" class="image-modal">
  <span class="close-modal">&times;</span>
  <img class="modal-content" id="modalImage">
  <div class="modal-caption" id="modalCaption"></div>
</div>

<?php include('../includes/enquiry_modal.php'); ?>
<?php include('../includes/footer.php'); ?>

<script>
// Image Modal functionality
function openImageModal(src, alt) {
  const modal = document.getElementById('imageModal');
  const modalImg = document.getElementById('modalImage');
  const captionText = document.getElementById('modalCaption');
  
  modal.style.display = 'block';
  modalImg.src = src;
  captionText.innerHTML = alt;
}

// Close modal when clicking the X
document.querySelector('.close-modal').onclick = function() {
  document.getElementById('imageModal').style.display = 'none';
}

// Close modal when clicking outside the image
window.onclick = function(event) {
  const modal = document.getElementById('imageModal');
  if (event.target == modal) {
    modal.style.display = 'none';
  }
}
</script>

