## Step 0: Baseline -> reproducible baseline

**Status:** done

**Source:** Framework-free, server-rendered PHP 8.4 taskboard with MySQL.

**Target:** Recorded starting contracts and best-effort baseline evidence, with
unavailable checks explicitly distinguished from failures.

**Plan context:** Created 2026-11-18 (UTC+08:00) for
`luxu-ms/test-php-add-laravel` at committed revision
`0b2b2d111b2f6585de011502a1ab46433b08f057`. Plan worktree:
`C:\source\github-copilot\copilot-worktrees\test-php-add-laravel\luxu-ms-fuzzy-winner`;
branch: `luxu-ms-php-readiness-and-plan`. No plan previously existed here.
The preceding assessment is recorded in `docs\php-x-to-y\readiness.md`.
At plan creation, all execution outcomes were pending; planning ran no builds,
tests, or servers. Step 0 execution evidence is recorded below.

**Destination and boundaries:** Adopt `laravel/framework:^13.0` in this existing
application, retain PHP `~8.4.0`, and lock the resolved packages in `composer.lock`.
Laravel's official support table lists PHP 8.3-8.5 for Laravel 13, with security
support through 2028-03-17. This is framework adoption, not a PHP upgrade.
Use one application and one public entry point throughout; do not generate a
replacement application, introduce traffic routing, or retain parallel old/new apps.
Keep PDO/raw SQL, the existing schema, single-user/no-login behavior, and static CSS.
Do not introduce Eloquent, schema migrations, an SPA, Vite/Node, authentication,
queues, external cache services, deployment changes, or unrelated hardening.

**Exact files/scope:** Read `composer.json`, `public\index.php`,
`src\Database.php`, `src\TaskRepository.php`, `config\database.php`,
`config\database.example.php`, `database\schema.sql`, `scripts\setup.php`,
`tests\run.php`, `views\tasks.php`, `public\styles.css`, and `README.md`.
Record execution evidence against this step in this plan or session artifacts.
Do not modify application files in Step 0.

**Known prerequisites and evidence boundaries:**

- The prior assessment found PHP 8.4.27 at
  `C:\Users\luxu\.copilot\session-state\fff568d2-f6a3-4d39-b958-e799e20b2828\files\php-runtime\php.exe`.
  Its module listing contains PDO MySQL and OpenSSL, but not cURL, Fileinfo, or
  Mbstring, which Laravel's documented server requirements include. Ask the user
  through the canvas parent for an approved PHP environment with these capabilities;
  do not install tools/extensions, edit a shared runtime, or bypass platform checks.
- Composer was not discoverable on PATH, and no local `composer.phar`,
  `composer.lock`, or `vendor\autoload.php` exists. This does not prove Composer
  is absent from the machine. Request an approved Composer 2 executable/path or
  approved execution environment; installation is user-managed.
- The database and running app are shared local resources, not authorized test
  fixtures. Obtain explicit permission and an identified dedicated, empty MySQL
  test database before any database connection, setup, repository test, or app
  request. A `_test` suffix alone is not authorization. Do not copy ignored local
  credentials from another checkout or print connection values.
- `tests\run.php:27-41` connects before its name guard and creates the table
  before beginning its rollback transaction. Schema creation persists. Existing
  rows must not be deleted to satisfy its empty-database assertion.
- No HTTP/browser suite, PHPUnit configuration, API/OpenAPI document, framework
  bootstrap, fake authentication, or alternative isolated integration was found.
  The prior eight-file syntax pass is parsing evidence, not a reproduced live app.

**Acceptance criteria:** Record applicable commands, actual results, failures,
unavailable checks, and their implications. Capture baseline contracts for CRUD,
search/filtering, status codes, form values, notices, counts, and rendering.
Completing these attempts and documenting the boundaries completes Step 0.
An unverified baseline alone does not block Step 1; actual execution prerequisites
still apply, and final build, integration, live-run, and UAT gates are not waived.

**Test plan:** Proposed commands, using the supplied/approved PHP executable as
`php` and approved Composer as `composer`: `php -v`, `php --ini`, `php -m`,
and `php -l` for each tracked PHP file. Run `php tests\run.php` only after
the dedicated database and its permissions are approved and its configuration
is securely supplied. No dependency restore is needed for the original application.
Record a withheld database run as unverified, not failed or passed.

**Live test and observable results:** Only with approved database access, start
the baseline on an unused loopback port using
`php -S 127.0.0.1:8081 -t public`. Confirm responsiveness, then observe an empty
list, create/edit/toggle/delete a uniquely named fixture, and reload to check
persistence and notices. Record request status codes and visible state, and clean
up only fixtures created for this run. Stop the specific server process afterward.
If authorization or prerequisites are missing, record the withheld check and why;
do not contact the existing running app. These are baseline observations, not
Laravel acceptance.

**Step 0 execution evidence (2026-11-18, UTC+08:00):**

Completed the best-effort baseline attempt in the dedicated worktree
`C:\source\github-copilot\copilot-worktrees\test-php-add-laravel\luxu-ms-urban-train`,
branch `luxu-ms-php-step-0-baseline`, app session
`221a5134-8a78-4125-91e1-bacf4c5e5613`. Starting HEAD was approved bootstrap
`c8b5b0b887d3fa545eb5c53a1e8ee5275be5452d` on application source
`0b2b2d111b2f6585de011502a1ab46433b08f057`; `git status --short` was empty.
Read all 12 files named in this step, the dated readiness snapshot, and the map
index/readiness articles on build/restore, test evidence, and integrations.
Only this plan's Step 0 status/evidence changed; application, configuration, tests,
CSS, schema, and `readiness.md` remain untouched. Steps 1-3 remain pending.

**Commands and actual results:** Commands ran from this worktree using PowerShell.
`$php` below denotes the approved executable, not a PATH-selected runtime:

```powershell
$php = 'C:\Users\luxu\.copilot\session-state\fff568d2-f6a3-4d39-b958-e799e20b2828\files\php-runtime\php.exe'
& $php -v
& $php --ini
& $php -m
git ls-files -- '*.php'
git ls-files -- '*.php' '*test*' '*phpunit*' '*lock*' '*composer*'
$files = @(git ls-files -- '*.php')
$failures = 0
foreach ($file in $files) {
    & $php -l ($file.Replace('/', '\'))
    if ($LASTEXITCODE -ne 0) { $failures++ }
}
& $php -d extension=curl -d extension=fileinfo -d extension=mbstring -m
Get-Command composer, composer.phar -CommandType Application -ErrorAction SilentlyContinue
Get-Content -LiteralPath .\composer.json -Raw | ConvertFrom-Json -ErrorAction Stop
```

- Version, INI discovery, default modules, and process-local modules each exited 0.
  CLI is PHP 8.4.27 NTS x64, Zend Engine 4.4.27, satisfying the original
  `~8.4.0` constraint. The loaded `php.ini` is beside the approved executable;
  no additional INI scan directory/files were reported.
- Default modules include PDO, `pdo_mysql`, OpenSSL, session, DOM, PCRE, and
  tokenizer; cURL, Fileinfo, and Mbstring are not loaded by default. The separate
  authorized `-d extension=curl -d extension=fileinfo -d extension=mbstring -m`
  invocation successfully lists all three. No runtime edits or installations
  occurred. Future Composer/Artisan/HTTP processes must use an approved invocation
  with required capabilities; this module probe is not a full Laravel platform
  check and says nothing about the existing web server's runtime.
- All eight tracked PHP files parsed: `config\database.example.php`,
  `config\database.php`, `public\index.php`, `scripts\setup.php`,
  `src\Database.php`, `src\TaskRepository.php`, `tests\run.php`, and
  `views\tasks.php`. Each lint exited 0; aggregate syntax failures: 0.
  No application assertions or database code executed through lint.
- Manifest JSON parsing succeeded. It declares only PHP/PDO/PDO MySQL,
  no third-party packages, and `start`, `setup`, `test` scripts; no dependency
  restore is needed for this baseline. JSON parsing is not Composer validation.
  Tracked-file discovery found only `tests\run.php` as a test runner, with no
  PHPUnit configuration or HTTP/browser runner.
- Composer PATH discovery returned no applications. Additional bounded,
  read-only `Test-Path -LiteralPath <candidate> -PathType Leaf` checks returned
  false for `%ProgramData%\ComposerSetup\bin\composer.bat` and `composer.phar`,
  `%APPDATA%\Composer\vendor\bin\composer.bat`,
  `%LOCALAPPDATA%\ComposerSetup\bin\composer.bat`, and this worktree's
  `composer.phar`, `composer.lock`, and `vendor\autoload.php`.
  This does not establish that Composer is absent elsewhere. Composer validation,
  dependency resolution/restore, and platform checks were not run; an approved
  Composer 2 path/environment is still needed for Step 1.
- `Test-Path -LiteralPath .\config\database.local.php -PathType Leaf` returned
  false. No ignored credential file from another checkout, environment values,
  raw connection strings, or secret-store contents were read or copied.
- No executed capability/syntax check failed. Composer was undiscovered in the
  bounded locations, rather than a failed Composer command. No source repair,
  runtime installation, dependency installation, setup hook, or service start
  was attempted.

**Baseline source contracts (inspection, not executed behavior):**

- **Persistence and CRUD:** `src\TaskRepository.php:13-116` uses prepared MySQL
  statements against `tasks`. Lists sort by `completed` ascending, then newest
  ID first. Create returns the inserted ID; schema defaults give incomplete,
  medium-priority tasks and a creation timestamp. Edit changes title,
  description, priority, and date without altering completion. Toggle changes
  `completed` to `1 - completed`; delete removes only the requested ID.
  Missing update/toggle/delete rows raise `OutOfBoundsException`; find returns
  null for an absent ID. Found-row semantics in `src\Database.php` preserve
  successful identical-value updates rather than treating them as missing.
  `database\schema.sql` requires MySQL-specific unsigned types, completion and
  priority constraints, InnoDB, and `utf8mb4_0900_as_ci`; it was read, not executed.
- **Values and validation:** `public\index.php:28-47,53-54,83-105` reads query and
  body fields separately. Missing/null fields become empty strings; non-string
  fields, including arrays, raise a 422 validation error. Task IDs must be positive
  integers; an empty ID creates only for `save`. Form defaults are empty
  title/description/date, null ID, and medium priority. Save captures submitted
  values before repository validation, so ordinary invalid save values redisplay
  untrimmed; persistence trims title/description. Title is 1-200 Unicode
  characters and description 0-5,000, validated as UTF-8 by PCRE with multiline
  content allowed. Priority must be exactly low/medium/high; an empty priority
  is invalid. Date must be empty or an exact valid `YYYY-MM-DD` in
  1000-01-01 through 9999-12-31; empty becomes SQL NULL. No global
  trim/empty-to-null request middleware exists. Invalid scalar select/date
  values remain in the form model, but native controls cannot necessarily display
  them as a valid selection/date. See `src\TaskRepository.php:53-94`.
- **Search/filter preservation:** `public\index.php:65-73` trims query `q` and
  defaults empty/missing `status` to all. Repository search permits at most 200
  Unicode characters and matches title OR description through
  `instr(lower(...), lower(?))`, not LIKE; `%`, `_`, and quotes are literal text.
  Status is exactly all/active/completed, AND-combined with search. Nonempty search
  and non-all status are encoded into `$listUrl`; edit links add only `edit`,
  write forms and 303 redirects retain filters but drop `edit`, Cancel retains
  filters, and Clear returns `/` (`views\tasks.php:37-52,63-67,87-92,103-129`).
  SQL matching/collation behavior still requires real MySQL verification.
- **HTTP and failure ordering:** With a usable database, `GET /` renders the
  list and optional `?edit=` form; `POST /` accepts only save/toggle/delete.
  Successful writes return 303 to the filtered list. Missing/mismatched scalar
  CSRF tokens return 403 with reload guidance; non-string token input raises
  422 through field validation. Invalid fields/actions/filter/ID return inline
  422; missing tasks return inline 404 and reset the editor. Unsupported methods,
  including HEAD/OPTIONS, reach 405 with `Allow: GET, POST`. Other throwables are
  logged and return a sanitized 500; unsupported PHP versions return 500
  (`public\index.php:8-21,63-143`). Crucially database connection and list/filter
  reads precede CSRF/method/action checks: those response expectations assume
  preceding operations succeed, not an isolated request-validation path.
- **Session and notices:** Native sessions use strict mode, HttpOnly,
  SameSite=Lax, and Secure when HTTPS is detected. A 32-byte random token encoded
  as hex is retained in the session and compared using `hash_equals` on POST.
  All write forms use the field `csrf`. Successful notices are exactly
  `Task added.`, `Task updated.`, `Task status updated.`, and `Task deleted.`;
  the next rendered response consumes the notice once
  (`public\index.php:16-21,50-51,76-80,107-113,145-146`).
  Early exit responses do not consume it.
- **Counts and rendering:** Summary total/in-progress/completed use the unfiltered
  list, while the list heading shows filtered count of total. Empty global data
  displays `A fresh start.`; an empty filtered result displays
  `No matching tasks.`. `views\tasks.php` renders Taskboard branding, a
  server-rendered list/editor, escaped text/attribute values using
  `htmlspecialchars(ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')`, and descriptions
  through `nl2br` after escaping. Completion changes labels/styles; priority
  badges and optional semantic due dates remain. An incomplete task is overdue
  only when its date precedes PHP's server-local `date('Y-m-d')`; no application
  timezone override is present. Notices use `role=status`, errors `role=alert`,
  and controls have labels. `/styles.css` is the only stylesheet; there is no
  JavaScript/frontend build. CSS uses a two-column desktop workspace and, at
  widths <=760px, one column with editor first and badge hidden
  (`views\tasks.php:1-137`, `public\styles.css:31,84-92`).
- **Database configuration boundary:** `config\database.php:5-41` merges
  defaults, optional ignored `database.local.php`, then present DB_* environment
  overrides, including empty values; PHP does not load `.env` automatically.
  No registry lookup exists. `src\Database.php` validates configuration, uses
  native prepares, associative/non-stringified fetches, UTF-8 and UTC connection
  initialization. Certificate verification is default; disabling it is permitted
  only for literal loopback addresses with a readable CA file. This is a source
  contract, not evidence that any actual credentials, TLS, or database work.

**Withheld/unverified checks and consequences:** Did not run
`php tests\run.php`, `composer test`, `php scripts\setup.php`, or `composer setup`;
did not start a server, invoke `composer start`, contact MySQL/the existing app,
or make HTTP/browser requests. The shared services are unauthorized, and no
approved dedicated empty test database/configuration or known isolated fixture
was supplied. `tests\run.php:27-41` connects before checking the `_test` suffix
and executes DDL before its transaction; rollback at lines 112-116 cannot make
that resource use safe. `scripts\setup.php:14-21` can create persistent schema.
No tests passed or failed because no assertions ran. Actual MySQL version/schema,
TLS/permissions, CRUD/reload persistence, search results, HTTP/status/session/CSRF
behavior, notices/counts, and visual/responsive rendering remain unverified.
There are no baseline screenshots or observed live status codes. Syntax and
source inspection are not successful application reproduction.

Step 0 is **done for the approved best-effort attempt**, not certification of a
working application. The unverified baseline alone does not block Step 1.
Process-local extension loading is available without shared-runtime edits, but
approved Composer remains undiscovered; database and live validation later still
require explicit resource authorization and safely supplied isolated configuration.
All later required validation and acceptance gates remain in force.

## Step 1: Manual includes -> Composer-managed Laravel bootstrap

**Status:** pending

**Source:** Dependency-free manifest, manual includes, and standalone PDO classes.

**Target:** Reproducible Laravel 13 dependencies, Artisan bootstrap, and lazy
container bindings in the same repository; the existing web entry point remains
unchanged until Step 2.

**Dependencies:** Step 0's evidence collection is best-effort; framework
dependency resolution requires the approved runtime and Composer capability.

**Exact files/scope:** Update `composer.json`, create `composer.lock`, `artisan`,
`bootstrap\app.php`, `bootstrap\providers.php`,
`app\Providers\AppServiceProvider.php`, `config\app.php`, `config\session.php`,
`config\cache.php`, `config\view.php`, and `.env.example`. Update `.gitignore`
and add only necessary keep/ignore files under `bootstrap\cache`,
`storage\framework\sessions`, `storage\framework\views`,
`storage\framework\cache`, and `storage\logs`. Update only autoload entry points
in `tests\run.php` and `scripts\setup.php`, and related commands in `README.md`.
Do not change `public\index.php`, task SQL, validation, or visual layout yet.

**Implementation boundaries:** Add only the framework and its actual platform
requirements; retain the plain PHP test-runner approach without adding a test
framework. Use PSR-4 for new `App` classes and Composer classmapping for existing
global classes in `src`; load `vendor\autoload.php` once in CLI entry points.
Bind `PDO` lazily through the existing `Database::connect()` and inject it into
`TaskRepository`. Bootstrapping Artisan must not open a database connection.
Preserve `config\database.php` and its local-file/environment precedence as the
existing PDO adapter configuration; do not overwrite it with Laravel's unrelated
connection-manager shape or start using the DB facade. No registry-backed
configuration was found, and none is to be introduced or replaced.

Configure file-backed sessions/cache, existing `views` as the PHP view path, UTC,
and writable local framework directories. Do not inherit skeleton defaults that
require database-backed sessions/cache or add tables. Keep database settings in
their existing sources; `.env.example` documents only necessary non-secret Laravel
settings. Generate the application key only into ignored, approved local settings
during execution; never place it in the plan or version control. Review Composer
scripts/plugins before resolution; do not add setup hooks that migrate, seed, start
services, or contact a database.

**Acceptance criteria:** The manifest and lock agree; an ordinary
`composer install --no-interaction --prefer-dist` can reproduce the selected
dependencies with their reviewed hooks. Existing classes autoload, Artisan boots
without database access, and the original repository assertions are retained.
No new app, new schema, or database access is required just to bootstrap.
Dependency installation and application correctness are separate outcomes.

**Test plan:** After approved tooling is available, resolve the new framework
constraint once, review and retain the generated lock, then run
`composer validate --strict`, `composer check-platform-reqs`, and syntax checks
for changed PHP files. Exercise autoload resolution without instantiating PDO.
Run `php tests\run.php` only against the approved isolated database to check the
autoload-only adjustment; do not require HTTP/UAT checks before HTTP conversion.

**Live test and observable results:** Run `php artisan --version` and
`php artisan list --raw` without database credentials/resource access.
Expect a Laravel Framework 13.x version and a successful command listing, with no
connection attempt or missing-provider/key/path failure. This is an in-process
framework-bootstrap check, not proof that the web application uses Laravel.

## Step 2: Procedural front controller -> Laravel-managed taskboard

**Status:** pending

**Source:** `public\index.php` handles routing, native sessions, input validation,
CSRF, repository calls, and direct template inclusion.

**Target:** The same taskboard served exclusively through Laravel's HTTP kernel,
web routes, container, sessions, responses, and PHP view engine.

**Dependencies:** Step 1's framework/bootstrap and approved configuration.

**Exact files/scope:** Replace the internals of `public\index.php` with the Laravel
front controller; add `routes\web.php`, `app\Http\Controllers\TaskController.php`,
`app\Http\Middleware\TaskCsrf.php`, and
`app\Http\Middleware\TaskResponseHeaders.php`. Adjust `bootstrap\app.php`,
`views\tasks.php`, `composer.json` scripts, and `README.md` for the one Laravel
startup path. Add `tests\http.php` and a focused
`tests\acceptance.md` scenario document; retain `tests\run.php` and its assertions.
Keep `public\styles.css`, `database\schema.sql`, and repository SQL unchanged.
Do not create Blade duplicates, an ORM model, a new setup/migration system, or
parallel legacy/framework routes.

**Implementation boundaries:** Extract the existing controller behavior rather
than rewriting domain validation. Use the existing PHP view through Laravel;
replace only the escaping/session integration needed to render it. Preserve
`GET /`, `?edit=`, `?q=`, `?status=`, and `POST /` with `save`, `toggle`, and
`delete` actions and the existing field names. Keep query/body sources distinct,
including invalid array inputs and empty string handling. Exclude this task
endpoint from Laravel's default trimming/null-conversion behavior where it would
alter the existing validation/form contract.

Use Laravel sessions for tokens/notices and response helpers for rendering and
303 redirects. Keep the existing `csrf` form field and require its session token
on every task POST through narrow application middleware replacing the default
forgery middleware for this web flow. Do not merely exempt the route or rely on
Laravel 13's same-origin shortcut. Keep missing/invalid-token responses at 403,
not Laravel's default 419, and test this on the live server with middleware active.
Do not disable protection in tests. Fresh page loads must issue usable tokens;
an expired/stale form must fail safely and recover after reload. Verify session
continuity and one-time notices across subsequent requests.

Preserve inline 422 validation with entered form data, 404 missing-task handling,
405 plus `Allow: GET, POST` for unsupported methods (including Laravel's normally
automatic HEAD/OPTIONS behavior), sanitized 500 responses with logged failures,
and existing response headers. Preserve Unicode limits, date limits, literal
percent/underscore search, completion ordering, overall summary counts, and
search/status selection after edit/write actions. Keep single-user access unchanged.
No additional authentication or cross-user isolation is part of this adoption.

**Acceptance criteria:** All task requests traverse Laravel; no controller
`session_start`, native `$_SESSION` workflow, or direct view include remains.
The existing PDO adapter retains prepared statements, found-row semantics,
UTC connection initialization, UTF-8 behavior, and its loopback-only certificate
verification exception/CA requirement. No schema or existing data changes are
needed. CSS and taskboard layout remain functionally unchanged. Documentation
describes the actual Laravel boot, approved key configuration, and test commands.

**Test plan:** Run manifest/platform checks only if dependencies changed, syntax
checks for touched PHP, `php artisan route:list --path=/`, and the repository
suite on the approved isolated MySQL database. Build `tests\http.php` in the
project's existing plain-PHP style, using PHP cURL and DOM capabilities to make
real requests, retain cookies/tokens, and assert responses. It must take an
explicit approved loopback base URL, identify its isolated test configuration
before writes, use unique fixtures, and delete only its own records. Add Composer
`test:http` and `test:acceptance` commands pointing to this repeatable runner;
do not silently redirect tests to the normal app or bypass database guards.
Cover CRUD, reload persistence, notices, filter combinations, 303/403/404/405/422,
escaped text, and field/Unicode/date boundaries without replacing MySQL with SQLite.
No blanket `migrate:fresh`, data clearing, or new production health/debug endpoint.

**Live test and observable results:** In the approved isolated environment, start
`php artisan serve --host=127.0.0.1 --port=8081` using the selected PHP runtime,
with an approved key and dedicated database configuration. Verify responsiveness;
run `php tests\http.php --base-url=http://127.0.0.1:8081`.
Observe HTTP 200 task rendering, a created task surviving reload, 303 writes,
403 invalid-token rejection without mutation, and 422 validation retaining inputs.
Failures must be logged/reported, not swallowed. Stop the exact server process
after checks. This establishes framework HTTP/integration behavior; screenshot
evaluation and approval remain Step 3 requirements.

## Step 3: Run AI-assisted user acceptance testing

**Status:** pending

**Source:** Laravel-managed taskboard with repository and HTTP regression checks;
no completed visual acceptance evidence.

**Target:** Repeatable, scenario-based acceptance evidence for the requested
framework adoption and behavior preservation, ready for user approval.

**Dependencies:** Step 2, approved runtime/key, approved isolated MySQL test
resource, and available browser navigation/interaction/screenshot capability.

**Exact files/scope:** Finalize and run `tests\acceptance.md` and `tests\http.php`;
update only directly related test corrections if needed. Record commands,
individual outcomes, environment identity without credentials, and screenshot
references in this plan/session artifacts. Do not commit screenshots, secrets,
generated sessions, or test data. Changes to behavior or scope require direction
through the canvas parent rather than adjusting expectations to manufacture a pass.

**Acceptance scenarios:** Define preconditions and expected results before capture.
Use isolated fixtures, a known server timezone, and a due date before that server's
current date for the overdue case.

1. Empty taskboard: branding, zero counts, intentional empty message, and usable
   creation form are visible.
2. Create and reload: Unicode title, multiline/HTML-looking description, high
   priority, and past due date persist; text is escaped and the active item is
   visibly overdue. Create/update notices appear once.
3. Edit, complete, and reopen: edited text/priority/date persist; completion survives
   reload, incomplete items sort before completed ones, overall counts are correct,
   and clearing the due date removes it without changing completion unexpectedly.
4. Search/filter preservation: search title and description, including literal
   `%`, `_`, and quotes; combine active/completed filters; verify selection and
   query survive edit/write/delete and summary counts remain global.
5. Validation and deletion: invalid title/date/priority preserves entered values
   with an inline error and no write; delete only the selected fixture; missing
   IDs yield 404. The HTTP runner separately verifies 403 CSRF, 405 methods,
   422 responses, and redirects because screenshots cannot prove those contracts.
6. Narrow viewport: repeat populated list/edit states at desktop and mobile widths;
   required labels, inputs, actions, errors, and task content remain visible and
   usable, without an obstructing layout or an unexpected error page.

**Test plan:** Run the final `composer install --no-interaction --prefer-dist`,
`composer validate --strict`, `composer check-platform-reqs`, changed-file syntax
checks, `php tests\run.php`, and
`php tests\http.php --base-url=http://127.0.0.1:8081` in the approved environment.
Preserve nonzero failures and unexecuted cases individually. These final commands
must succeed; Step 0's best-effort rule does not waive them. Then run the scenario
document independently as the acceptance gate, not just another name for fast tests.

**Live test and observable results:** Launch the single Laravel app on the
dedicated loopback port, verify readiness, then use the available built-in Browser
canvas for navigation, interactions, and screenshots. Its discovered actions
include `read_page`, `click_element`, `type_in_page`, and `screenshot_page`;
no Node/Playwright installation is required or presumed. If unavailable at execution,
inspect already-approved alternatives; do not install a browser or claim a preview
alone is evidence. Use a bounded wait (10 seconds per expected page state), record
the actual browser/viewport, and capture empty, populated/overdue, edited/error,
filtered, completed, and mobile states after the relevant interactions.

Give each screenshot, scenario, and explicit expectations to an image-capable
visual evaluator. Record pass/fail/inconclusive per expectation with visible
evidence; do not invent requirements or perform pixel/style matching. A screenshot
cannot establish persistence, status codes, or CSRF enforcement: retain the
corresponding HTTP/repository evidence separately. Missing screenshots or missing
database authorization leave affected checks inconclusive; any confirmed failure
prevents an overall pass. Rerun affected scenarios with fresh evidence after fixes.
Clean up only this run's fixtures and stop its specific app process.

**Acceptance criteria and approval gate:** Every required repository/HTTP assertion
and visual expectation passes, the app demonstrably runs through Laravel 13, and
the existing MySQL data/URL/form/UI contracts are preserved. Present results and
evidence to the canvas parent for user review and explicit acceptance before
declaring the transformation accepted. Do not commit, publish, or continue into
deployment as part of this plan; publication requires separate user approval.

**Planning references:** Relevant map guidance:
`web-frameworks\procedural-php-to-framework-knowledge.md`,
`core-php\manual-dependencies-to-composer-knowledge.md`, and
`testing\no-user-acceptance-testing-to-ai-assisted-user-acceptance-testing-knowledge.md`.
Official documentation consulted on 2026-11-18:
https://laravel.com/docs/13.x/releases,
https://laravel.com/docs/13.x/deployment,
https://laravel.com/docs/13.x/structure, and
https://laravel.com/docs/13.x/csrf.
