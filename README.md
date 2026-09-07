# Jestify Offline Music API

Laravel 12 REST API for a Flutter music player. The Flutter app owns the actual MP3 files in local device storage. This backend stores only metadata, local file identifiers, playlists, favorites, listening history, and user settings.

## Local setup

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Set local database values in `.env`. The seed creates `demo@jestify.app` with password `password` and sample metadata using identifiers such as `music/midnight-drive.mp3`.

## Supabase PostgreSQL and Render

Use Supabase PostgreSQL as the Render database. Set these Render environment variables from the Supabase connection details:

- `DB_CONNECTION=pgsql`
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `DB_SSLMODE=require`
- `APP_KEY`, `APP_URL`, `APP_ENV=production`, `APP_DEBUG=false`
- `API_RATE_LIMIT` (default `60` requests per minute per authenticated user or IP)

No audio, cover, or profile files are uploaded by this API. `cover_image` and `profile_image` are metadata references, such as a CDN URL or Flutter asset identifier.

The included `render.yaml` runs migrations during service startup and serves Laravel on Render's `$PORT`.

## API

Public: `POST /api/register`, `POST /api/login`, `GET /api/songs`, `GET /api/songs/{id}`, and `GET /api/search?q=`.

Authenticated with `Authorization: Bearer TOKEN`: logout, song metadata CRUD, playlists, playlist membership, favorites, listening history, and settings (`GET /api/settings`, `PUT /api/settings`).

Create song metadata with JSON, for example:

```json
{
  "title": "Song Name",
  "artist": "Artist",
  "album": "Album Name",
  "genre": "Electronic",
  "duration": 218,
  "cover_image": "covers/song001.jpg",
  "local_file_identifier": "music/song001.mp3"
}
```

Every response uses `{ success, message, data }`. Errors use `{ success: false, message, errors }`.

## Verification

```bash
php artisan migrate:fresh --seed
php artisan test
php artisan route:list --path=api
```

Import `postman/Jestify Offline Music API.postman_collection.json` into Postman for the endpoint collection.
