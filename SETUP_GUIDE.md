# EduGrants Scholarship System - Setup Guide

## Complete Setup Steps

Follow these steps in order to set up the complete system:

### Step 1: Run Database Setup
Visit the following URL in your browser to create all necessary database tables:
```
http://localhost/SCHOLARSHIP/fullcode/setup_database.php
```

You should see: ✅ Database setup completed successfully!

### Step 2: Create Default Admin Account
Visit the following URL to create a default admin account:
```
http://localhost/SCHOLARSHIP/fullcode/setup_admin.php
```

**Default Admin Credentials:**
- Username: `admin`
- Password: `admin123`

⚠️ **IMPORTANT:** Change this password immediately after first login!

---

## System Architecture

### User Types
1. **Students** - Register, apply for scholarships, view dashboard
2. **Admins** - Manage applications, approve/reject, manage schedules

### Database Tables

**For Students:**
- `users` - Registered student accounts (login credentials)
- `applicants` - Scholarship applications (one per user)
- `personal_information` - Student personal details
- `residency_information` - Student residence details
- `family_background` - Student family info
- `system_accounts` - Legacy approved accounts (still populated for compatibility)

**For Admins:**
- `admins` - Admin user accounts (login credentials)
- `schedules` - Appointment date/time slots
- `approved_credentials` - Track approvals with temporary passwords

### Database Relationships
```
admins (admins table)
  │
  └─→ manages → applicants
                  │
                  └─→ references → users (via applicant_id)
                  └─→ has → personal_information
                  └─→ has → residency_information
                  └─→ has → family_background
```

---

## User Flow

### Student Registration & Application
1. Student visits `/front_end/register.php`
2. Registers account → saved to `users` table
3. Student logs in at `/front_end/login.php` with email/password
4. Student completes application at `/front_end/apply.php`
   - Fills: personal info, residency, family background
   - Books appointment from `schedules` table
   - Submits → creates record in `applicants` table with status='pending'
5. Student views dashboard at `/front_end/dashboard.php`

### Admin Approval & Management
1. Admin logs in at `/front_end/login_admin.php`
   - Uses credentials from `admins` table (e.g., admin/admin123)
   - Session set: `$_SESSION['logged_in'] = true`, `$_SESSION['user_id'] = admin_id`
2. Admin views dashboard at `/front_end/admin_dashboard.html`
   - Shows count of approved students (from `users` table)
   - Shows chart of applicants by location
3. Admin views pending applications at `/front_end/pending_accounts.html`
   - Clicks "Approve" on applicant
   - Calls `/backend/approve_applicant.php` which:
     - Updates `applicants.status = 'approved'`
     - Creates account in `users` table with unique username and temporary password
     - Inserts record into `approved_credentials` with temp password
     - Inserts into `system_accounts` for compatibility
4. Admin manages schedules at `/front_end/set_schedule.html`
   - Creates appointment slots in `schedules` table
5. Admin views all registered accounts at `/front_end/system_account.html`
   - Shows all users from `users` table
6. Admin manages other admins at `/front_end/admin_profile.html`
   - Create/edit/delete admin accounts
   - Handled by `/backend/api_admins.php`

---

## Key Files Reference

### Authentication & Session
- `backend/login_process.php` - Handles admin login (POST)
- `backend/logout.php` - Handles logout for both students and admins
- `frontend/login_admin.php` - Admin login form
- `frontend/login.php` - Student login form
- `frontend/register.php` - Student registration form

### API Endpoints
- `backend/approve_applicant.php` - Approve an applicant (POST `applicant_id`)
- `backend/get_accounts.php` - Fetch all users (GET)
- `backend/get_pending_applicants.php` - Fetch pending applications (GET)
- `backend/get_dashboard_data.php` - Fetch dashboard stats (GET)
- `backend/get_schedules.php` - Fetch available appointment slots (GET)
- `backend/save_schedule.php` - Create appointment slot (POST)
- `backend/submit_application.php` - Submit scholarship application (POST)
- `backend/api_admins.php` - CRUD operations for admin accounts (GET/POST/PUT/DELETE)

### Admin Pages (Protected - require login)
- `frontend/admin_dashboard.html` - Dashboard with charts
- `frontend/admin_profile.html` - Admin management
- `frontend/system_account.html` - View registered students
- `frontend/pending_accounts.html` - Approve pending applications
- `frontend/set_schedule.html` - Manage appointment schedules
- `frontend/student_registration.html` - View all student registrations

### Student Pages (Protected - require login)
- `frontend/dashboard.php` - Student dashboard
- `frontend/profile.php` - Student profile
- `frontend/apply.php` - Application form
- `frontend/renewal.php` - Renewal form

---

## Testing the System

### Test Student Flow
1. Go to `/front_end/register.php`
2. Create account: 
   - Name: "John Doe"
   - Email: "john@example.com"
   - Password: "pass123"
3. Login at `/front_end/login.php` with those credentials
4. Fill out application at `/front_end/apply.php`
5. View on admin dashboard → pending applications

### Test Admin Approval
1. Login to `/front_end/login_admin.php` with admin/admin123
2. Go to pending applications
3. Click "Approve" on John's application
4. See temporary password displayed
5. Go to system accounts → should see John in the list
6. Dashboard should show approved count increased

---

## Troubleshooting

### Admin can't login
- Check: Did you run `setup_admin.php`? 
- Check: Does `admins` table exist? (`setup_database.php` should create it)
- Check: Username is `admin`, password is `admin123`

### Session not persisting
- Check: `session_start()` is called at top of files
- Check: Session headers not sent before `header()` calls
- Check: Cookies enabled in browser

### Student registration not saving
- Check: `users` table exists and has `first_name`, `last_name`, `email`, `password_hash` columns
- Check: Database connection works (`config/database.php`)

### Approval not working
- Check: `applicants` table has `applicant_id` and `status` columns
- Check: `personal_information` table has applicant data
- Check: Backend logs for SQL errors

---

## Security Notes

⚠️ **Change Default Password**
- Default admin password is `admin123`
- Change immediately after first login through admin profile

✅ **Passwords are hashed**
- Student passwords: `password_hash()` with PASSWORD_DEFAULT
- Admin passwords: `password_hash()` with PASSWORD_DEFAULT

✅ **Sessions are validated**
- Admin dashboard checks `$_SESSION['logged_in']`
- Redirects to login if session invalid or missing

✅ **SQL Injection Protected**
- All queries use prepared statements with parameterized bindings
- No direct string concatenation in SQL

