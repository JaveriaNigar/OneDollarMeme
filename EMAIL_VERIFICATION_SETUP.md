# Email Verification & Social Login Setup

## Features Implemented

### 1. Email Verification
- ✅ Users must verify email before participating in contests
- ✅ Verification email sent automatically on registration
- ✅ Resend verification link option
- ✅ Email verification required for uploading memes

### 2. Google Login
- ✅ One-click Google authentication
- ✅ Auto-creates account with verified email
- ✅ Links Google ID to existing accounts

### 3. Facebook Login
- ✅ One-click Facebook authentication
- ✅ Auto-creates account with verified email
- ✅ Links Facebook ID to existing accounts

---

## Configuration Steps

### 1. Google OAuth Setup

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing
3. Enable "Google+ API"
4. Go to "Credentials" → "Create Credentials" → "OAuth 2.0 Client ID"
5. Set authorized redirect URI: `http://127.0.0.1:8000/auth/google/callback`
6. Copy Client ID and Client Secret

### 2. Facebook OAuth Setup

1. Go to [Facebook Developers](https://developers.facebook.com/)
2. Create a new app
3. Add "Facebook Login" product
4. Go to Settings → Basic
5. Copy App ID (Client ID) and App Secret (Client Secret)
6. Set Valid OAuth Redirect URI: `http://127.0.0.1:8000/auth/facebook/callback`

### 3. Update .env File

```env
# Google
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT=http://127.0.0.1:8000/auth/google/callback

# Facebook
FACEBOOK_CLIENT_ID=your_facebook_app_id_here
FACEBOOK_CLIENT_SECRET=your_facebook_app_secret_here
FACEBOOK_REDIRECT=http://127.0.0.1:8000/auth/facebook/callback

# Email (for sending verification emails)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@onedollarmeme.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## User Flow

### Registration Flow
1. User registers with email/password
2. Verification email sent automatically
3. User redirected to verification notice page
4. User clicks verification link in email
5. Email verified - user can now upload memes and participate in contests

### Google/Facebook Login Flow
1. User clicks "Continue with Google/Facebook"
2. Redirected to social provider for authentication
3. Returns to site with verified email
4. Account auto-created or linked
5. User logged in immediately (email pre-verified)

---

## Routes

### Email Verification
- `GET /email/verify` - Show verification notice
- `GET /email/verify/{id}/{hash}` - Verify email link
- `POST /email/resend` - Resend verification email

### Social Login
- `GET /auth/google` - Redirect to Google
- `GET /auth/google/callback` - Google callback
- `GET /auth/facebook` - Redirect to Facebook
- `GET /auth/facebook/callback` - Facebook callback

---

## Files Modified/Created

| File | Purpose |
|------|---------|
| `app/Models/User.php` | Added `MustVerifyEmail` interface, social ID fields |
| `app/Http/Controllers/EmailVerificationController.php` | **NEW** - Handles verification & social login |
| `app/Http/Controllers/Auth/RegisteredUserController.php` | Send verification email on registration |
| `app/Http/Middleware/EnsureEmailIsVerified.php` | **NEW** - Check email verified |
| `database/migrations/*_add_social_ids_to_users_table.php` | **NEW** - Add google_id, facebook_id |
| `resources/views/auth/verify-email.blade.php` | **NEW** - Verification notice page |
| `routes/web.php` | Added verification & social routes |
| `bootstrap/app.php` | Registered `verified` middleware |

---

## Testing

1. **Register new user** → Check email for verification link
2. **Click verification link** → Should redirect to home with success message
3. **Login with Google** → Should auto-create account and log in
4. **Login with Facebook** → Should auto-create account and log in
5. **Upload meme without verified email** → Should redirect to verification page

---

## Security Notes

- Social login emails are pre-verified by Google/Facebook
- Password is randomly generated for social accounts (users can reset later)
- Email verification required for contest participation (uploading memes)
- Admin emails are pre-verified in the system
