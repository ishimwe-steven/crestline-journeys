# Quick EmailJS Template Setup

## Fast Setup Steps

### 1. Create Customer Thank You Template

**In EmailJS Dashboard:**
- Template Name: `customer_enquiry`
- To Email: `{{to_email}}`
- From Name: `Crestline Journeys`
- Subject: `Thank you for your enquiry - Crestline Journeys`

**Copy the HTML from:** `customer-thankyou-template.html`

**Template Variables Needed:**
- `{{to_name}}` - Customer first name
- `{{to_email}}` - Customer email
- `{{destination}}` - Destination preference
- `{{countries}}` - Selected countries
- `{{travel_with}}` - Traveling with
- `{{budget}}` - Budget range
- `{{enquiry_id}}` - Enquiry reference number

### 2. Create Admin Notification Template

**In EmailJS Dashboard:**
- Template Name: `admin_enquiry`
- To Email: `your-admin@email.com` (your actual email)
- From Name: `Crestline Journeys Website`
- Subject: `New Travel Enquiry #{{enquiry_id}} - {{customer_name}}`

**Copy the HTML from:** `admin-enquiry-template.html`

**Template Variables Needed:**
- `{{enquiry_id}}` - Enquiry ID
- `{{customer_name}}` - Full name
- `{{customer_email}}` - Email
- `{{customer_phone}}` - Phone
- `{{preferred_contact}}` - Contact method
- `{{destination_known}}` - Destination known
- `{{countries}}` - Countries
- `{{region}}` - Region
- `{{travel_time_choice}}` - Travel timing
- `{{travel_dates}}` - Date range
- `{{travel_with}}` - Traveling with
- `{{budget}}` - Budget
- `{{trip_details}}` - Additional details (optional)
- `{{referred}}` - Referred status
- `{{submitted_date}}` - Submission date/time

### 3. Get Your IDs

After creating templates:
1. Copy the **Template ID** from each template
2. Copy your **Service ID** from Email Services
3. Copy your **Public Key** from Account → API Keys

### 4. Update Config File

Edit `assets/js/emailjs-config.js`:

```javascript
const EMAILJS_CONFIG = {
  publicKey: 'your-public-key-here',
  serviceId: 'your-service-id-here',
  customerTemplateId: 'your-customer-template-id',
  adminTemplateId: 'your-admin-template-id'
};
```

### 5. Test!

1. Submit a test enquiry on your website
2. Check both email inboxes
3. Verify all information displays correctly

## That's It! 🎉

Your email system is now ready to send professional, branded emails automatically.

