// ================= MOBILE NAV =================
document.querySelector('.mobile-nav-toggle')?.addEventListener('click', () => {
  document.querySelector('nav').classList.add('show');
  document.querySelector('.close-menu').classList.add('show');
  document.body.style.overflow = 'hidden';
});

document.querySelector('.close-menu')?.addEventListener('click', () => {
  document.querySelector('nav').classList.remove('show');
  document.querySelector('.close-menu').classList.remove('show');
  document.body.style.overflow = 'auto';
});

document.querySelectorAll('#nav-menu a').forEach(link => {
  link.addEventListener('click', () => {
    if (window.innerWidth <= 991) {
      document.querySelector('nav').classList.remove('show');
      document.querySelector('.close-menu').classList.remove('show');
      document.body.style.overflow = 'auto';
    }
  });
});

// ================= SLIDESHOW =================
let slideIndex = 0;
showSlides();

function showSlides() {
  const slides = document.getElementsByClassName("destination-slide");
  for (let i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  slideIndex++;
  if (slideIndex > slides.length) { slideIndex = 1 }
  if (slides.length > 0) slides[slideIndex - 1].style.display = "block";
  setTimeout(showSlides, 6000); // Change every 6 seconds
}

// ================= MODAL LOGIC =================
const modal = document.getElementById("enquiryModal");
const openBtn = document.getElementById("openEnquiryModal");
const closeBtn = document.querySelector(".close");
const steps = document.querySelectorAll(".step");
let currentStep = 0;

if (openBtn && modal) {
  openBtn.onclick = () => modal.style.display = "flex";
}
if (closeBtn) {
  closeBtn.onclick = () => modal.style.display = "none";
}
window.onclick = e => {
  if (e.target === modal) modal.style.display = "none";
};

// ================= STEP NAVIGATION =================
document.querySelectorAll(".next").forEach(btn => {
  btn.addEventListener("click", () => {
    // Validate current step before proceeding
    const currentStepElement = steps[currentStep];
    const requiredInputs = currentStepElement.querySelectorAll("input[required], select[required]");
    let isValid = true;

    requiredInputs.forEach(input => {
      if (input.type === "radio" || input.type === "checkbox") {
        const groupName = input.name;
        const groupInputs = currentStepElement.querySelectorAll(`input[name="${groupName}"]`);
        const isGroupValid = Array.from(groupInputs).some(inp => inp.checked);
        if (!isGroupValid) isValid = false;
      } else if (!input.value.trim()) {
        isValid = false;
      }
    });

    if (!isValid) {
      alert("Please fill in all required fields");
      return;
    }

    steps[currentStep].classList.remove("active");
    updateStepIndicator(currentStep, false);
    currentStep++;
    if (currentStep < steps.length) {
      steps[currentStep].classList.add("active");
      updateStepIndicator(currentStep, true);
    }
  });
});

// Update step indicator
function updateStepIndicator(stepIndex, isActive) {
  const stepDots = document.querySelectorAll(".step-dot");
  if (stepDots[stepIndex]) {
    if (isActive) {
      stepDots[stepIndex].classList.add("active");
    } else {
      stepDots[stepIndex].classList.remove("active");
    }
  }
}

// Initialize step indicator on page load
if (steps.length > 0) {
  updateStepIndicator(0, true);
}

// Date range handling
const startDateInput = document.getElementById('start-date');
const endDateInput = document.getElementById('end-date');
const travelDatesCombined = document.getElementById('travel_dates_combined');

function updateDateRange() {
  if (startDateInput && endDateInput && travelDatesCombined) {
    const startDate = startDateInput.value;
    const endDate = endDateInput.value;
    
    if (startDate && endDate) {
      // Combine dates into single string
      travelDatesCombined.value = `${startDate} to ${endDate}`;
    } else if (startDate) {
      travelDatesCombined.value = startDate;
    } else if (endDate) {
      travelDatesCombined.value = endDate;
    }
  }
}

// Add event listeners for date inputs
if (startDateInput) {
  startDateInput.addEventListener('change', updateDateRange);
  // Set minimum date to today
  startDateInput.min = new Date().toISOString().split('T')[0];
}

if (endDateInput) {
  endDateInput.addEventListener('change', updateDateRange);
  // Set minimum date based on start date
  if (startDateInput) {
    startDateInput.addEventListener('change', function() {
      if (this.value) {
        endDateInput.min = this.value;
      }
    });
  }
  endDateInput.min = new Date().toISOString().split('T')[0];
}

// ================= FORM SUBMISSION =================
const enquiryForm = document.getElementById("enquiryForm");
if (enquiryForm) {
  // Initialize EmailJS
  if (typeof emailjs !== 'undefined' && EMAILJS_CONFIG.publicKey !== 'YOUR_PUBLIC_KEY_HERE') {
    emailjs.init(EMAILJS_CONFIG.publicKey);
  }

  enquiryForm.addEventListener("submit", e => {
    e.preventDefault();

    const formData = new FormData(enquiryForm);
    const submitBtn = enquiryForm.querySelector('.btn-enquiry.submit');
    
    // Disable submit button
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = 'Submitting...';
    }

    // ✅ Use correct path based on current location
    const basePath = window.location.pathname.includes('/pages/') ? '../' : './';
    const saveUrl = basePath + "save_enquiry.php";

    // First, save to database
    fetch(saveUrl, {
      method: "POST",
      body: formData
    })
      .then(async res => {
        const text = await res.text();
        console.log("Raw response:", text);
        try {
          return JSON.parse(text);
        } catch {
          return { status: "error", message: "Invalid JSON: " + text };
        }
      })
      .then(data => {
        console.log("Database save response:", data);

        if (data.status === "success") {
          // Extract form data for emails
          const formObj = Object.fromEntries(formData);
          
          // Send emails via EmailJS (if configured)
          if (typeof emailjs !== 'undefined' && EMAILJS_CONFIG.publicKey !== 'YOUR_PUBLIC_KEY_HERE') {
            sendEnquiryEmails(formObj, data.insert_id || '');
          } else {
            // If EmailJS not configured, just show success
            showEnquirySuccess();
          }
        } else {
          throw new Error(data.message || 'Failed to save enquiry');
        }
      })
      .catch(err => {
        console.error("❌ Error:", err);
        alert("❌ Error: " + err.message);
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Submit Enquiry';
        }
      });
  });
}

// Function to send emails via EmailJS
function sendEnquiryEmails(formData, enquiryId) {
  // Handle countries for customer email too
  let customerCountriesStr = 'Not specified';
  if (formData.countries) {
    if (Array.isArray(formData.countries)) {
      customerCountriesStr = formData.countries.join(', ');
    } else if (typeof formData.countries === 'string') {
      customerCountriesStr = formData.countries;
    }
  }

  const customerEmailData = {
    to_name: formData.first_name || 'Guest',
    to_email: formData.email,
    destination: formData.know_where || 'Anywhere in Africa',
    countries: customerCountriesStr,
    travel_with: formData.travel_with || 'Not specified',
    budget: formData.budget || 'Not specified',
    enquiry_id: enquiryId
  };

  // Handle countries - might be array or string
  let countriesStr = 'Not specified';
  if (formData.countries) {
    if (Array.isArray(formData.countries)) {
      countriesStr = formData.countries.join(', ');
    } else if (typeof formData.countries === 'string') {
      countriesStr = formData.countries;
    }
  }

  const adminEmailData = {
    enquiry_id: enquiryId,
    customer_name: `${formData.first_name || ''} ${formData.last_name || ''}`.trim(),
    customer_email: formData.email,
    customer_phone: formData.phone || 'Not provided',
    preferred_contact: formData.preferred_contact || 'Email',
    destination_known: formData.know_where || '',
    countries: countriesStr,
    region: formData.region || 'Not specified',
    travel_time_choice: formData.travel_time_choice || '',
    travel_dates: formData.travel_dates || 'Not specified',
    travel_with: formData.travel_with || '',
    budget: formData.budget || '',
    trip_details: formData.trip_details || '',
    referred: formData.referred || 'No',
    submitted_date: new Date().toLocaleString()
  };

  // Send customer thank you email
  emailjs.send(
    EMAILJS_CONFIG.serviceId,
    EMAILJS_CONFIG.customerTemplateId,
    customerEmailData
  )
    .then(() => {
      console.log('Customer email sent successfully');
    })
    .catch(err => {
      console.error('Failed to send customer email:', err);
    });

  // Send admin notification email
  emailjs.send(
    EMAILJS_CONFIG.serviceId,
    EMAILJS_CONFIG.adminTemplateId,
    adminEmailData
  )
    .then(() => {
      console.log('Admin email sent successfully');
      showEnquirySuccess();
    })
    .catch(err => {
      console.error('Failed to send admin email:', err);
      // Still show success if database saved, even if email fails
      showEnquirySuccess();
    });
}

// Function to show success and reset form
function showEnquirySuccess() {
  const enquiryForm = document.getElementById("enquiryForm");
  const modal = document.getElementById("enquiryModal");
  const submitBtn = enquiryForm?.querySelector('.btn-enquiry.submit');
  
  alert("✅ Your enquiry was sent successfully! We'll be in touch soon.");
  
  if (modal) modal.style.display = "none";
  if (enquiryForm) {
    enquiryForm.reset();
    currentStep = 0;
    steps.forEach(s => s.classList.remove("active"));
    if (steps[0]) steps[0].classList.add("active");
    // Reset step indicators
    document.querySelectorAll(".step-dot").forEach((dot, idx) => {
      if (idx === 0) {
        dot.classList.add("active");
      } else {
        dot.classList.remove("active");
      }
    });
  }
  
  if (submitBtn) {
    submitBtn.disabled = false;
    submitBtn.textContent = 'Submit Enquiry';
  }
}
