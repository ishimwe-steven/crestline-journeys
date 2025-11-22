# EmailJS Templates Setup Guide

This folder contains the HTML templates for EmailJS emails.

## Admin Enquiry Template Setup

### Step 1: Copy the Template

1. Open `admin-enquiry-template.html`
2. Copy **ALL** the HTML content (from `<!DOCTYPE html>` to `</html>`)

### Step 2: Create Template in EmailJS

1. Go to your EmailJS dashboard: https://dashboard.emailjs.com/admin/template
2. Click **"Create New Template"**
3. Give it a name: `admin_enquiry` or `Admin Enquiry Notification`

### Step 3: Configure the Template

1. **To Email**: Enter your admin email address (e.g., `stevenishimwe28@gmail.com`)
2. **From Name**: `Crestline Journeys Website`
3. **From Email**: Your email address or the service email
4. **Subject**: `New Travel Enquiry #{{enquiry_id}} - {{customer_name}}`

### Step 4: Paste the HTML

1. Click on the template editor
2. Switch to **HTML mode** (if not already)
3. **Delete any default content**
4. Paste the HTML from `admin-enquiry-template.html`
5. Click **Save**

### Step 5: Map the Variables

The template uses these variables (EmailJS will auto-detect them):

- `{{enquiry_id}}` - Enquiry ID number
- `{{customer_name}}` - Customer's full name
- `{{customer_email}}` - Customer's email address
- `{{customer_phone}}` - Customer's phone number
- `{{preferred_contact}}` - Preferred contact method
- `{{destination_known}}` - Whether customer knows destination
- `{{countries}}` - Selected countries
- `{{region}}` - Region or camp preference
- `{{travel_time_choice}}` - Travel timing preference
- `{{travel_dates}}` - Travel date range
- `{{travel_with}}` - Who they're traveling with
- `{{budget}}` - Budget range
- `{{trip_details}}` - Additional trip details (optional)
- `{{referred}}` - Whether they were referred
- `{{submitted_date}}` - Date and time submitted

**Note**: EmailJS will automatically detect these variables. Just make sure they match the variable names in `assets/js/main.js`.

### Step 6: Test the Template

1. Click **"Test"** button in EmailJS dashboard
2. Fill in test values for the variables
3. Send a test email
4. Check your inbox to verify formatting

### Step 7: Get Template ID

1. After saving, note the **Template ID** (looks like `template_abc123`)
2. Update `assets/js/emailjs-config.js`:
   ```javascript
   adminTemplateId: 'template_abc123'
   ```

## Template Features

✅ **Professional Design** - Matches your brand colors (green #0c2d1a and gold #CDA434)
✅ **Responsive** - Looks great on mobile and desktop
✅ **Complete Information** - Shows all enquiry details organized by section
✅ **Action Buttons** - Quick links to reply or call customer
✅ **Clean Layout** - Easy to read and scan quickly

## Troubleshooting

### Variables not showing?
- Make sure variable names match exactly (case-sensitive)
- Use double curly braces: `{{variable_name}}`
- Check that variables are being sent from `main.js`

### Email looks broken?
- Make sure you pasted ALL the HTML (including styles in `<style>` tag)
- Test in different email clients (Gmail, Outlook, etc.)
- Some email clients strip certain CSS - that's normal

### Missing information?
- Check `assets/js/main.js` to see what variables are being sent
- Add any missing variables to the adminEmailData object

## Template Customization

You can customize:
- Colors: Change `#0c2d1a` (green) and `#CDA434` (gold) to your brand colors
- Fonts: Update font-family in the `<style>` section
- Layout: Modify the HTML structure as needed
- Add logo: Insert an `<img>` tag in the header section

## Alternative: Plain Text Version

If HTML emails don't work well, you can create a plain text version:

1. In EmailJS, create a new template
2. Use this simple format:
```
New Travel Enquiry #{{enquiry_id}}

Contact Information:
Name: {{customer_name}}
Email: {{customer_email}}
Phone: {{customer_phone}}
Preferred Contact: {{preferred_contact}}

Travel Preferences:
Destination: {{destination_known}}
Countries: {{countries}}
Travel Dates: {{travel_dates}}
Budget: {{budget}}
...

Reply to: {{customer_email}}
```

