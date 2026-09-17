# LMS Mobile API — Flutter Handover

Base URL: `https://YOUR-DOMAIN/api` — Auth: `Authorization: Bearer <token>` (Laravel Sanctum).
All authenticated endpoints return JSON. Pagination: Laravel format (`data`, `links`, `meta`).

## 1) Auth

### POST /api/login
Body: `{ "email": "student@mail.com", "password": "****" }`
- 200: `{ "token": "...", "user": { id, name, email, type, grade, current_teacher_id } }`
- 401: wrong credentials — 403: `{ message: "تم إيقاف حسابك" }` (blocked)

### POST /api/logout (auth)
Deletes current token. 200.

### GET /api/me (auth)
Current user profile (no password hash exposed).

## 2) Teacher picking (student with multiple teachers)

### GET /api/my-teachers (auth, student)
`{ teachers: [{ id, name, subject, color }], current_teacher_id }`
- If `teachers.length > 1 && current_teacher_id == null` → show picker screen.

### POST /api/select-teacher (auth, student)
Body: `{ teacher_id }` → `{ current_teacher_id }` (403 if not enrolled with them).
- Optional query `?teacher_id=` on courses/videos/exams overrides the saved choice per call.

## 3) Content (auto-scoped: student sees own grade only + selected teacher)

### GET /api/courses, /api/videos, /api/exams (auth)
Query: `?page=` (+ `?teacher_id=`). Shapes:
- course: `{ id, title, description, grade, subject, scheduled_at, price, is_live, teacher: { id, name } }`
- video: `{ id, title, description, grade, subject, embed_url, duration_seconds, views_count, teacher }`
- exam: `{ id, title, description, grade, subject, exam_date, duration_minutes, total_marks, max_attempts, questions: [{ id, type, question, options, marks }] }`
- NOTE: `questions` never include `correct_answer`.

### POST /api/exams/{exam}/submit (auth, student)
Body: `{ answers: { "<question_id>": "answer" }, tab_switches?: number }`
- Enforces time window + `max_attempts` server-side (attempts tracked in `exam_attempts`, same as web).
- 200: `{ marks, result_id, attempt_no }` — 422 when not available.

## 4) Payments / gamification / offline

### GET /api/payments (auth)
Student: own invoices. Parent: children invoices. Item: `{ id, month, amount, paid_amount, remaining, method, payer, student }`.
- NOTE: online payment + OTP confirmation is currently **web-only** (`/dashboard/payments/...`). Mobile should open it in WebView or wait for v2 endpoints.

### GET /api/leaderboard?grade=3_secondary (auth)
Top 20: `[{ id, name, points, avg }]` (best-score-per-exam).

### GET /api/offline-manifest (auth)
`{ videos: [{ id, title, video_url, duration_seconds }], generated_at }` — latest 20 for download queue.

## 5) Rules the app must respect
- Timer: use `duration_minutes` from exam; auto-submit on expiry.
- Shuffle display order client-side if desired (server shuffles for web only).
- Grades enum: `1_secondary | 2_secondary | 3_secondary`.
- User types: `admin | teacher | student | parent`.
- Store token in secure storage; call `/api/logout` on sign-out.

## 6) Suggested Flutter screens
Login → (Teachers picker if needed) → Home (upcoming + recommendations*) → Courses / Videos (player) → Exams (timer + submit) → Invoices (WebView pay) → Leaderboard → Profile.
\* Recommendations/predictions are web-dashboard only for now.
