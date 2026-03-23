Good, these clarifications actually make the v1 much cleaner.

Plan mode update: revised Laravel app plan

1. Updated product definition

This is now basically a small-company Leave / PTO Management App with:

- employee login
- leave request booking
- shared company calendar
- leave history
- admin approval flow
- monthly PTO accrual

Since it’s under 20 people, we can keep v1 simpler:

- no departments
- no privacy restrictions
- no holiday API integration yet
- weekdays only: Monday to Friday

2. Important modeling decision: half-day should not be a leave type

I strongly recommend this:

- “Vacation”, “Paid Leave”, “Sick Leave”, “Urgent Leave” = leave types
- “Half day” = request duration option

Why:
- a half day can apply to vacation or paid leave
- it’s not really a different reason for being absent
- it’s just how much time is being used

So in the system, a request should be something like:

- Leave Type: Vacation
- Duration: Half day
- Date: 2026-04-10
- Consumed balance: 0.5 day

That is much cleaner than treating “half day” as a standalone leave type.

3. Vacation vs Paid Leave: are they the same?

Short answer: maybe yes, maybe no.

From a business perspective:

- Vacation usually means planned time off for rest/holiday
- Paid Leave often means personal paid time off for other reasons

But from what you described, both use the same pool of days.

That means system-wise they can be:

- different labels for reporting/calendar
- but tied to the same balance bucket

My recommendation for v1:
keep them as separate selectable leave types, but make both consume the same shared PTO balance.

So you could have:

- Vacation
- Paid Leave
- Sick Leave
- Urgent Leave

Rules:
- Vacation -> consumes PTO balance
- Paid Leave -> consumes PTO balance
- Sick Leave -> decision needed
- Urgent Leave -> decision needed
- Half day -> consumes 0.5 PTO if the selected type uses PTO

If you want even cleaner wording, I’d consider renaming “Paid Leave” to:

- Personal Leave

Because “Vacation” is also paid leave in many companies, so the current naming can get confusing.

4. Your updated leave balance logic

Based on what you said, the best model is:

- every employee earns 1 PTO day per month
- after 12 months, they have earned 12 PTO days
- that balance can be used for Vacation or Paid Leave
- half day uses 0.5 from that same balance
- weekends do not count

So this becomes a shared balance pool.

Recommended terminology:
- call it `PTO balance` or `Leave balance`

Example:
- employee has 10 PTO days available
- books 2 vacation days -> remaining 8
- books 1 half day paid leave -> remaining 7.5

That logic is simple and easy for employees to understand.

5. Recommended rule for monthly accrual

I recommend this exact rule for v1:

- on the 1st of each month, each active employee gets `+1.0` PTO day
- the app stores that as a balance transaction
- balance is calculated from:
  - monthly accruals
  - manual adjustments by admin
  - approved/pending requests that consume PTO

This is better than just storing one total number, because you get an audit trail.

So the system knows:
- when PTO was earned
- when it was used
- if admin adjusted it manually

6. Very important business rule: future bookings with monthly accrual

This is something you should decide early.

Question:
Can an employee book leave in the future using days they have not earned yet, but will earn by then?

Example:
- today is March
- employee currently has 3 PTO days earned this year
- they want to book 5 days in August

Possible approaches:

Option A: strict current balance only
- they can only request up to 3 days now
- simple, but annoying for planning vacations

Option B: projected balance by leave date
- system checks how many days they will have by August
- if by August they will have enough, request is allowed

I recommend Option B for your use case.

Why:
- it’s much more practical for a company
- employees can plan ahead
- admins can still approve/reject based on workload

If you choose Option B, the app should also reserve pending requests so employees cannot overbook future days.

Example:
- current earned: 3
- by August projected earned: 8
- already has 2 days pending in June
- then available projected for August is not 8, but effectively 6

That reservation logic is important.

7. What “max future booking window” means

This setting controls how far into the future an employee is allowed to request leave.

Examples:
- 3 months
- 6 months
- 12 months

If set to 6 months:
- today is March 23
- employee can request leave up to around September 23
- anything beyond that is blocked

Why companies use this:
- prevents people booking too far ahead
- helps staffing planning
- avoids employees reserving dates a year in advance
- works nicely with monthly accrual policies

My recommendation for your app:
- make this a configurable setting
- default it to 6 months
- if you want less complexity for MVP, you can postpone enforcing it until later

So for v1:
- either skip it entirely
- or add a simple setting with default 6 months

8. Holidays for later

For now:
- only Monday to Friday count as workdays
- weekends are excluded
- no public holiday logic yet

But architecturally, we should still write the leave day calculation in a way that can later support holidays.

Meaning:
- create a dedicated `LeaveDurationCalculator` service
- for now it excludes only weekends
- later it can also exclude public holidays from a `public_holidays` table

That way you won’t need a refactor later.

9. Updated v1 business rules

Here is the revised rule set I would use.

Leave types
- Vacation
- Paid Leave
- Sick Leave
- Urgent Leave

Duration
- Full day
- Half day

Visibility
- everyone can see who is off
- everyone can see the leave type

Workday calculation
- Monday to Friday only
- Saturday/Sunday excluded
- holidays ignored for now

Balance
- 1 PTO day earned per month
- shared PTO balance used for Vacation and Paid Leave
- Half day uses 0.5
- carry-over continues unless you define yearly reset later

Request lifecycle
- Pending
- Approved
- Rejected
- Cancelled

Editing
- employee can edit/cancel only while request is pending

Calendar
- show company-wide leave calendar
- no department filter needed in v1

10. One decision still needed: sick leave and urgent leave

This is the biggest remaining rule question.

Do Sick Leave and Urgent Leave:

A. consume the same PTO balance?
B. have a separate balance?
C. not use balance at all, and just be tracked?

My recommendation for v1:
- Vacation -> uses PTO
- Paid Leave -> uses PTO
- Sick Leave -> tracked separately, does not use PTO
- Urgent Leave -> tracked separately for now, does not use PTO

Why I recommend that:
- it keeps the monthly PTO logic clean
- sick leave is usually different from planned paid leave
- urgent leave often needs different handling too

If later company policy says urgent leave should consume PTO, you can change it easily.

11. Updated data model

For your clarified requirements, I’d now use this simpler schema.

`users`
- id
- name
- email
- password
- role
- is_active
- hire_date nullable
- timestamps

`leave_types`
- id
- name
- code
- color
- consumes_pto boolean
- requires_approval boolean
- active boolean
- timestamps

Suggested records:
- Vacation / consumes PTO
- Paid Leave / consumes PTO
- Sick Leave / false
- Urgent Leave / false

`leave_requests`
- id
- user_id
- leave_type_id
- start_date
- end_date
- duration_type enum: `full_day`, `half_day`
- half_day_period enum nullable: `morning`, `afternoon`
- total_days decimal(4,2)
- reason text nullable
- status enum: `pending`, `approved`, `rejected`, `cancelled`
- approved_by nullable
- approved_at nullable
- rejection_reason nullable
- cancelled_at nullable
- timestamps

`pto_transactions`
- id
- user_id
- amount decimal(5,2)
- type enum:
  - `monthly_accrual`
  - `manual_adjustment`
  - `carry_over`
- effective_date
- reference_month nullable
- notes nullable
- created_by nullable
- timestamps

`leave_request_logs`
- id
- leave_request_id
- actor_id
- action
- note nullable
- created_at

Optional later:
- `public_holidays`
- `settings`

12. Why I prefer `pto_transactions`

Because your balance is earned monthly.

This table gives you:
- auditability
- safer balance calculations
- easier debugging
- easier future changes like carry-over, manual bonus days, corrections

For a leave balance system, ledger-style tracking is much better than only storing a final number.

13. Updated feature plan

Employee side
- login
- dashboard
- request leave
- select full day or half day
- see current PTO balance
- see projected future balance
- see request history
- see company leave calendar

Admin side
- approve/reject requests
- see pending queue
- see company calendar
- adjust PTO balances manually
- create leave on behalf of employee
- manage leave types
- manage users

Super admin side
- manage admins
- manage roles/permissions
- manage system settings
- configure accrual policy later
- audit logs

14. Suggested UI/UX for this version

Employee dashboard
- current PTO balance
- pending requests
- upcoming approved leave
- quick “Request Leave” button
- “Who is off today” list
- mini calendar or upcoming absences list

Request form
- leave type
- date
- full day / half day toggle
- if half day, choose morning or afternoon
- live display of total days to be used
- live display of remaining balance
- submit

History page
- list of all own requests
- filters by status and type
- clearly show approved/rejected/cancelled
- show past and upcoming separately

Company calendar
- month view
- all employees visible
- color by leave type
- click event to view details

15. Recommended implementation approach

Given the smaller scope now, you have two valid paths:

Option 1: best UX
- custom TALL frontend for employees
- Filament for admin and super admin

Option 2: fastest delivery
- use Filament for nearly everything
- add a custom calendar page and custom request page where needed

My recommendation is still:
- custom employee UI
- Filament admin panel

But because the company is small, using more Filament than originally planned is also very reasonable.

16. Core services you should build

These should be isolated from controllers/Livewire components.

`LeaveDurationCalculator`
- calculates days between start and end
- excludes weekends
- handles half day as 0.5

`PtoBalanceService`
- current balance
- projected balance at future date
- pending reserved amount
- available balance after reservations

`LeaveRequestService`
- create request
- validate overlaps
- validate future booking rules
- validate balance rules

`LeaveApprovalService`
- approve
- reject
- cancel
- update reservation/usage effects

17. Important validations

Must-have for v1:

- cannot request overlapping leave
- weekends are not counted
- half day should only be allowed for one date in v1
- only pending requests can be edited/cancelled by employee
- employee cannot approve own request
- PTO-consuming leave cannot exceed allowed balance
- start date cannot be after end date
- if half day, start and end date should be the same in v1

That last rule keeps the first version much simpler.

18. Revised implementation phases

Phase 1: foundation
- Laravel setup
- auth
- roles and permissions
- base layouts
- seed users/roles

Phase 2: leave domain
- leave types
- leave requests
- PTO transaction ledger
- balance service
- duration calculator
- overlap validation

Phase 3: employee features
- dashboard
- request form
- request history
- calendar page
- half-day support

Phase 4: admin workflow
- approvals queue
- approve/reject actions
- manual balance adjustments
- user management
- leave type management

Phase 5: automation and polish
- monthly accrual command
- scheduler
- notifications
- better validation UX
- tests

19. Monthly accrual automation

This should be an automated scheduled job.

Behavior:
- once per month, create `+1.0` PTO transaction for each active employee
- make it idempotent so it cannot duplicate if the job runs twice

This is important:
the accrual job must be safe to rerun.

20. My recommended v1 decisions

If I were locking this down now, I would set:

- No departments
- Everyone sees everyone’s leave and leave type
- Leave types:
  - Vacation
  - Paid Leave
  - Sick Leave
  - Urgent Leave
- Half day is a duration option, not a leave type
- PTO accrual = 1 day/month
- Vacation + Paid Leave consume PTO
- Sick + Urgent tracked separately for now
- Weekdays only
- No holiday exclusion yet
- Future requests allowed based on projected balance
- Max future booking window = configurable, default 6 months, optional for MVP

21. Remaining open questions

These are the only major things I’d still want confirmed before implementation:

1. Should Sick Leave consume PTO or not?
2. Should Urgent Leave consume PTO or not?
3. Should unused PTO expire at year end, or carry forward indefinitely?
4. Should new employees receive PTO immediately, or from the next month?
5. Do you want half day to support both:
   - morning
   - afternoon

I recommend yes for morning/afternoon.

My recommendation on question 3:
- for v1, let PTO carry forward
- add expiry/carry-over policy later only if needed

22. Best next step

You now have enough clarity to move from concept into build planning.

The best next thing I can do is one of these:

1. make you a concrete Laravel implementation backlog
2. design the exact database migrations and Eloquent models
3. design the page map and user flows
4. design the service classes and app structure for TALL + Filament

Best choice right now:
ask me for “the Laravel implementation backlog with tasks in order”.

If you want, I can do that next and make it very practical, like:
- install packages
- create migrations
- build services
- build Livewire pages
- build Filament resources
- add tests in the right order
