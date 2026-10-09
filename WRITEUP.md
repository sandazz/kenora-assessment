# Assessment Write-Up — Workshop Registration System

## Tech Stack & Choice Rationale
I chose **Laravel with standard Blade templates** and vanilla Tailwind CSS. Given the project requirements and tight time frame, using Laravel’s built-in session auth, Blade rendering, and Form Request validation allowed me to get a fully working, secure app running quickly without spending time configuring SPA routing or complex frontend state management. It also fits well with the problem context: non-technical staff using a simple, dependable internal tool.

For the database, I went with **SQLite as the zero-setup default** so anyone evaluating the project can clone and run it immediately without spinning up extra database containers. I configured SQLite with `IMMEDIATE` transaction mode and a `busy_timeout` to handle concurrent write transactions smoothly, while structuring all code to work on MySQL or PostgreSQL.

---

## Key Design Decisions
- **Service Layer**: Kept controllers thin by moving core logic into dedicated services (`RegistrationService`, `WorkshopService`, `UserService`). Every database write passes through a service class wrapped in a Form Request for validation.
- **Backend-First Security**: Role checks (`admin`, `manager`, `staff`) are enforced on the server via custom middleware (`EnsureRole`), Laravel Policies, and Form Requests. The Blade templates only hide or show UI elements based on what the user is allowed to do.
- **Audit & History Retention**: Registrations are never hard-deleted. When cancelled, the status changes to `cancelled`, and details like who cancelled it, when, and the reason are recorded.
- **Unique Active Registration Trick**: Used a composite unique index on `(workshop_id, attendee_email, active_key)`. When a registration is active, `active_key = 1`. When cancelled, `active_key = NULL`. Because SQL standards ignore `NULL` collisions in unique indexes, attendees can re-register after cancelling, while active duplicates are blocked at the database level.

---

## Preventing Over-Registration
To ensure active registrations never exceed workshop capacity—even under concurrent requests:
1. `RegistrationService::register()` wraps the process inside `DB::transaction(..., attempts: 3)`.
2. The workshop row is fetched with `lockForUpdate()` (pessimistic lock) inside the transaction.
3. Active seats are counted *inside* the lock (`status = 'active'`). If `count >= capacity`, a `WorkshopFullException` is thrown.
4. `WorkshopService::update()` also locks the row to block lowering capacity below the current active registration count.

I verified this with an automated test suite and a script (`scripts/concurrency-demo.php`) that fires parallel registration attempts at a 1-seat workshop.

---

## Trade-offs & Assumptions
- **Pessimistic vs. Optimistic Locking**: I chose pessimistic locking (`lockForUpdate()`) because preventing overbooking is a hard business requirement. A small wait during simultaneous bookings is better than double-booking a seat.
- **Assumptions**: Attendees are external guests registered by staff (no attendee user accounts needed). Emails are normalized to lowercase. Admins focus purely on user management and audit logs, keeping workshop ops to managers and staff.

---

## What Was Skipped
- **Phase 6 Waitlist**: Deactivated by choice to focus time on making sure access control, capacity locks, audit logging, and tests were 100% complete and verified.
