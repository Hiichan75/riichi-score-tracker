# Riichi Score Tracker

Live riichi mahjong hanchan scorer with room codes for multiplayer sessions.

## Features

- Create a session with a 6-character room code (e.g. `LOTUS4`)
- Players join on their phones by entering the room code
- Scores sync automatically every 2 seconds
- Uma: +15/+5/-5/-15 · Starting points: 30 000
- Leaderboard and full hanchan history

## Setup

### 1. Database
Create a MySQL database on Hostinger and run `schema.sql` in phpMyAdmin.

### 2. Config
Edit `db.php` with your Hostinger MySQL credentials.

### 3. Upload
Upload all files to `public_html/riichi/` on Hostinger.

### 4. Done
Open `https://yourdomain.com/riichi/` and create your first session.

## Files

| File | Purpose |
|------|---------|
| `index.html` | Frontend — lobby, scorer, leaderboard, history |
| `api.php` | REST API — sessions and hanchans |
| `db.php` | Database connection config |
| `schema.sql` | Creates the database tables |
