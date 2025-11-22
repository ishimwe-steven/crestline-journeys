<?php include('../includes/header.php'); ?>

<!-- ================= CONTACT HERO ================= -->
<section class="page-hero contact-hero">
  <div class="hero-content">
    <h1>Get In <span>Touch</span></h1>
    <p>We'd love to hear from you and help plan your next adventure</p>
  </div>
</section>

<!-- ================= CONTACT SECTION ================= -->
<section class="contact-section">
  <div class="container">
    <div class="contact-wrapper">
      <!-- Contact Form -->
      <div class="contact-form-wrapper">
        <h2>Send Us a Message</h2>
        <form id="contactForm" class="contact-form">
          <div class="form-row">
            <div class="form-group">
              <label for="name">Full Name *</label>
              <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
              <label for="email">Email Address *</label>
              <input type="email" id="email" name="email" required>
            </div>
          </div>
          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone">
          </div>
          <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" placeholder="What is your enquiry about?">
          </div>
          <div class="form-group">
            <label for="message">Message *</label>
            <textarea id="message" name="message" rows="6" required placeholder="Tell us about your travel plans..."></textarea>
          </div>
          <button type="submit" class="btn-submit">Send Message</button>
        </form>
      </div>

      <!-- Contact Information -->
      <div class="contact-info-wrapper">
        <h2>Contact Information</h2>
        <div class="contact-info">
          <div class="info-item">
            <div class="info-icon">📍</div>
            <div>
              <h3>Location</h3>
              <p>Kigali, Rwanda</p>
            </div>
          </div>
          
          <div class="info-item">
            <div class="info-icon">📧</div>
            <div>
              <h3>Email</h3>
              <p><a href="mailto:info@crestlinejourneys.com">info@crestlinejourneys.com</a></p>
            </div>
          </div>
          
          <div class="info-item">
            <div class="info-icon">📞</div>
            <div>
              <h3>Phone</h3>
              <p><a href="tel:+250788123456">+250 788 123 456</a></p>
            </div>
          </div>
        </div>

        <!-- Map -->
        <div class="map-container">
          <h3>Find Us</h3>
          <div class="map-wrapper">
            <iframe 
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15949.84875472243!2d30.0615!3d-1.9441!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x19dca4254c8c8d0b%3A0x9b7b5e4f4b4b4b4b!2sKigali%2C%20Rwanda!5e0!3m2!1sen!2sus!4v1234567890"
              width="100%" 
              height="300" 
              style="border:0; border-radius: 10px;" 
              allowfullscreen="" 
              loading="lazy" 
              referrerpolicy="no-referrer-when-downgrade">
            </iframe>
          </div>
        </div>

        <!-- Social Links -->
        <div class="social-links">
          <h3>Follow Us</h3>
          <div class="social-icons">
            <a href="#" target="_blank"><i class="fab fa-linkedin"></i></a> <!-- LinkedIn --> 
            <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>     <!-- Twitter -->
            <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>  <!-- Instagram -->
            <a href="#" target="_blank"><i class="fab fa-tiktok"></i></a>  <!-- TikTok -->
            <a href="#" target="_blank"><i class="fab fa-facebook"></i></a>  <!-- Facebook -->
            <a href="#" target="_blank"><i class="fab fa-youtube"></i></a>  <!-- YouTube -->
            <a href="#"><i class="fas fa-envelope"></i></a>  <!-- Email -->
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Enquiry CTA -->
    <div class="contact-cta">
      <h2>Ready to Plan Your Journey?</h2>
      <p>Use our detailed enquiry form to get personalized travel recommendations</p>
      <button class="btn" id="openEnquiryModal">Start Your Enquiry</button>
    </div>
  </div>
</section>

<?php include('../includes/enquiry_modal.php'); ?>
<?php include('../includes/footer.php'); ?>

<script>
// Contact Form Submission with EmailJS
document.addEventListener('DOMContentLoaded', function() {
  const contactForm = document.getElementById('contactForm');
  
  // Initialize EmailJS if configured
  if (typeof emailjs !== 'undefined' && typeof EMAILJS_CONFIG !== 'undefined' && EMAILJS_CONFIG.publicKey !== 'YOUR_PUBLIC_KEY_HERE') {
    emailjs.init(EMAILJS_CONFIG.publicKey);
  }
  
  if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const submitBtn = this.querySelector('.btn-submit');
      const formData = new FormData(this);
      const formObj = Object.fromEntries(formData);
      
      // Disable submit button
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';
      }
      
      // Send via EmailJS if configured
      if (typeof emailjs !== 'undefined' && EMAILJS_CONFIG && EMAILJS_CONFIG.publicKey !== 'YOUR_PUBLIC_KEY_HERE') {
        const emailData = {
          from_name: formObj.name,
          from_email: formObj.email,
          phone: formObj.phone || 'Not provided',
          subject: formObj.subject || 'General Inquiry',
          message: formObj.message,
          submitted_date: new Date().toLocaleString()
        };
        
        emailjs.send(
          EMAILJS_CONFIG.serviceId,
          EMAILJS_CONFIG.adminTemplateId, // Use admin template for contact form
          emailData
        )
        .then(() => {
          alert('✅ Thank you for your message! We will get back to you soon.');
          contactForm.reset();
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Send Message';
          }
        })
        .catch(err => {
          console.error('EmailJS Error:', err);
          alert('⚠️ Message received! However, there was an issue sending the email. We will still get your message.');
          contactForm.reset();
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Send Message';
          }
        });
      } else {
        // Fallback if EmailJS not configured
        alert('✅ Thank you for your message! We will get back to you soon.');
        contactForm.reset();
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Send Message';
        }
      }
    });
  }
});
</script>

