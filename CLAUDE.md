# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Netlabs is an Intelligent Tutoring System (ITS) + LMS for "Praktikum Jaringan Komputer Dasar" (networking lab) aimed at SMK (vocational high school) students. It uses RAG (Retrieval-Augmented Generation) with Claude API for an AI tutor feature.

## Architecture

Three independent components share a Supabase (PostgreSQL) database:

- **backend/** — FastAPI (Python) API server. Serves the mobile app. Handles auth (JWT), CRUD, AI chat (RAG via ChromaDB + Claude API), and PDF module processing.
- **web-laravel/** — Laravel 12 + Blade + Tailwind CSS 4 + Vite. Server-rendered admin dashboard for teachers (guru). Connects directly to Supabase via its own queries, not through the FastAPI backend.
- **mobile/** — Flutter app for students (siswa). Uses GetX for state management and routing. Connects to the FastAPI backend.

### Data flow

- Mobile app → FastAPI backend → Supabase
- Web admin (Laravel) → Supabase (direct)
- RAG: Guru uploads PDF → backend parses/chunks → embeddings stored in ChromaDB → student asks question → relevant chunks retrieved → Claude API generates answer

## Common Commands

### Backend (FastAPI)

```bash
cd backend
python -m venv venv && venv/Scripts/activate   # Windows
pip install -r requirements.txt
uvicorn main:app --reload                       # http://localhost:8000/docs
```

Env vars needed: `SUPABASE_URL`, `SUPABASE_KEY`, `SUPABASE_SERVICE_KEY`, `ANTHROPIC_API_KEY`, `JWT_SECRET`

### Web Admin (Laravel)

```bash
cd web-laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
composer dev    # runs server + queue + pail + vite concurrently
```

Or individually: `php artisan serve` and `npm run dev`

Tests: `composer test` (runs PHPUnit via `php artisan test`)
Lint: `./vendor/bin/pint` (Laravel Pint)

### Mobile (Flutter)

```bash
cd mobile
flutter pub get
flutter run
```

Requires Flutter SDK ^3.8.1.

## Key Patterns

### Mobile (Flutter + GetX)

- Modules at `mobile/lib/modules/<feature>/` — each has a view and optionally a controller
- Shared services/constants/theme at `mobile/lib/app/`
- API calls go through `mobile/lib/app/services/api_service.dart`
- Navigation via GetX named routes defined in `mobile/lib/app/routes/`
- All screens have dummy data fallback when backend is offline

### Backend (FastAPI)

- Routers at `backend/app/routers/` — one file per domain (auth, pertemuan, topik, chat, kuis, nilai, guru, modul)
- Services at `backend/app/services/` (pdf_service, rag_service)
- Config via `backend/app/config.py` (reads from `.env`)
- All API routes prefixed with `/api/<domain>`

### Web Admin (Laravel)

- Blade views at `web-laravel/resources/views/` with layouts
- Controllers: AuthController, DashboardController, PertemuanController, SiswaController
- Custom middleware `AuthGuru` protects teacher-only routes
- Uses session-based auth (not JWT)

## Language

Project documentation, commit messages, and UI text are in Bahasa Indonesia. Code identifiers mix Indonesian domain terms (pertemuan, topik, siswa, guru, kuis, nilai, modul) with English programming conventions.

## Demo Credentials

| Role | Username | Password |
|------|----------|----------|
| Guru | GURU001 | guru123 |
| Siswa | 2122100045 | siswa123 |
