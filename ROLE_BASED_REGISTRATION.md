# Role-Based Registration System

**Project:** OneDollarMeme  
**Feature:** User Role Selection During Registration  
**Date:** April 2, 2026  
**Status:** ✅ Complete

---

## 1. Overview

Users now select their role during registration:
- **"Post Memes"** → `user` role (meme creator)
- **"Write Blogs"** → `blogger` role (content writer)

Each role has restricted access to specific features.

---

## 2. Role Permissions

| Feature | Blogger | User (Meme) | Admin | Guest |
|---------|---------|-------------|-------|-------|
| **View Blogs** | ✅ | ✅ | ✅ | ✅ |
| **Create Blogs** | ✅ | ❌ | ✅ | ❌ |
| **Edit Own Blogs** | ✅ | ❌ | ✅ | ❌ |
| **Delete Own Blogs** | ✅ | ❌ | ✅ | ❌ |
| **Blogger Dashboard** | ✅ | ❌ | ✅ | ❌ |
| **View Memes** | ❌ | ✅ | ✅ | ✅ |
| **Upload Memes** | ❌ | ✅ | ✅ | ❌ |
| **Contest Participation** | ❌ | ✅ | ✅ | ❌ |
| **Brand Campaigns** | ❌ | ✅ | ✅ | ❌ |
| **Meme Comments** | ❌ | ✅ | ✅ | ❌ |
| **Blog Comments** | ❌ | ✅ | ✅ | ❌ |
| **Meme Reactions** | ❌ | ✅ | ✅ | ❌ |
| **Admin Panel** | ❌ | ❌ | ✅ | ❌ |
| **Full Access** | ❌ | ❌ | ✅ | ❌ |

**Note:** Admins have unrestricted access to ALL features (blogs + memes + admin panel).

---

## 3. Files Created/Modified

### 3.1 New Files

| File | Purpose |
|------|---------|
| `app/Http/Middleware/BloggerRestriction.php` | Restricts bloggers to blog routes only |

### 3.2 Modified Files

| File | Changes |
|------|---------|
| `resources/views/auth/register.blade.php` | Added role selection UI |
| `app/Http/Controllers/Auth/RegisteredUserController.php` | Handle role selection, redirect by role |
| `app/Models/User.php` | Added `isMemeUser()` method |
| `bootstrap/app.php` | Registered `restrict.blogger` middleware |
| `routes/web.php` | Applied restriction to meme routes |
| `resources/views/partials/_main-nav.blade.php` | Role-based navigation |

---

## 4. Registration Flow

### 4.1 Registration Page

Users see two options:

```
┌─────────────────────────────────────────────────────────┐
│                   I want to                              │
│                                                          │
│  ┌──────────────────┐    ┌──────────────────┐           │
│  │   🖼️ IMAGE      │    │   ✏️ PEN          │           │
│  │  Post Memes     │    │  Write Blogs     │           │
│  │  Upload & compete│   │  Create content  │           │
│  └──────────────────┘    └──────────────────┘           │
│       (user)                   (blogger)                 │
└─────────────────────────────────────────────────────────┘
```

### 4.2 Post-Registration Redirect

- **Bloggers** → `/blogs/dashboard`
- **Users** → `/upload-meme`

---

## 5. Middleware Logic

### BloggerRestriction Middleware

**Location:** `app/Http/Middleware/BloggerRestriction.php`

**Logic:**
1. **Admin Check (First):** If user is admin → Allow all access immediately
2. **Blogger Check:** If user is blogger → Check allowed routes
3. **Meme User:** No restrictions (can access meme features)

**Allowed Routes for Bloggers:**
- `blogs/*` - All blog routes
- `blog/*` - Single blog routes
- `logout` - Logout
- `password.*` - Password management
- `verification.*` - Email verification
- `profile.*` - Profile management
- `account.settings*` - Account settings

**Admin Exception:**
- Admin users bypass ALL restrictions
- Admins can access: memes, blogs, admin panel, everything

**Redirect:**
- Bloggers trying to access meme routes → Redirected to `/blogs/dashboard`
- Error message: "Bloggers can only access blog-related features..."

---

## 6. Navigation Changes

### 6.1 Meme Users See:
- TRENDING | BRANDS | WEEKLY BATTLE | BLOG
- Search bar
- UPLOAD button (purple)

### 6.2 Bloggers See:
- BLOG | MY DASHBOARD (orange)
- CREATE BLOG button (orange)

### 6.3 Admins See:
- TRENDING | BRANDS | WEEKLY BATTLE | BLOG | **ADMIN** (red)
- Search bar
- UPLOAD button (purple)
- CREATE BLOG button (orange)

### 6.4 Guests See:
- TRENDING | BRANDS | WEEKLY BATTLE | BLOG
- Login button

---

## 7. Route Protection

### Meme Routes (Restricted from Bloggers)

```php
Route::middleware(['restrict.blogger'])->group(function () {
    // Upload memes
    Route::get('/upload-meme', ...)
    Route::post('/upload-meme', ...)
    
    // Meme comments
    Route::post('/meme/{meme}/comments', ...)
    
    // Reactions
    Route::post('/memes/{meme}/reaction', ...)
    
    // Sponsored campaigns
    Route::get('/sponsored/{slug}/submit', ...)
    
    // Brand management
    Route::get('/my-brands', ...)
});
```

### Blog Routes (Open to All)

```php
// Public routes
Route::get('/blogs', ...)       // Anyone can view
Route::get('/blog/{slug}', ...) // Anyone can view

// Blogger-only routes
Route::middleware(['blogger'])->group(function () {
    Route::get('/blogs/create', ...)
    Route::post('/blogs/store', ...)
    Route::get('/blogs/dashboard', ...)
});
```

---

## 8. User Model Methods

```php
// Check if user is a blogger
$user->isBlogger();  // true if role='blogger' or admin

// Check if user is a meme user
$user->isMemeUser(); // true if role='user' or null or admin

// Check if user is admin
$user->isAdmin();    // true if email matches admin list

// Check if user has full access (admin only)
$user->canAccessAll(); // true if admin
```

---

## 9. Testing

### Test Scenarios

1. **Register as Blogger:**
   - Select "Write Blogs"
   - Should redirect to `/blogs/dashboard`
   - Cannot access `/upload-meme` (redirects)
   - Can create/edit/delete blogs

2. **Register as User:**
   - Select "Post Memes"
   - Should redirect to `/upload-meme`
   - Can upload memes
   - Can comment on memes
   - Can view blogs but not create

3. **Admin User:**
   - Can access EVERYTHING
   - Can upload memes ✅
   - Can create blogs ✅
   - Can access admin panel ✅
   - Can view/edit/delete anything ✅
   - No restrictions applied

4. **Navigation:**
   - Bloggers see "CREATE BLOG" button
   - Users see "UPLOAD" button
   - Admins see BOTH buttons + "ADMIN" link
   - Bloggers see "MY DASHBOARD" link

5. **Route Protection:**
   - Blogger tries `/upload-meme` → Redirect to dashboard
   - Blogger tries `/brands` → Redirect to dashboard
   - Blogger tries `/blogs/create` → ✅ Allowed
   - Admin tries anything → ✅ Always allowed

---

## 10. Changing User Role

Currently, role is set during registration and cannot be changed by the user.

**To change a user's role manually:**

```bash
php artisan tinker

# Change to blogger
App\Models\User::where('email', 'user@example.com')->update(['role' => 'blogger']);

# Change to meme user
App\Models\User::where('email', 'blogger@example.com')->update(['role' => 'user']);
```

---

## 11. Future Enhancements

1. **Role Change Request:** Allow users to request role change from dashboard
2. **Admin Role Management:** Add role management in admin panel
3. **Hybrid Accounts:** Allow users to have both roles (if needed)
4. **Role-based Landing Pages:** Different home page experience per role
5. **Onboarding Tutorial:** Show role-specific features on first login

---

## 12. Security Notes

- Role is stored in database (`users.role` column)
- Middleware validates role on every request
- Admin users bypass all restrictions
- Role cannot be changed via form submission (only in controller)

---

**Final Verdict:** Role-based access control is fully implemented. Bloggers are restricted to blog features only, while meme users have full access to meme features.
