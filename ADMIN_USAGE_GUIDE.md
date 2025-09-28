# Bokod CMS - Admin Usage Guide

## Table of Contents
1. [System Overview](#system-overview)
2. [Getting Started](#getting-started)
3. [Dashboard Overview](#dashboard-overview)
4. [Patient Management](#patient-management)
5. [Medicine Management](#medicine-management)
6. [Appointments Management](#appointments-management)
7. [Prescriptions Management](#prescriptions-management)
8. [Messaging System](#messaging-system)
9. [Reports & Analytics](#reports--analytics)
10. [User Management](#user-management)
11. [System Settings](#system-settings)
12. [Troubleshooting](#troubleshooting)

---

## System Overview

**Bokod CMS** is a comprehensive healthcare management system designed for medical facilities. It provides tools for managing patients, medicines, appointments, prescriptions, and communication between staff and patients.

### Key Features
- 📊 **Dashboard** with real-time analytics
- 👥 **Patient Management** with complete medical records
- 💊 **Medicine Inventory** with stock tracking
- 📅 **Appointment Scheduling** with calendar view
- 📝 **Prescription Management** with dispensing tracking
- 💬 **Messaging System** for patient communication
- 📈 **Reports & Analytics** for insights
- ⚙️ **System Settings** and maintenance tools

---

## Getting Started

### Login Process
1. Navigate to your CMS URL (e.g., `http://localhost/cms` or your domain)
2. Click **"Login"** on the landing page
3. Enter your admin credentials
4. You'll be redirected to the main dashboard

### First-Time Setup Checklist
- [ ] Review system settings
- [ ] Set up medicine inventory
- [ ] Configure appointment types
- [ ] Test messaging system
- [ ] Review user permissions

---

## Dashboard Overview

The dashboard is your main control center, providing:

### Real-time Statistics
- **Patient Count**: Total registered patients
- **Today's Appointments**: Scheduled for today
- **Pending Prescriptions**: Awaiting processing
- **Medicine Alerts**: Low stock warnings

### Quick Actions
- **Add New Patient**: Direct link to patient registration
- **Create Appointment**: Quick appointment booking
- **Medicine Management**: Access inventory controls
- **View Messages**: Check patient communications

### Activity Feed
- Recent patient registrations
- Appointment updates
- Medicine stock alerts
- System notifications

---

## Patient Management

### Viewing Patients
1. Navigate to **Patients** → **All Patients**
2. Use search/filter options to find specific patients
3. Click on any patient to view detailed information

### Adding New Patients
1. Click **"Add New Patient"** button
2. Fill required information:
   - **Personal Details**: Name, contact, address
   - **Medical Information**: Medical history, allergies
   - **Emergency Contact**: Contact person details
3. Click **"Save Patient"**

### Patient Profile Features
- **Medical History**: View past appointments and treatments
- **Prescriptions**: Active and historical prescriptions  
- **Appointments**: Upcoming and past appointments
- **Messages**: Communication history
- **Documents**: Uploaded medical documents

### Managing Patient Records
- **Edit Information**: Update patient details anytime
- **Medical Notes**: Add clinical observations
- **Document Upload**: Store medical documents and images
- **Status Management**: Active/Inactive patient status

---

## Medicine Management

### Medicine Inventory Overview
1. Go to **Medicines** → **All Medicines**
2. View inventory with real-time stock levels
3. Stock status indicators:
   - 🟢 **In Stock**: Adequate supply
   - 🟡 **Low Stock**: Below minimum threshold
   - 🔴 **Out of Stock**: Zero quantity

### Adding New Medicines
1. Click **"Add New Medicine"**
2. Complete the form sections:
   - **Basic Information**: Name, category, manufacturer
   - **Dosage & Properties**: Form, strength, instructions
   - **Inventory Details**: Stock levels, supplier info
   - **Medical Information**: Side effects, contraindications
   - **Medicine Image**: Upload product photo

### Stock Management
1. **Update Stock Levels**:
   - Click on any medicine
   - Use **"Update Stock"** button
   - Choose action: Add/Remove/Set quantity
   - Add reason for stock change

2. **Bulk Stock Updates**:
   - Go to **Medicines** → **Stock Management**
   - Select multiple medicines
   - Perform bulk operations

3. **Stock Alerts**:
   - System automatically alerts for low stock
   - Set custom minimum stock levels
   - Export stock reports for analysis

### Medicine Categories
- **Pain Relief**: Analgesics, anti-inflammatories
- **Antibiotics**: Various antibiotic medications
- **Vitamins & Supplements**: Nutritional supplements
- **Cold & Flu**: Respiratory medications
- **Diabetes**: Blood sugar management
- **Heart & Blood Pressure**: Cardiovascular medications

---

## Appointments Management

### Calendar View
1. Navigate to **Appointments** → **Calendar**
2. Switch between Month/Week/Day views
3. Click any date to create new appointment
4. Drag appointments to reschedule

### Creating Appointments
1. Click **"New Appointment"** or click calendar date
2. Fill appointment details:
   - **Patient**: Search and select patient
   - **Date & Time**: Schedule appointment
   - **Type**: Consultation, follow-up, etc.
   - **Notes**: Special instructions
3. Save appointment

### Appointment Status Management
- **Pending**: Awaiting confirmation
- **Approved**: Confirmed appointment
- **Completed**: Appointment finished
- **Cancelled**: Cancelled by patient/admin
- **No-show**: Patient didn't attend

### Appointment Workflow
1. **New Request**: Patient books appointment
2. **Review**: Admin reviews and approves/modifies
3. **Confirmation**: Patient receives confirmation
4. **Completion**: Mark as completed after visit
5. **Follow-up**: Schedule next appointment if needed

---

## Prescriptions Management

### Creating Prescriptions
1. Go to **Prescriptions** → **Create New**
2. Select patient from dropdown
3. Add medicines:
   - Search medicine by name
   - Set dosage and quantity
   - Add instructions
4. Set prescription duration
5. Save prescription

### Prescription Workflow
1. **Created**: New prescription issued
2. **Active**: Available for dispensing
3. **Partially Dispensed**: Some medicines given
4. **Completed**: All medicines dispensed
5. **Expired**: Past expiration date

### Dispensing Medicines
1. Open prescription details
2. Click **"Dispense"** button
3. Select medicines to dispense
4. Update quantities given
5. Record dispensing notes
6. Print dispensing receipt

### Prescription Tracking
- View all active prescriptions
- Track dispensing history
- Monitor expiration dates
- Generate prescription reports

---

## Messaging System

### Accessing Messages
1. Click **Messages** icon in navigation
2. View conversation list on left panel
3. Select conversation to view messages
4. Send replies in the chat panel

### Message Features
- **Real-time Messaging**: Instant communication
- **File Attachments**: Share documents and images
- **Message Status**: Read/Unread indicators
- **Message History**: Complete conversation log
- **Archive Conversations**: Organize old conversations

### Patient Communication Best Practices
- Respond promptly to patient inquiries
- Use professional and clear language
- Share relevant medical documents
- Archive completed conversations
- Monitor unread message count

### Message Types
- **Text Messages**: Standard communication
- **File Attachments**: Documents, images, reports
- **System Messages**: Automated notifications
- **Priority Messages**: Urgent communications

---

## Reports & Analytics

### Available Reports
1. **Patient Reports**:
   - Patient demographics
   - Registration trends
   - Medical history summaries

2. **Appointment Reports**:
   - Appointment statistics
   - No-show rates
   - Popular appointment times

3. **Medicine Reports**:
   - Inventory levels
   - Usage patterns
   - Expiration tracking

4. **Prescription Reports**:
   - Prescription volume
   - Most prescribed medicines
   - Dispensing patterns

### Generating Reports
1. Navigate to **Reports** section
2. Select report type
3. Choose date range and filters
4. Click **"Generate Report"**
5. Export as PDF/Excel if needed

### Key Metrics to Monitor
- **Daily Patient Count**: Track patient visits
- **Medicine Turnover**: Inventory usage rates
- **Appointment Completion**: Success rates
- **Response Times**: Message response efficiency

---

## User Management

### User Types
- **Admin**: Full system access
- **Staff**: Limited administrative access
- **Patient**: Patient portal access

### Managing Admin Users
1. Go to **Users** → **All Users**
2. Click **"Add New User"**
3. Set user details and permissions
4. Assign appropriate role
5. Send login credentials

### User Account Management
- **Account Status**: Active/Inactive/Suspended
- **Password Reset**: Generate new passwords
- **Permission Levels**: Control access rights
- **Login History**: Monitor user activity

### Registration Approvals
1. Navigate to **Registrations** → **Pending**
2. Review patient self-registrations
3. Approve or reject applications
4. Send welcome emails to approved patients

---

## System Settings

### General Settings
1. **System Information**:
   - Clinic name and details
   - Contact information
   - Operating hours

2. **Email Configuration**:
   - SMTP settings
   - Email templates
   - Notification preferences

### System Maintenance
1. **Cache Management**:
   - Clear application cache
   - Clear configuration cache
   - Clear view cache

2. **Database Maintenance**:
   - Optimize database
   - Create backups
   - System health check

3. **File Management**:
   - Clean temporary files
   - Manage uploaded files
   - Storage monitoring

### Backup & Security
- **Automated Backups**: Schedule regular backups
- **Manual Backups**: Create on-demand backups
- **Backup Restoration**: Restore from backup files
- **Security Monitoring**: Track login attempts

---

## Troubleshooting

### Common Issues

#### Patient Login Problems
- **Symptom**: Patient can't log in
- **Solution**: 
  1. Check if account is approved
  2. Reset password if needed
  3. Verify account status is active

#### Medicine Stock Discrepancies
- **Symptom**: Stock levels don't match physical count
- **Solution**:
  1. Use physical count feature
  2. Adjust stock levels accordingly
  3. Check stock movement history

#### Appointment Conflicts
- **Symptom**: Double-booked appointments
- **Solution**:
  1. Check calendar for conflicts
  2. Reschedule one appointment
  3. Notify affected patients

#### Message Delivery Issues
- **Symptom**: Messages not reaching patients
- **Solution**:
  1. Check email configuration
  2. Verify patient email addresses
  3. Test email system

### Performance Optimization
1. **Regular Maintenance**:
   - Clear cache weekly
   - Optimize database monthly
   - Review and archive old data

2. **Monitor Resources**:
   - Check server disk space
   - Monitor database size
   - Review system logs

### Getting Help
- **System Logs**: Check for error messages
- **Email Testing**: Use built-in email test tools
- **Database Status**: Monitor database health
- **Contact Support**: Document issues and contact system administrator

---

## Quick Reference

### Daily Tasks Checklist
- [ ] Check dashboard for alerts
- [ ] Review new patient registrations
- [ ] Process pending appointments
- [ ] Monitor medicine stock levels
- [ ] Respond to patient messages
- [ ] Review critical reports

### Weekly Tasks
- [ ] Generate weekly reports
- [ ] Update medicine inventory
- [ ] Review system performance
- [ ] Process user account requests
- [ ] Clear system cache

### Monthly Tasks
- [ ] Generate monthly analytics
- [ ] Review and archive old data
- [ ] Update system settings
- [ ] Perform database maintenance
- [ ] Create system backup

### Keyboard Shortcuts
- **Ctrl + /** : Global search
- **Ctrl + N** : New record (context dependent)
- **Ctrl + S** : Save current form
- **Esc** : Close modal/cancel action

---

## Support & Contact

For technical issues or questions about using the Bokod CMS:

1. **Check System Health**: Settings → System Health Check
2. **Review Logs**: Monitor system logs for errors
3. **Documentation**: Refer to this guide and inline help
4. **Administrator Contact**: Contact your system administrator

---

*Last Updated: September 28, 2025*
*Version: 1.0*