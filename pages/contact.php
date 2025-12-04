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
    <!-- Enquiry Form (Visible on Page) -->
    <div class="enquiry-form-container">
      <div class="enquiry-header">
        <h2>Let's Plan Your Journey</h2>
        <p class="enquiry-subtitle">Tell us about your dream adventure and we'll create a bespoke itinerary just for you</p>
      </div>

      <div class="step-indicator">
        <div class="step-dot active" data-step="1"></div>
        <div class="step-dot" data-step="2"></div>
        <div class="step-dot" data-step="3"></div>
        <div class="step-dot" data-step="4"></div>
        <div class="step-dot" data-step="5"></div>
        <div class="step-dot" data-step="6"></div>
        <div class="step-dot" data-step="7"></div>
        <div class="step-dot" data-step="8"></div>
        <div class="step-dot" data-step="9"></div>
      </div>

      <form id="enquiryForm" class="enquiry-form-page">
        <!-- STEP 1 -->
        <div class="step active" data-step="1">
          <h3>Do you know where you would like to travel?</h3>
          <div class="form-options">
            <label class="option-card">
              <input type="radio" name="know_where" value="Yes" required>
              <span class="option-label">Yes</span>
            </label>
            <label class="option-card">
              <input type="radio" name="know_where" value="Anywhere in Africa" required>
              <span class="option-label">Anywhere in Africa</span>
            </label>
          </div>
          <button type="button" class="btn-enquiry next">Next</button>
        </div>

        <!-- STEP 2 -->
        <div class="step" data-step="2">
          <h3>Where would you like to travel?</h3>
          <div class="countries-grid">
            <label class="country-card">
              <input type="checkbox" name="countries[]" value="Botswana">
              <span>Tanzania</span>
            </label>
            <label class="country-card">
              <input type="checkbox" name="countries[]" value="Rwanda">
              <span>Rwanda</span>
            </label>
            <label class="country-card">
              <input type="checkbox" name="countries[]" value="Zimbabwe">
              <span>Uganda</span>
            </label>
            <label class="country-card">
              <input type="checkbox" name="countries[]" value="Other">
              <span>Other</span>
            </label>
            <label class="country-card">
              <input type="checkbox" name="countries[]" value="I haven't decided yet">
              <span>I haven't decided yet</span>
            </label>
          </div>
          <button type="button" class="btn-enquiry next">Next</button>
        </div>

        <!-- STEP 3 -->
        <div class="step" data-step="3">
          <h3>Do you have a specific region or camp in mind?</h3>
          <input type="text" name="region" placeholder="Region or camp (optional)" class="form-input">
          <label class="option-card">
            <input type="radio" name="region_choice" value="I haven't decided yet">
            <span class="option-label">I haven't decided yet</span>
          </label>
          <button type="button" class="btn-enquiry next">Next</button>
        </div>

        <!-- STEP 4 -->
        <div class="step" data-step="4">
          <h3>When would you like to travel?</h3>
          <div class="form-options">
            <label class="option-card">
              <input type="radio" name="travel_time_choice" value="I Know Exactly when" id="exact-dates" required>
              <span class="option-label">I Know Exactly when</span>
            </label>
            <label class="option-card">
              <input type="radio" name="travel_time_choice" value="I have a rough Idea" id="rough-idea" required>
              <span class="option-label">I have a rough Idea</span>
            </label>
            <label class="option-card">
              <input type="radio" name="travel_time_choice" value="Tell me when is best" required>
              <span class="option-label">Tell me when is best</span>
            </label>
          </div>
          <button type="button" class="btn-enquiry next">Next</button>
        </div>

        <!-- STEP 5 -->
        <div class="step" data-step="5">
          <h3>Select your travel date range</h3>
          <p class="step-description">Choose your preferred travel period</p>
          <div class="date-range-container">
            <div class="date-input-group">
              <label class="form-label">Start Date</label>
              <input type="date" name="travel_dates_start" class="form-input" id="start-date">
            </div>
            <div class="date-input-group">
              <label class="form-label">End Date</label>
              <input type="date" name="travel_dates_end" class="form-input" id="end-date">
            </div>
          </div>
          <input type="hidden" name="travel_dates" id="travel_dates_combined">
          <button type="button" class="btn-enquiry next">Next</button>
        </div>

        <!-- STEP 6 -->
        <div class="step" data-step="6">
          <h3>Who are you travelling with?</h3>
          <div class="form-options">
            <label class="option-card">
              <input type="radio" name="travel_with" value="Couple" required>
              <span class="option-label">Couple</span>
            </label>
            <label class="option-card">
              <input type="radio" name="travel_with" value="Solo" required>
              <span class="option-label">Solo</span>
            </label>
            <label class="option-card">
              <input type="radio" name="travel_with" value="Family" required>
              <span class="option-label">Family</span>
            </label>
            <label class="option-card">
              <input type="radio" name="travel_with" value="Friends" required>
              <span class="option-label">Friends</span>
            </label>
          </div>
          <button type="button" class="btn-enquiry next">Next</button>
        </div>

        <!-- STEP 7 -->
        <div class="step" data-step="7">
          <h3>What is your travel budget per person?</h3>
          <div class="form-options">
            <label class="option-card">
              <input type="radio" name="budget" value="USD 7.5k - 10k" required>
              <span class="option-label">USD 7.5k - 10k</span>
            </label>
            <label class="option-card">
              <input type="radio" name="budget" value="USD 10k - 20k" required>
              <span class="option-label">USD 10k - 20k</span>
            </label>
            <label class="option-card">
              <input type="radio" name="budget" value="USD 20k - 40k" required>
              <span class="option-label">USD 20k - 40k</span>
            </label>
            <label class="option-card">
              <input type="radio" name="budget" value="USD 40k+" required>
              <span class="option-label">USD 40k+</span>
            </label>
          </div>
          <button type="button" class="btn-enquiry next">Next</button>
        </div>

        <!-- STEP 8 -->
        <div class="step" data-step="8">
          <h3>Tell us more about your trip (optional)</h3>
          <textarea name="trip_details" rows="5" placeholder="Are you traveling for a specific purpose? Anything specific you'd like to experience?" class="form-textarea"></textarea>
          <label class="form-label">Have you been referred?</label>
          <div class="form-options">
            <label class="option-card">
              <input type="radio" name="referred" value="Yes">
              <span class="option-label">Yes</span>
            </label>
            <label class="option-card">
              <input type="radio" name="referred" value="No">
              <span class="option-label">No</span>
            </label>
          </div>
          <button type="button" class="btn-enquiry next">Next</button>
        </div>

        <!-- STEP 9 -->
        <div class="step" data-step="9">
          <h3>Where can we send your trip suggestions?</h3>
          <p class="step-description">Our expert Travel Designers will contact you to design your bespoke journey.</p>
          <input type="text" name="first_name" placeholder="First name" class="form-input" required>
          <input type="text" name="last_name" placeholder="Surname" class="form-input" required>
          <input type="email" name="email" placeholder="Email address" class="form-input" required>
          <input type="tel" name="phone" placeholder="Phone number" class="form-input" required>
          <label class="form-label">Preferred method of contact:</label>
          <select name="preferred_contact" class="form-select" required>
            <option value="">Select...</option>
            <option value="Email">Email</option>
            <option value="Phone">Phone</option>
            <option value="WhatsApp">WhatsApp</option>
          </select>
          <button type="submit" class="btn-enquiry submit">Submit Enquiry</button>
        </div>
      </form>
    </div>

    <div class="contact-wrapper" style="margin-top: 60px;">
      <!-- Contact Information -->
      <div class="contact-info-wrapper" style="width: 100%;">
        <h2>Contact Information</h2>
        <div class="contact-info">
          <div class="info-item">
            <div class="info-icon"><i class="fas fa-route"></i></div>
            <div>
              <h3>Location</h3>
              <p>Kigali, Rwanda</p>
            </div>
          </div>
          
          <div class="info-item">
            <div class="info-icon"><i class="fas fa-envelope"></i></div>
            <div>
              <h3>Email</h3>
              <p><a href="mailto:info@crestlinejourneys.com">info@crestjourneys.com</a></p>
            </div>
          </div>
          
          <div class="info-item">
            <div class="info-icon"><i class="fas fa-phone"></i></div>
            <div>
              <h3>Phone</h3>
              <p><a href="tel:+250 782 793 718">+250 782 793 718</a></p>
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
  </div>
</section>

<?php include('../includes/footer.php'); ?>

