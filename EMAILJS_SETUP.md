# EmailJS Setup Guide

This guide will help you configure EmailJS for sending emails from your Crestline Journeys website.

## Why EmailJS?

- ✅ **No server configuration needed** - Works from any hosting provider
- ✅ **Free tier available** - 200 emails/month free
- ✅ **Easy to set up** - No SMTP credentials needed
- ✅ **Reliable** - Works with Gmail, Outlook, and other email services
- ✅ **Client-side** - Handles email sending from the browser

## Step-by-Step Setup

### 1. Create EmailJS Account

1. Go to [https://www.emailjs.com/](https://www.emailjs.com/)
2. Click "Sign Up" (you can use Google, GitHub, or email)
3. Verify your email address

### 2. Add Email Service

1. Go to **Email Services** in the dashboard
2. Click **Add New Service**
3. Choose your email provider (Gmail recommended)
4. Follow the connection instructions:
   - **For Gmail**: Click "Connect Account" and authorize EmailJS
   - **For other providers**: Enter SMTP credentials if needed
5. Note your **Service ID** (e.g., `service_abc123`)

### 3. Create Email Templates

#### Template 1: Customer Thank You Email

1. Go to **Email Templates** → **Create New Template**
2. Name it: `customer_enquiry`
3. Set **To Email**: `{{to_email}}`
4. Set **From Name**: `Crestline Journeys`
5. Set **Subject**: `Thank you for your enquiry - Crestline Journeys`
6. Template content:
```
Dear {{to_name}},

Thank you for your enquiry! We're excited to help you plan your journey through Africa.

Our expert Travel Designers have received your request and will contact you within 24-48 hours to discuss your dream adventure.

Your Enquiry Details:
- Destination: {{destination}}
- Countries: {{countries}}
- Travel With: {{travel_with}}
- Budget: {{budget}}

If you have any urgent questions, please don't hesitate to contact us.

Best regards,
The Crestline Journeys Team

Enquiry ID: #{{enquiry_id}}
```
7. Note the **Template ID** (e.g., `template_xyz789`)

#### Template 2: Admin Notification Email

1. Create another template named: `admin_enquiry`
2. Set **To Email**: `your-admin@email.com` (your email address)
3. Set **From Name**: `Crestline Journeys Website`
4. Set **Subject**: `New Enquiry Received - {{customer_name}}`
5. Template content:
```
New Travel Enquiry Received

Enquiry ID: #{{enquiry_id}}

Contact Information:
Name: {{customer_name}}
Email: {{customer_email}}
Phone: {{customer_phone}}
Preferred Contact: {{preferred_contact}}

Travel Preferences:
Destination Known: {{destination_known}}
Countries: {{countries}}
Region: {{region}}
Travel Time Choice: {{travel_time_choice}}
Travel Dates: {{travel_dates}}
Traveling With: {{travel_with}}
Budget: {{budget}}
Referred: {{referred}}

Trip Details:
{{trip_details}}

Submitted: {{submitted_date}}

---
Reply directly to this email or contact: {{customer_email}}
```
6. Note the **Template ID**

### 4. Get API Key

1. Go to **Account** → **General**
2. Under **API Keys**, copy your **Public Key** (e.g., `abc123def456`)

### 5. Configure Your Website

1. Open `assets/js/emailjs-config.js`
2. Replace the placeholder values:

```javascript
const EMAILJS_CONFIG = {
  publicKey: 'abc123def456', // Your Public Key from step 4
  serviceId: 'service_abc123', // Your Service ID from step 2
  customerTemplateId: 'template_xyz789', // Customer template ID from step 3
  adminTemplateId: 'template_admin123' // Admin template ID from step 3
};
```

### 6. Test Your Setup

1. Fill out the enquiry form on your website
2. Submit it
3. Check both your admin email and customer email
4. Check the browser console (F12) for any errors

## Troubleshooting

### Emails not sending?

1. **Check browser console** (F12) for error messages
2. **Verify all IDs are correct** in `emailjs-config.js`
3. **Check EmailJS dashboard** → Email Logs to see delivery status
4. **Verify email service** is connected properly in EmailJS dashboard
5. **Check domain restrictions** - Make sure your domain is allowed in EmailJS settings

### "Invalid Public Key" error?

- Make sure you copied the **Public Key**, not the Private Key
- Public Key starts with letters/numbers (e.g., `abc123...`)

### "Template not found" error?

- Double-check template IDs match exactly
- Make sure templates are published (not in draft mode)

## EmailJS Limits

**Free Plan:**
- 200 emails/month
- Single email service
- Standard support

**Paid Plans:**
- Start at $15/month for 1,000 emails
- Multiple services
- Priority support

## Alternative: Use PHP Mail Instead

If you prefer to use PHP's mail() function or SMTP, you can:
1. Keep using `save_enquiry.php` as is
2. Configure PHP mail settings on your server
3. Or use PHPMailer library for SMTP

The current setup saves to database first, then sends emails via EmailJS, so if EmailJS fails, the enquiry is still saved.

## Support

- EmailJS Documentation: https://www.emailjs.com/docs/
- EmailJS Support: support@emailjs.com

