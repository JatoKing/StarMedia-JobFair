# Job Fair 2026 — Practical Test Submission

A web application for a Job Fair, built with **VueJS** (frontend), **PHP** (backend API), and **MySQL** (database). Covers Assignment #1 (Job Fair Web App) and Assignment #2 (Spinning Wheel Game), combined into a single application as separate routes (`/` and `/spinning-wheel`).

---

## 📋 System Requirements (Prerequisites)

Before starting, make sure the following are installed:

- **PHP 7.4+ or PHP 8** — recommended to use [Laravel Herd](https://herd.laravel.com) (Mac/Windows) for automatic PHP + Nginx setup
- **MySQL** — can be enabled via Herd Pro, or installed separately (e.g. `brew install mysql` on Mac)
- **Node.js** (v18 or above) and **npm** — to run Vue/Vite
- **Composer** — not required (this project does not use Laravel/PHP framework, only vanilla PHP)

---

## 📁 Project Structure

```
starmedia-jobfair/
├── backend/            ← PHP API, config, database connection
│   ├── api/
│   ├── config/
│   └── includes/
├── frontend/            ← VueJS app (Vite)
│   └── src/
├── database/
│   └── schema.sql       ← Database structure & seed data
└── README.md             ← This file
```

---

## 🗄️ Step 1 — Database Setup (MySQL)

### 1.1 Create a New Database

Open a terminal and run:

```bash
mysql -u root -e "CREATE DATABASE starmedia_jobfair_db;"
```

> If your MySQL has a password, add `-p` after `-u root` and enter the password when prompted.

### 1.2 Import Table Structure (Migration)

From the project root folder, run:

```bash
mysql -u root starmedia_jobfair_db < database/schema.sql
```

This command will create all required tables:

| Table | Purpose |
|---|---|
| `contact_submissions` | Stores Contact Us form data |
| `exhibitor_registrations` | Stores exhibitor registration data |
| `career_talk_sessions` | List of Job Matching/Career Talk sessions (with seed data) |
| `reservations` | Visitor session slot reservations |

### 1.3 Verify Tables Were Created Successfully

```bash
mysql -u root starmedia_jobfair_db -e "SHOW TABLES;"
```

You should see all four tables above listed.

> ⚠️ **Important note**: If you get a message like *"Table already exists"* and the data structure isn't as expected, there may be a leftover table with the same name from earlier testing. Drop it first (`DROP TABLE table_name;`) before re-importing, so the latest structure is used.

---

## ⚙️ Step 2 — Backend Setup (PHP)

### 2.1 Configure Database Connection

Open `backend/config/app.php`, and make sure the following constants match your MySQL setup (`backend/includes/db.php` reads these directly — it's the single source of truth for DB credentials):

```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'starmedia_jobfair_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // fill in if your MySQL has a password
```

### 2.2 Configure the AI Chatbot (Gemini API)

The chatbot uses the **Google Gemini API** (free, no credit card required).

**Get an API Key:**
1. Go to [aistudio.google.com/app/apikey](https://aistudio.google.com/app/apikey)
2. Sign in with a Google account
3. Click **"Create API Key"** and copy the generated key

**Set up in the project:**

Copy `backend/config/gemini.example.php` to `backend/config/gemini.php`:

```bash
cp backend/config/gemini.example.php backend/config/gemini.php
```

Open `backend/config/gemini.php` and replace the placeholder with your actual API key:

```php
define('GEMINI_API_KEY', 'REPLACE_WITH_YOUR_API_KEY');
define('GEMINI_MODEL', 'gemini-3.1-flash-lite');
```

> ⚠️ Gemini models are updated periodically. If the chatbot returns a "quota exceeded" or "model not found" error, run the following command to check which models are available for your account:
> ```bash
> curl "https://generativelanguage.googleapis.com/v1beta/models?key=YOUR_API_KEY"
> ```
> Pick any model listed under `"supportedGenerationMethods": ["generateContent"]` and update `GEMINI_MODEL` accordingly.

> `backend/config/gemini.php` is gitignored (`backend/.gitignore`) and never committed — the API key stays local only.

### 2.3 Register the Site with Herd

```bash
cd starmedia-jobfair
herd link starmedia-jobfair
```

The site will be accessible at `http://starmedia-jobfair.test`.

### 2.4 Configure CORS

Open `backend/includes/cors.php` and make sure the origin matches your Vite dev server port (default `5173`):

```php
header('Access-Control-Allow-Origin: http://localhost:5173');
```

### 2.5 Configure the API Base URL (Frontend)

Open `frontend/src/config/api.js` and make sure the URL matches your Herd domain:

```javascript
export const API_BASE_URL = 'http://starmedia-jobfair.test/backend/api';
```

---

## 🎨 Step 3 — Frontend Setup (VueJS)

### 3.1 Install Dependencies

```bash
cd frontend
npm install --legacy-peer-deps
```

> The `--legacy-peer-deps` flag is required to avoid a version conflict between `oxlint` and `eslint-plugin-oxlint`.

### 3.2 Prize Configuration File (Spinning Wheel)

The file `backend/config/prizes.json` stores prize data for Assignment #2. It can be edited directly without touching any PHP/Vue code:

```json
{
  "prizes": [
    {
      "id": "grand-01",
      "name": "iPhone 16",
      "type": "grand",
      "graphic": "🏆",
      "probability": 0.02,
      "quantity": 1,
      "remaining": 1
    }
    // ... other prizes
  ]
}
```

Make sure this file is writable by PHP (needed to update quantities after each spin):

```bash
chmod 664 backend/config/prizes.json
```

---

## ▶️ Step 4 — Run the Application

### 4.1 Start the Vite Dev Server (Frontend)

```bash
cd frontend
npm run dev
```

The terminal will display a URL similar to:
```
➜  Local:   http://localhost:5173/
```

### 4.2 Confirm the PHP Backend Is Running

Herd automatically runs PHP in the background. Confirm by opening:
```
http://starmedia-jobfair.test/backend/api/sessions.php
```
in your browser — you should see a JSON response (not an error).

### 4.3 Access the Application

Open your browser to:
```
http://localhost:5173
```

- **Main Page (Assignment #1)**: `http://localhost:5173/`
- **Spinning Wheel (Assignment #2)**: `http://localhost:5173/spinning-wheel`

---

## ✅ Testing Checklist

After setup, confirm all features work as expected:

- [ ] Countdown timer runs and the flip animation works
- [ ] Directory — search & category filter work, floor plan modal opens
- [ ] "Contact Us" form submits successfully (check data lands in `contact_submissions`)
- [ ] "Become an Exhibitor" form submits successfully (check `exhibitor_registrations`)
- [ ] Career Talk session reservation succeeds, remaining seats decrease
- [ ] BM/EN language toggle switches all text on the page
- [ ] Chatbot returns relevant AI responses
- [ ] Spinning Wheel spins, prize quantity decreases on a win, sold-out prizes disappear from the wheel

---

## 📝 Assessment Requirement Mapping

This section maps each requirement from the "Practical Test for Front-End Developer" brief to its actual implementation in the code, to make evaluation easier.

### Assignment #1 — Job Fair Web Application

**Basic Page Structure (HTML & CSS)**

| Requirement | Status | Implementation Location |
|---|---|---|
| Semantic HTML5 elements | ✅ | `HomeView.vue` and all components use `<header>`, `<nav>`, `<section>`, `<form>` |
| Dummy data / stock images | ✅ | `src/data/exhibitors.js` — 12 dummy companies with booth, positions, description |
| Responsive (across devices) | ✅ | Media queries `@media (max-width: 768px/640px/480px)` in every component |
| CSS matching mock design | ✅ | "Vibrant Playful" design system — `src/assets/styles/variables.css` |
| Animations/effects | ✅ | Scroll reveal (`v-reveal` directive), flip countdown, floating blobs, hover lift |
| Hover effects | ✅ | Buttons, cards, filter chips — all have hover transitions (`transform`, `box-shadow`) |

**Interactive Features (JavaScript & VueJS)**

| Requirement | Status | Implementation Location |
|---|---|---|
| Clickable floor plan/directory | ✅ | `FloorPlanGrid.vue`, `DirectorySection.vue` |
| Larger version (modal/lightbox) | ✅ | "View Floor Plan" button opens the full floor plan directly inside a `BaseModal` lightbox (`DirectorySection.vue` — `isFullMapOpen`) |
| Filtering/search | ✅ | `DirectorySection.vue` — search input + category filter |
| Contact Us form | ✅ | `ContactSection.vue` |
| JS validation (required, email format) | ✅ | `src/utils/validators.js` — `isRequired()`, `isValidEmail()` |
| PHP validation + save to database | ✅ | `backend/api/contact.php` |
| Exhibitor Registration (modal on click) | ✅ | `ExhibitorRegistrationModal.vue`, triggered from `HeroSection.vue` |
| Validate before submission | ✅ | Client-side (`validate()`) + server-side (`exhibitor.php`) |
| PHP server-side validation + save to DB | ✅ | `backend/api/exhibitor.php` |
| VueJS dynamic update without page reload | ✅ | Reactive form (`reactive()`), AJAX `fetch()` — no full page reload |
| Live Countdown Timer (days/hours/minutes/seconds) | ✅ | `CountdownTimer.vue` |
| Chatbot (free API) | ✅ | `ChatbotWidget.vue` + `backend/api/chatbot.php` (Google Gemini API) |
| Chatbot answers visitor/exhibitor questions | ✅ | System prompt in `chatbot.php` provides full Job Fair context |
| Job Matching/Career Talk reservation | ✅ | `ReservationSection.vue`, `SessionCard.vue` |
| VueJS manages the reservation process | ✅ | `useSessions.js`, `ReservationModal.vue` |
| PHP saves reservation to DB | ✅ | `backend/api/reservation.php` (with row-locking to prevent double-booking) |
| Multi-language support | ✅ | `src/i18n/` (Vue I18n) — Bahasa Melayu & English |
| Vue I18n + dynamic switching | ✅ | `LanguageSwitcher.vue`, all text uses `t()` |

**Bonus Features**

| Requirement | Status | Notes |
|---|---|---|
| Accessibility (keyboard nav, screen readers) | ⚠️ Partial | Semantic HTML + `aria-label`/`aria-expanded` on toggle buttons, `role="dialog"`/`aria-modal` + Escape-to-close on modals; no full focus-trap inside modals yet |
| Performance optimization | ⚠️ Partial | Lightweight CSS with no heavy framework; emoji used instead of heavy image assets |
| Progressive enhancement (works without JS) | ❌ No | The app relies fully on VueJS (SPA), as intended by the assignment |

### Assignment #2 — Job Fair Spinning Wheel Game

| Requirement | Status | Implementation Location |
|---|---|---|
| Semantic HTML5 | ✅ | `SpinningWheelView.vue` |
| Reads data from a config file (JSON) | ✅ | `backend/config/prizes.json` |
| Visually appealing CSS + animation | ✅ | Same design system (Vibrant Playful), gradient background |
| CSS customizable based on game data | ✅ | Wheel slice colors follow prize `type` (grand = gold, etc.) |
| Config file: name, quantity, probability, graphic | ✅ | `prizes.json` structure — every prize has all these fields |
| Wheel — pie slices matching number of unique prizes | ✅ | `SpinWheelSVG.vue` — dynamic slices based on `prizes.length` |
| Click "Spin" → animation → stop on a prize | ✅ | `SpinningWheelView.vue` — `handleSpin()`, 4s CSS transition |
| Different effect per prize type | ✅ | `ConfettiBurst.vue` (grand), `SparkleBurst.vue` (second) |
| Reduce quantity on a win | ✅ | `backend/api/spinning-wheel/spin.php` — updates `remaining` in the JSON, using `flock()` exclusive locking to stay correct under concurrent spins |
| Sold-out prizes no longer appear | ✅ | `availablePrizes` computed property — filters `remaining > 0` |
| Multi-language | ✅ | `spinningWheel.*` keys in `ms.json`/`en.json` |
| Solution to handle prize data from configuration file | ✅ | `backend/includes/prize_store.php` — safe read (`LOCK_SH`) / read-modify-write (`LOCK_EX`) helpers around `prizes.json`, used by `spin.php`; load-tested with 20 concurrent spin requests with no data corruption or lost decrements |

**Bonus Features**

| Requirement | Status | Notes |
|---|---|---|
| Performance optimization | ⚠️ Partial | Lightweight SVG, no large images to load |

---

## 🛠️ Common Troubleshooting

| Issue | Cause & Fix |
|---|---|
| Blank/empty page | Check `App.vue` uses `<RouterView />` and route `/` is registered in `router/index.js` |
| CSS/import "file not found" error | Check relative paths (`./`) are correct based on the file's actual location |
| "Server error" on a form | Check the MySQL table structure matches what the PHP code expects (`DESCRIBE table_name;`) |
| CORS error in browser console | Make sure the origin in `cors.php` matches your Vite port (`localhost:5173`) |
| Chatbot not responding | Check the Gemini API key is correct and the model used is still supported (see Step 2.2) |

---

## 📦 Tech Stack

- **Frontend**: VueJS 3 (Composition API), Vue Router, Pinia, Vue I18n, Vite
- **Backend**: PHP (vanilla, PDO for database access)
- **Database**: MySQL
- **AI**: Google Gemini API (chatbot)
- **Styling**: Custom CSS (design system with CSS variables), animations via native CSS transitions & Vue `<Transition>`

---

*Prepared for the Practical Test — Front-End Developer, Star Media Group Berhad.*
