# Laravel taskboard acceptance scenarios

This is the separate final **Step 3** approval suite. Preparing this document or
passing `tests\run.php` / `tests\http.php` does not execute visual acceptance.
`composer test:acceptance` displays this guide; it does not claim a UAT pass.
Record each expectation as pass/fail/inconclusive with screenshot evidence, then
present the results for explicit user approval through the coordinator.

## Preconditions and repeatable launch

- Use only the explicitly approved, initially empty `taskboard_php84_test`
  database and restricted application account. Select it in ignored
  `config\database.local.php` **and** `DB_DATABASE`; verify `SELECT DATABASE()`
  matches exactly before writes. Do not clear unknown rows or touch the ordinary
  taskboard dataset. Keep the existing schema, SQL and CSS.
- Install the reviewed lock normally, enable the required PHP modules, and
  generate a local key in ignored `.env`. Never capture keys, credentials,
  session cookies or CSRF tokens in evidence. Sessions/cache must remain local
  files; timezone is UTC.
- Start the single Laravel app from this checkout on an unused approved
  loopback port, for example
  `php artisan serve --host=127.0.0.1 --port=8081 --no-reload`.
  Verify the **web child process**, not just the invoking CLI, loads cURL,
  Fileinfo, Mbstring, Zip, PDO MySQL and DOM. If PHP modules are supplied through
  `-d` flags, Artisan's bare child does not inherit them: use a process-local
  `PHP_INI_SCAN_DIR` overlay of already packaged modules, preserving DB_* and
  APP_KEY, or the documented equivalent direct Laravel router launch.
- Record a non-secret JSON launch record outside Git with `root` (absolute
  checkout), `base_url`, `database: "taskboard_php84_test"`,
  `verified_database: true`, and
  `extensions: ["curl","fileinfo","mbstring","zip","pdo_mysql","dom"]`.
  These are operator attestations: generate them only after inspecting the
  actual server command/environment and exact database identity. Set
  `TASKBOARD_HTTP_SERVER_PROOF` to this file (or pass `--server-proof=<file>`).
  The HTTP runner additionally authenticates the real encrypted Laravel session
  cookie against this checkout's key and matches its token to this checkout's
  session file **before writes**. It refuses an unapproved/default URL, database,
  populated table, missing launch record or mismatched session provenance.
- Check readiness, then run
  `php tests\http.php --base-url=http://127.0.0.1:8081` separately. It creates
  uniquely prefixed tasks, retains their IDs, and cleans only those fixtures.
  It does not create schema, disable middleware, use mocks, follow redirects
  implicitly, or replace the following visual scenarios.

For visual scenarios use the available Browser canvas's navigation, interaction,
viewport and screenshot actions. Inspect those capabilities first; do not install
a new browser/tool. Wait at most 10 seconds for each expected page state.
Use a unique run prefix for every created task, record its IDs, and clean only
those IDs. Keep screenshots in session artifacts, not Git.

## 1. Empty taskboard

Open `/` with the verified empty test table at a desktop viewport (for example
1280x900). Capture the page before creating fixtures.

Expected: Taskboard branding, "Your day, in order.", zero total/in-progress/
completed counts, "A fresh start.", and a usable labeled creation form. No
unexpected error, blank shell or obstructed content.

## 2. Unicode, escaped creation, overdue and reload

Create a uniquely prefixed title containing Unicode/emoji and quotes, a multiline
description containing `<script>not executable</script>` and `&`, high priority,
and yesterday's date relative to the verified UTC server date. Capture the notice
and populated task, then reload and capture it again.

Expected: title, multiline description, high priority and date persist; HTML-like
text is displayed literally and cannot execute; the incomplete task is marked
overdue. "Task added." appears once, not again on reload. Capture the corresponding
HTTP/DB persistence evidence separately; screenshots alone do not prove it.

## 3. Edit, complete, reopen, clear date, ordering and counts

Create a second prefixed task. Edit text/priority/date on the first, complete it,
reload, edit its metadata while completed, clear its date, then reopen it.
Capture edited, completed and reopened states.

Expected: "Task updated." and "Task status updated." appear once after the
respective writes. Values survive reload; editing does not reopen a completed
task. A cleared date disappears. Incomplete tasks precede completed ones and
newer IDs precede older IDs within each group. Global summary counts reflect the
two fixtures and their actual states.

## 4. Literal searches and filter retention

Give one fixture title `100% done_yet` with the run prefix and description
`Owner's REPORT`. Search `%`, `_`, `Owner's` and `report` separately, then combine
the matching search with active and completed filters. Open Edit, Cancel, save,
toggle and delete while filtered. Capture matching and empty-filtered states.

Expected: literal characters match only text containing them; search matches
title or description without ASCII case sensitivity. Search/status controls stay
selected, action/Cancel/redirect URLs keep q/status but drop edit, Clear returns
to `/`, and the selected list changes without narrowing global counts.
"No matching tasks." is distinct from the globally empty state.

## 5. Invalid values, delete, missing ID and HTTP contracts

Submit an empty or whitespace-only title, over-limit text, invalid priority, and
an impossible/out-of-range date through available browser interactions or the
HTTP runner where native controls prohibit submission. Capture inline errors,
then correct and submit the form. Delete only a selected run-owned fixture and
open an actually missing task ID.

Expected: inline errors preserve untrimmed entered text without writes; native
select/date controls may not visibly display an invalid raw value although the
HTML form model retains it. Correction succeeds. Delete removes only the chosen
fixture; missing tasks show the inline missing-task error with a reset editor.
Record HTTP evidence separately for 303 redirects, missing/mismatched scalar
CSRF 403 (including same-origin requests), non-string CSRF 422, validation 422,
missing IDs 404, and unsupported methods including HEAD/OPTIONS 405 with
`Allow: GET, POST`. A stale token fails without mutation and reload recovers.
Screenshots are not substitutes for these response/session/database checks.

## 6. Desktop and mobile usability

Repeat populated list, editor, filtered and inline-error states at desktop
1280x900 and mobile 390x844 (record actual dimensions). Capture each intended
state after interaction.

Expected: required labels, controls, task content and actions are visible and
usable; mobile uses the existing single-column/editor-first layout. No error
page, overlap, clipping or overlay prevents the required task interactions.
Evaluate usability, not pixel-perfect spacing or a subjective redesign.

## Evidence, evaluation and cleanup

For each group record URL/state, viewport, fixture IDs, screenshot references,
each supplied expectation and its pass/fail/inconclusive judgment. An
image-capable evaluator must cite visible evidence and identify missing evidence,
not infer unseen interactions. Confirmed failure takes precedence over
inconclusive; overall pass requires every expectation. Rerun affected scenarios
after a fix and preserve initial failures/recoveries.

Delete only recorded run-owned fixtures, verify exact database identity and zero
remaining rows, stop only this run's server process/tree, and remove temporary
ignored secrets/provenance/cookies when no longer needed. Keep the original app
on port 8000 and existing services untouched. Report results to the coordinator;
**explicit user acceptance is still required**, and is not publication approval.
