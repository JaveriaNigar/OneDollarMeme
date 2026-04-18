# OneDollarMeme - Project Documentation

## Overview

OneDollarMeme is a full-stack meme platform built with Laravel PHP and Python AI services. It enables users to upload, share, and discover memes, participate in weekly challenges, read blogs, and generate AI-powered memes through an integrated meme agent.

## Tech Stack

### Backend (Laravel)
- **Framework**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade templates + TailwindCSS + AlpineJS
- **Build Tool**: Vite
- **Database**: MySQL/PostgreSQL with Eloquent ORM
- **Authentication**: Laravel Breeze + Socialite (Google, Facebook)
- **Email**: Resend API
- **Payments**: Stripe
- **Queue**: Redis/Celery

### AI Services (Python)
- **Framework**: FastAPI + Uvicorn
- **AI/ML**: OpenAI Agents, GPT-4o, OpenAI Moderation API
- **Vector Search**: FAISS + Sentence Transformers
- **Embeddings**: Sentence Transformers (all-MiniLM-L6-v2)
- **Web Scraping**: BeautifulSoup4
- **ML Libraries**: PyTorch, Scikit-learn

## Project Structure

```
OneDollarMeme/
├── app/
│   ├── Http/Controllers/     # Laravel controllers
│   ├── Models/               # Eloquent models (User, Meme, Blog, etc.)
│   ├── Policies/             # Authorization policies
│   └── Providers/            # Service providers
├── backend/                  # Python FastAPI services
│   ├── main.py              # Main FastAPI application
│   ├── server.py            # Alternative server entry
│   └── requirements.txt     # Python dependencies
├── meme_agent/              # AI Meme generation agent
│   ├── core.py             # Core agent logic with OpenAI
│   ├── api.py              # API endpoints for meme generation
│   ├── data_loader.py      # Dataset loader for meme templates
│   ├── embeddings.py       # Vector embedding generation
│   ├── vector_store.py     # FAISS vector store management
│   ├── caption_search.py   # Semantic search for captions
│   ├── vision_analyzer.py  # Image analysis with vision models
│   ├── web_search_tool.py  # Web scraping for trending content
│   └── scripts/            # Utility scripts (build index, tests)
├── resources/
│   ├── views/              # Blade templates
│   ├── css/                # Stylesheets
│   └── js/                 # JavaScript assets
├── routes/
│   ├── web.php             # Web routes
│   ├── api.php             # API routes
│   └── auth.php            # Authentication routes
├── database/
│   └── migrations/         # Database migrations
├── config/                 # Laravel configuration
├── public/                 # Public assets
├── storage/                # Storage (uploads, logs, cache)
└── docker-compose.yml      # Docker configuration
```

## Key Features

### 1. User Management
- **Role-based access control**:
  - `user` - Standard meme users
  - `blogger` - Can create/edit blogs, add links
  - `admin` - Full system access
- Social authentication (Google, Facebook)
- Email verification with Resend
- Profile management with avatars

### 2. Meme System
- Upload memes with titles, tags, templates
- Browse and search memes
- Like, comment, and share memes
- Report inappropriate content
- Weekly challenges/contests with payouts

### 3. AI Meme Agent
- Generate memes from topics using OpenAI GPT-4o
- Style selection (relatable, trending, classic, etc.)
- Tone customization (funny, sarcastic, wholesome, etc.)
- Template-based or AI-generated memes
- Semantic search for similar memes
- Web search integration for trending topics
- Content safety moderation

### 4. Blog System
- Role-based blog creation (bloggers only)
- Rich text editor (TinyMCE) with link support
- Comments on blog posts
- SEO-friendly URLs and meta tags
- HTML sanitization for security

### 5. Brand & Sponsored Campaigns
- Brand registration and management
- Sponsored meme campaigns
- Brand access restrictions
- Campaign analytics

### 6. Engagement & Gamification
- Weekly challenges with prize payouts
- Leaderboards and winner announcements
- User activity tracking
- Share events and analytics

## Database Schema

### Core Tables
- `users` - User accounts with roles
- `memes` - Meme uploads with metadata
- `meme_comments` - Nested comments on memes
- `meme_likes` - Like tracking
- `reactions` - Emoji reactions
- `blogs` - Blog posts
- `blog_comments` - Blog comments
- `brands` - Brand profiles
- `sponsored_campaigns` - Campaign data
- `sponsored_submissions` - User submissions to campaigns
- `weekly_challenges` - Challenge definitions
- `challenge_entries` - User challenge submissions
- `challenge_payouts` - Prize distributions
- `share_events` - Share tracking

## API Endpoints

### Laravel Web Routes (`routes/web.php`)
- `/` - Home page
- `/memes` - Browse memes
- `/meme/{id}` - Single meme view
- `/upload` - Upload meme
- `/blogs` - Blog listing
- `/blog/{slug}` - Blog detail
- `/meme-agent` - AI meme generator
- `/winners` - Challenge winners
- `/profile` - User profile
- `/admin/*` - Admin dashboard

### Python FastAPI Endpoints (`backend/`)
- `GET /` - Health check
- `GET /styles` - Available meme styles
- `GET /tones` - Available tones
- `POST /chat` - Generate memes
- `POST /generate` - Alternative generation endpoint

## Environment Variables

```env
# Laravel
APP_NAME=OneDollarMeme
APP_ENV=local
APP_KEY=
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

# Services
OPENAI_API_KEY=
RESEND_API_KEY=
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=

# Social Login
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=

# Python Backend
REDIS_URL=
```

## Setup Instructions

### Laravel Setup
```bash
# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Install JavaScript dependencies
npm install

# Build assets
npm run build

# Start development server
composer run dev
```

### Python Backend Setup
```bash
# Install dependencies
pip install -r backend/requirements.txt
pip install -r meme_agent/requirements.txt

# Run FastAPI server
python backend/server.py --reload

# Or with uvicorn
cd backend
uvicorn main:app --reload --port 8000
```

### Docker Setup
```bash
# Build and run with Docker Compose
docker-compose up -d
```

## Vector Search Setup

1. Prepare meme dataset in `meme_agent/data/`
2. Build FAISS index:
   ```bash
   cd meme_agent/scripts
   python build_index_cpu.py  # or build_index_gpu.py
   ```
3. Index file generated at `meme_agent/data/meme_vector_index.faiss`

## Key Controllers

| Controller | Purpose |
|------------|---------|
| `MemeController` | Meme CRUD, likes, comments |
| `MemeAgentController` | AI meme generation integration |
| `BlogController` | Blog CRUD, comments |
| `BrandController` | Brand management |
| `AdminController` | Admin dashboard |
| `ProfileController` | User profiles |
| `CommentsController` | Comment management |
| `SponsoredCampaignController` | Campaign management |

## Security Features

- CSRF protection on all forms
- HTML sanitization with allowed tags whitelist
- XSS protection
- Role-based middleware
- Email verification
- Content moderation via OpenAI API
- Profanity filtering

## Development Commands

```bash
# Run tests
php artisan test

# Queue worker
php artisan queue:work

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Database
php artisan migrate:fresh --seed
php artisan migrate:rollback
```

## License

MIT License - See composer.json for details.

---

*Last updated: 2026-04-17*
