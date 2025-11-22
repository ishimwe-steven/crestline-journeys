<?php
include '../includes/db_connect.php';
include '../includes/header.php';

// Get all images from database
$imagesQuery = $conn->query("SELECT * FROM site_images WHERE is_active = 1 ORDER BY display_order ASC, created_at DESC");
$galleryImages = [];

while ($row = $imagesQuery->fetch_assoc()) {
    $galleryImages[] = $row;
}

// If no images from admin, use default images
if (empty($galleryImages)) {
    $galleryImages = [
        ['image_path' => 'assets/images/gorilla1.jpg', 'title' => 'Mountain Gorilla', 'description' => 'Gorilla trekking in Rwanda'],
        ['image_path' => 'assets/images/rwanda2.jpg', 'title' => 'Rwanda Landscape', 'description' => 'Beautiful hills of Rwanda'],
        ['image_path' => 'assets/images/uganda.jpg', 'title' => 'Uganda Safari', 'description' => 'Wildlife in Uganda'],
        ['image_path' => 'assets/images/tanzania.png', 'title' => 'Tanzania Serengeti', 'description' => 'Great Migration'],
        ['image_path' => 'assets/images/game.jpg', 'title' => 'Game Drive', 'description' => 'Safari adventure'],
        ['image_path' => 'assets/images/hotel.webp', 'title' => 'Luxury Lodge', 'description' => 'Safari accommodation'],
        ['image_path' => 'assets/images/mountain.jpg', 'title' => 'Mountains', 'description' => 'Scenic views'],
        ['image_path' => 'assets/images/volcano.jpg', 'title' => 'Volcanoes', 'description' => 'Volcanic landscapes'],
    ];
}
?>

<!-- ================= GALLERY HERO ================= -->
<section class="page-hero gallery-hero">
  <div class="hero-content">
    <h1>Photo <span>Gallery</span></h1>
    <p>Capturing moments from our journeys across Africa</p>
  </div>
</section>

<!-- ================= GALLERY GRID ================= -->
<section class="gallery-section">
  <div class="container">
    <div class="gallery-filters">
      <button class="filter-btn active" data-filter="all">All</button>
      <button class="filter-btn" data-filter="wildlife">Wildlife</button>
      <button class="filter-btn" data-filter="destination">Destination</button>
      <button class="filter-btn" data-filter="safari">Safari</button>
      <button class="filter-btn" data-filter="hotel">Hotel</button>
      <button class="filter-btn" data-filter="culture">Culture</button>
    </div>

    <div class="gallery-grid" id="galleryGrid">
      <?php foreach ($galleryImages as $index => $image): 
        $category = $image['category'] ?? 'general';
        $imagePath = '../' . ($image['image_path'] ?? $image['image_path']);
        $title = $image['title'] ?? 'Gallery Image';
        $description = $image['description'] ?? '';
      ?>
        <div class="gallery-item" data-category="<?php echo htmlspecialchars($category); ?>">
          <div class="gallery-item-inner">
            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($title); ?>" loading="lazy">
            <div class="gallery-overlay">
              <div class="gallery-info">
                <h3><?php echo htmlspecialchars($title); ?></h3>
                <?php if ($description): ?>
                  <p><?php echo htmlspecialchars($description); ?></p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Lightbox Modal -->
<div id="lightboxModal" class="lightbox-modal">
  <span class="lightbox-close">&times;</span>
  <img id="lightboxImage" src="" alt="">
  <div class="lightbox-caption"></div>
</div>

<?php include('../includes/footer.php'); ?>

<script>
// Gallery Filter
document.querySelectorAll('.filter-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    
    const filter = this.getAttribute('data-filter');
    const items = document.querySelectorAll('.gallery-item');
    
    items.forEach(item => {
      if (filter === 'all' || item.getAttribute('data-category') === filter) {
        item.style.display = 'block';
      } else {
        item.style.display = 'none';
      }
    });
  });
});

// Lightbox
const lightboxModal = document.getElementById('lightboxModal');
const lightboxImage = document.getElementById('lightboxImage');
const lightboxCaption = document.querySelector('.lightbox-caption');
const lightboxClose = document.querySelector('.lightbox-close');

document.querySelectorAll('.gallery-item img').forEach(img => {
  img.addEventListener('click', function() {
    lightboxImage.src = this.src;
    const title = this.alt;
    const desc = this.parentElement.querySelector('.gallery-info p')?.textContent || '';
    lightboxCaption.innerHTML = `<h3>${title}</h3><p>${desc}</p>`;
    lightboxModal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  });
});

lightboxClose.addEventListener('click', () => {
  lightboxModal.style.display = 'none';
  document.body.style.overflow = 'auto';
});

lightboxModal.addEventListener('click', (e) => {
  if (e.target === lightboxModal) {
    lightboxModal.style.display = 'none';
    document.body.style.overflow = 'auto';
  }
});
</script>

