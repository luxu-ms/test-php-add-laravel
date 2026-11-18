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

**Status:** done

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

**Approved validation exception (2026-11-18):** The user chose to leave the
application license unspecified and accept only Composer's existing missing-license
warning. `composer validate --strict` must still run and its actual exit/warning
must be recorded; this is not an unqualified strict-validation pass. Manifest/lock
correctness and absence of any other warning/error remain mandatory. This narrow
exception also applies to the final Step 3 validation; no other gate is waived.

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

**Step 1 execution evidence (2026-11-18, UTC+08:00):**

Executed only this step in worktree
`C:\source\github-copilot\copilot-worktrees\test-php-add-laravel\luxu-ms-congenial-carnival`,
branch `luxu-ms-php-step-1-laravel-bootstrap`, app session
`2bae0f5f-396c-4e86-a99e-2a688b6bb42b`. Starting HEAD was verified as
`b1259566a11eb4a2f507d392b06ec9907b162400`; initial working status was clean.
Read this full plan, the unchanged dated readiness snapshot, and the map's
Composer/framework, build/restore, integration/test, and escalation guidance.
Step 0 remains done; Steps 2 and 3 remain pending.

**Implementation and scope:** Added `laravel/framework:^13.0`, retaining
`php:~8.4.0` and PDO/PDO MySQL; declared the needed cURL, Fileinfo, and Mbstring
capabilities. PSR-4 maps `App` to `app`; a classmap maps existing global `src`
classes. Added an Artisan-only bootstrap, provider list, and lazy provider
bindings: PDO calls the unchanged `Database::connect()` only on resolution;
`TaskRepository` receives that PDO. Added UTC app configuration, file sessions
and cache, the existing `views` path/PHP engine support, minimal writable
directory ignore files, and a non-secret `.env.example` with an empty key.
Updated only the manual-autoload requires in setup/tests and directly related
README commands. No new application key was needed or generated.

`public\index.php`, `views\tasks.php`, `public\styles.css`,
`database\schema.sql`, both existing `src` classes, `config\database.php`, and
`readiness.md` are unchanged. The public application is deliberately not yet
Laravel-managed. No routes, controller, template conversion, schema/migrations,
ORM usage, authentication, frontend tooling, deployment, or parallel app was added.
The framework's stock commands being listed is not permission to execute its
database, migration, queue, installer, or server commands.

**Dependency review, commands, and actual results:** All commands used PowerShell
from this worktree. The exact approved tool paths and shared arguments were:

```powershell
$php = 'C:\Users\luxu\.copilot\session-state\fff568d2-f6a3-4d39-b958-e799e20b2828\files\php-runtime\php.exe'
$composer = 'C:\Users\luxu\.copilot\session-state\fff568d2-f6a3-4d39-b958-e799e20b2828\files\composer\composer.phar'
$artifacts = 'C:\Users\luxu\.copilot\session-state\50a547e7-10b0-4e68-91d5-eed66197797f\files'
& $php -d extension=curl -d extension=fileinfo -d extension=mbstring $composer update --no-install --no-interaction --prefer-dist
& $php -d extension=curl -d extension=fileinfo -d extension=mbstring $composer install --no-interaction --prefer-dist
Test-Path -LiteralPath 'C:\Users\luxu\.copilot\session-state\fff568d2-f6a3-4d39-b958-e799e20b2828\files\php-runtime\ext\php_zip.dll' -PathType Leaf
Get-Command 7z,7za,unzip -CommandType Application -ErrorAction SilentlyContinue
& $php -d extension=curl -d extension=fileinfo -d extension=mbstring -d extension=zip --ri zip
& $php -d extension=curl -d extension=fileinfo -d extension=mbstring -d extension=zip $composer install --no-interaction --prefer-dist
& $php -d extension=curl -d extension=fileinfo -d extension=mbstring -d extension=zip $composer validate --strict --no-interaction
& $php -d extension=curl -d extension=fileinfo -d extension=mbstring -d extension=zip $composer check-platform-reqs
Get-FileHash .\composer.lock -Algorithm SHA256
```

- Before resolution, reviewed the root scripts: only the existing manual
  `start`, `setup`, and `test` commands; no automatic event hooks, plugins,
  private repositories, or database/setup hooks were added. Resolved once with
  `update --no-install`: exit 0, 74 production libraries, no development packages,
  Laravel Framework `v13.43.0`, and no reported vulnerability advisories.
- Before installation, parsed every locked package's name/type, requirements,
  scripts, and distribution/source hosts. All 74 are libraries; no
  `composer-plugin`/installer packages or package scripts are present. All
  distribution hosts are `api.github.com`, sources `github.com`.
- The first ordinary install exited 1 in `ZipDownloader.php:81`: "The zip
  extension and unzip/7z commands are both missing, skipping." The packaged
  `php_zip.dll` exists, while bounded PATH discovery found no 7z/7za/unzip.
  Consulted missing-tools/supported-alternatives guidance and obtained parent
  approval to load this existing module process-locally. `--ri zip` exited 0:
  Zip 1.22.8 / Libzip 1.12.1. Retried the same lock with `-d extension=zip`;
  ordinary install exited 0 and extracted all 74 packages with normal hooks
  enabled. No tool/extension installation, shared INI/PATH modification, platform
  bypass, graph re-resolution, or disabled script/plugin flags occurred.
- A second identical successful install exited 0: "Nothing to install, update
  or remove"; autoload generation succeeded. The reviewed lock SHA256 remained
  `93EBAD158B45BEDF2DE4CA17B1D79ECD78CD3FB538C2543D36442785865CF617`
  before/after both installs. This verifies lock agreement and repeatability in
  this worktree, not a separately provisioned clean machine.
- Strict validate exited **1**, stating the manifest is valid and reporting
  only: "No license specified, it is recommended to do so. For closed-source
  software you may use 'proprietary' as license." This warning existed before
  the changes. Parent relayed the user's explicit decision to leave it
  unspecified and accept only this warning. No license was invented; this is the
  approved exception, not strict exit-0 success.
- `check-platform-reqs` exited 0: all 18 reported requirements succeeded,
  including PHP 8.4.27, Composer runtime API 2.2.2, PDO/PDO MySQL, cURL, Fileinfo,
  OpenSSL, DOM and remaining transitive capabilities. Composer labels Ctype and
  Mbstring as supplied by polyfills; a separate native `extension_loaded`
  assertion confirmed cURL, Fileinfo, and Mbstring actually load with the selected
  process flags. These invocations do not verify a future web/child PHP runtime.

**Syntax, class resolution, and Artisan commands/results:**

```powershell
$files = @('artisan','bootstrap\app.php','bootstrap\providers.php','app\Providers\AppServiceProvider.php','config\app.php','config\session.php','config\cache.php','config\view.php','scripts\setup.php','tests\run.php')
$failures = 0
foreach ($file in $files) {
    & $php -d extension=curl -d extension=fileinfo -d extension=mbstring -l $file
    if ($LASTEXITCODE -ne 0) { $failures++ }
}
& $php -d extension=curl -d extension=fileinfo -d extension=mbstring -r 'foreach (["curl", "fileinfo", "mbstring"] as $extension) { if (!extension_loaded($extension)) { fwrite(STDERR, "Required native extension missing: " . $extension . PHP_EOL); exit(1); } echo $extension . " native extension loaded" . PHP_EOL; }'
& $php -d extension=curl -d extension=fileinfo -d extension=mbstring artisan --version
& $php -d extension=curl -d extension=fileinfo -d extension=mbstring artisan list --raw
& $php -d extension=curl -d extension=fileinfo -d extension=mbstring "$artifacts\step1-bootstrap-check.php" (Get-Location).Path
```

All ten touched PHP files parsed; aggregate syntax failures 0. Both direct
Artisan commands exited 0: `Laravel Framework 13.43.0` and the normal nonempty
command list. The session-only bootstrap checker (no fixture/substitute) resolved
`Database`, `TaskRepository`, and `App\Providers\AppServiceProvider` classes without
construction, booted the real console kernel, and ran its raw command listing.
Guards fail if PDO, TaskRepository, `db.connection`, or `db.schema` resolve.
Both application bindings stayed unresolved; the stock framework database manager
had zero connections. UTC/runtime timezone, actual file session/cache handlers,
the existing `tasks.php` path, and the PHP view engine were verified.
Final checker exit 0, after the temporary credential file had been removed.

Two diagnostic checker failures were corrected in the checker only, not hidden:
the initial overbroad guard rejected stock `db` manager registration, which
`DatabaseServiceProvider::boot()` uses without opening a connection; inspection
confirmed lazy connection creation and the final check instead guards connection
resolution plus asserts the manager's connection list is empty. Its initial
literal view-path comparison also rejected Windows separator normalization;
canonical paths now compare equal. Neither failure required an application
change or execution of a database connection.

**Approved isolated repository test and resource guards:**

During this step, parent relayed explicit user approval for exactly
`taskboard_php84_test` on the existing local MySQL, using only the restricted
application settings from the approved ignored file
`C:\source\github-copilot\copilot-worktrees\test-php-add-laravel\luxu-ms-congenial-couscous\config\database.local.php`.
No other ignored files or credentials were accessed. No connection values were
printed, logged, copied into evidence, or committed. Before writing settings, the
new worktree's temporary ignored `config\database.local.php` had inheritance
removed and its ACL restricted to the current user and SYSTEM; protection and
exactly two access rules were checked, and `git check-ignore --quiet` exited 0.
The generated configuration and `DB_DATABASE` explicitly selected the approved
test name; remaining DB environment overrides were set process-locally from the
approved application settings, preserving the existing TLS behavior.

```powershell
$env:DB_DATABASE = 'taskboard_php84_test'
& $php -d extension=curl -d extension=fileinfo -d extension=mbstring "$artifacts\step1-isolated-db-check.php" (Get-Location).Path
$local = Join-Path (Get-Location).Path 'config\database.local.php'
Remove-Item -LiteralPath $local
```

The session-only wrapper used the unchanged `Database::connect()`, required
`SELECT DATABASE()` to equal **exactly** `taskboard_php84_test` before mutation,
and checked all base tables in that selected schema. Only the existing `tasks`
table was present, with zero rows. No unknown tables/data were cleared. Then it
required the actual `tests\run.php` and all retained assertions executed:
**"All task repository tests passed."** Post-run exact database identity and
`SELECT COUNT(*) FROM tasks = 0` both passed after rollback; the existing test
table was retained. Wrapper exit 0. No setup command or Laravel database command
ran. The generated credential file was removed immediately afterward; final
DB-free bootstrap checks ran without it. Only this explicitly approved test
database was contacted; the live dataset and existing app/services were untouched.

**Remaining gaps and outcome:** Step 1 satisfies its revised approved criteria,
including the original repository suite under the later explicit resource
authorization. No HTTP request, app server, browser, screenshot, middleware/CSRF
integration, or visual acceptance check ran; those still require Steps 2 and 3.
Artisan CLI success does not mean the web entry point uses Laravel or prove the
web runtime inherits process-local extension flags. No shared/live data was used
as evidence, no generated cache/vendor files or secrets are committed, and the
dated readiness snapshot remains unchanged. Only a local Step 1 commit is
authorized; no push or pull request is part of this step.

## Step 2: Procedural front controller -> Laravel-managed taskboard

**Status:** done

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

**Step 2 execution evidence (2026-11-18, UTC+08:00):**

Executed only Step 2 in app session
`d5477c98-8d6b-4079-a479-978fb7de8c8a`, worktree
`C:\source\github-copilot\copilot-worktrees\test-php-add-laravel\luxu-ms-special-funicular`,
branch `luxu-ms-php-step-2-laravel-http`. Verified starting HEAD
`a26d332fda74d39fc1f8a39a159f6694309412c4` and a clean working tree before edits.
Read this plan, its detailed Step 0 contracts/Step 1 evidence, the unchanged dated
readiness snapshot, and the map's framework, Composer, acceptance, integration,
test-evidence and missing-tools/supported-alternatives guidance. Step 0/1 evidence
is historical and unchanged. Step 3 is still pending.

**Implementation and boundaries:** `public\index.php` now enters Laravel's real
HTTP kernel. The single root web route invokes the container-injected
`TaskController` using the existing `TaskRepository`/PDO bindings, and renders the
existing `tasks.php` through Laravel's PHP view engine. The view's only change is
the equivalent local escaping closure; no duplicate Blade template or layout/CSS
change was made. Native controller sessions, manual class includes, and direct
template includes were removed. Request body/query bags remain separate; the
front controller preserves the original form-only body source and disables
method overrides. Root requests skip Laravel's trimming/empty-to-null middleware.

`TaskCsrf` replaces the default root web forgery middleware, checks the existing
`csrf` field against Laravel's actual session token on every POST, and does not
trust same-origin headers. Its fail-closed rejection is consumed by the controller
before any write, after the original list/filter reads; this preserves validation
ordering, including non-string CSRF 422 versus missing/mismatched scalar 403.
It is not a CSRF exemption or disabled-test path. Session notices are explicitly
consumed once on rendered pages, not by early 303/403/405 responses. The controller
retains the domain validation and raw invalid-form model, exact notices, filtered
URLs, relative 303 Location, missing-task 404/reset editor and 405/Allow contract.
The exception handler logs failures through PHP's error log and returns the
sanitized generic 500 with the existing security/content headers.

**Small directly coupled configuration fix:** The first route listing exposed
Laravel 13's inherited filesystem `local.serve=true`, which automatically added
GET/HEAD and PUT `storage/{path}` routes. This is not a taskboard capability.
Added only `config\filesystems.php`'s local driver/root/`serve=false` override;
subsequent route listings show exactly the root task route. The initial overly
small override containing only `serve=false` failed with "Undefined array key
driver" because the local disk array is replaced, not recursively merged at that
level; retaining its driver/root corrected that failure. No storage endpoints,
uploads, new infrastructure or general hardening were added.

Added guarded plain-PHP/cURL/DOM `tests\http.php`, the distinct six-group
`tests\acceptance.md` final scenario suite, and Composer `start`, `test:http` and
`test:acceptance` entry points. `test:acceptance` displays the scenario guide,
explicitly not a UAT result. README documents the actual Laravel boot, local key,
child-runtime requirements, isolated runner and separate visual gate.
`src\Database.php`, `src\TaskRepository.php`, their SQL/validation, schema,
`public\styles.css`, the original `tests\run.php` and all its assertions,
`composer.lock`, and `readiness.md` are unchanged (`git diff --exit-code`).
No ORM, migration, authentication, parallel app, SPA/build tooling, deployment,
dependency update, tool install, shared INI/PATH change, push or PR occurred.

**Exact tools and commands:** Commands ran in PowerShell from this worktree.
The following variables abbreviate the actual approved paths/flags:

```powershell
$php = 'C:\Users\luxu\.copilot\session-state\fff568d2-f6a3-4d39-b958-e799e20b2828\files\php-runtime\php.exe'
$composer = 'C:\Users\luxu\.copilot\session-state\fff568d2-f6a3-4d39-b958-e799e20b2828\files\composer\composer.phar'
$phpArgs = @('-d','extension=curl','-d','extension=fileinfo','-d','extension=mbstring','-d','extension=zip')
$artifacts = 'C:\Users\luxu\.copilot\session-state\73d310d6-e4d0-4386-9122-43035a260739\files'
& $php @phpArgs $composer install --no-interaction --prefer-dist
& $php @phpArgs $composer validate --strict --no-interaction
& $php @phpArgs $composer check-platform-reqs
& $php @phpArgs artisan route:list --path=/
& $php @phpArgs artisan route:list --path=/ -vv
$files = @('public\index.php','routes\web.php','bootstrap\app.php',
    'views\tasks.php','app\Http\Controllers\TaskController.php',
    'app\Http\Middleware\TaskCsrf.php','app\Http\Middleware\TaskResponseHeaders.php',
    'config\filesystems.php','tests\http.php')
foreach ($file in $files) { & $php @phpArgs -l $file }
git hash-object composer.lock
git rev-parse HEAD:composer.lock
git diff --check
git diff --exit-code -- src\Database.php src\TaskRepository.php database\schema.sql public\styles.css tests\run.php docs\php-x-to-y\readiness.md composer.lock
```

Ordinary restore exited 0, installing the same 74 reviewed locked libraries with
normal hooks and no re-resolution. All 18 platform requirements succeeded.
Strict validation ran twice, each time exit **1 with only the explicitly approved
unspecified-license warning**; neither run is claimed as a strict exit-0 pass.
No other Composer warning/error was accepted. Working/HEAD lock Git object IDs
both equal `32e2ac8a66f1135186a1bd6586db0693e4ad15f3`. All nine touched PHP
files parsed; the final middleware/runner edits were linted again. Whitespace
checks passed. Final route listing exits 0 with exactly the root task controller
route, including the web middleware group; no database/key is needed for listing.

**Isolated configuration and launch:** Created this worktree's ignored
`config\database.local.php` and `.env` as empty files, removed inherited ACLs and
verified protection plus exactly current-user/SYSTEM access before writing any
secrets. `git check-ignore --quiet` passed for both. The session-only
`step2-environment.php` reads only the explicitly approved restricted source
settings from
`C:\source\github-copilot\copilot-worktrees\test-php-add-laravel\luxu-ms-congenial-couscous\config\database.local.php`,
selects `taskboard_php84_test` in the generated configuration and `DB_DATABASE`,
and sets the other approved DB_* overrides process-locally. A random key is
generated only into that ignored restricted `.env`; no secret is printed or
stored in Git/evidence. Before mutation or live checks, `SELECT DATABASE()`
equaled exactly `taskboard_php84_test`, with only the existing `tasks` table and
zero rows. No unknown tables/data were removed.

An independent route-list command accidentally raced the brief empty placeholder
during secure configuration generation and failed "database.local.php must return
an array." It made no database connection; rerunning after generation completed
passed. This was an orchestration failure, not repaired by weakening validation.

```powershell
$env:DB_DATABASE = 'taskboard_php84_test'
& $php @phpArgs "$artifacts\step2-environment.php" prepare
& $php @phpArgs "$artifacts\step2-environment.php" repository
# In the dedicated attached server shell only:
$env:PHP_INI_SCAN_DIR = "$artifacts\step2-runtime"
& $php "$artifacts\step2-environment.php" serve
```

The serve mode calls the real application's
`handleCommand(new ArgvInput(['artisan','serve','--host=127.0.0.1','--port=8081','--no-reload']))`
with the approved DB environment retained. The isolated INI overlay loads only
the four packaged modules missing from the shared INI; no duplicate module flags
are used on that overlay-backed invocation. The actual Artisan web child,
not merely its parent CLI, ran as `cli-server`, PID **55840**, on the previously
unused `127.0.0.1:8081`. The session-only auto-prepend guard verifies cURL,
Fileinfo, Mbstring, Zip, PDO MySQL and DOM, explicit DB_DATABASE and actual
SELECT DATABASE() in that child before each application request. It records
only non-secret runtime provenance; it neither replaces the application nor
creates a debug endpoint. Actual readiness returned HTTP 200 and the intentional
empty taskboard. The key is loaded from this worktree's `.env`, never dropped
or replaced by launching the child. The documented direct-router alternative
was not needed or executed; all HTTP evidence below used Artisan's real child.

**Repository/HTTP execution, failures and recoveries:**

```powershell
$env:TASKBOARD_HTTP_SERVER_PROOF = "$artifacts\step2-server-proof.json"
& $php @phpArgs "$artifacts\step2-environment.php" http
& $php @phpArgs "$artifacts\step2-environment.php" guard-negative
& $php @phpArgs "$artifacts\step2-environment.php" composer-http
& $php @phpArgs "$artifacts\step2-environment.php" composer-acceptance
& $php @phpArgs tests\http.php
& $php @phpArgs tests\http.php --base-url=http://127.0.0.1:8000
& $php @phpArgs "$artifacts\step2-environment.php" repository
```

The `http` wrapper establishes the exact empty-database guard then invokes the
normal committed command with the approved PHP/module flags:
`php tests\http.php --base-url=http://127.0.0.1:8081`.
It does not mock middleware or responses. Before its first write the runner
checks explicit URL/database/launch provenance, decrypts the real Laravel session
cookie using this checkout's key and matches the actual form token against this
checkout's file session. Its cURL cookie jar, requests and DOM assertions exercise
the active real server, with automatic redirect following disabled.

- Both original repository runs exited 0: **"All task repository tests passed."**
  No assertions changed or were omitted; that runner does not emit a numeric
  assertion count. Each run was preceded by exact identity/table/empty guards
  and followed by a zero-row rollback check.
- Two preliminary HTTP runs failed at the redirect assertion: Laravel's URL
  generator emitted `http://127.0.0.1:8081` instead of the original relative `/`.
  The second run exposed that actual Location diagnostically. Returned an
  explicit Laravel 303 response with the original relative Location; retained
  strict filter/edit assertions rather than loosening the behavior. Each failed
  run cleaned its uniquely prefixed fixture and verified zero remaining rows.
- The corrected initial suite passed **763 assertions**. Expanded stale-session,
  notice-ordering, body/query, invalid-model and deletion checks passed
  **817 assertions / 131 requests**, then the same expanded suite passed again
  through the actual Composer `test:http` script. The Composer child used the
  inherited isolated INI overlay; no module/platform bypass was used.
- Extra live `PROPFIND`/`COPY` probes exposed Laravel's default 405 Allow list
  (`GET, HEAD, POST, PUT, PATCH, DELETE, OPTIONS`), unlike the original contract.
  Added narrow root method-exception rendering and those cases to the runner.
  The **final complete HTTP run exited 0: 836 assertions / 133 actual requests**.
  Status counts were **200:49, 303:17, 403:11, 404:4, 405:10, 422:42**.
  This includes exact plain failure bodies and `Allow: GET, POST` for
  HEAD/OPTIONS/PUT/PATCH/DELETE/TRACE/CONNECT/PROPFIND/COPY, same-origin invalid
  tokens, non-string CSRF 422, expired-session cookie/reload recovery, no method
  override/JSON-form semantics, CRUD/reloads, notice consumption, ordering,
  completion-preserving edits/date clear, literal searches/combined filters,
  global counts, escaping, Unicode and exact date/priority/input limits.
- Negative guards produced the expected exit 1 for no explicit URL, port 8000,
  and a populated test table. For the last case the session helper first verified
  emptiness and created one identifiable owned guard fixture; the runner refused
  before HTTP/mutation and left it untouched. The helper removed only that exact
  ID and verified emptiness. These are expected safety refusals, not passing
  application requests or waived errors.
- Composer `test:acceptance` exited 0 and displayed the separate six-group guide
  with an explicit "not an executed or approved UAT result" message. No visual
  scenario, screenshot evaluation or final user approval ran.

**Sanitized 500/logging evidence:** After the normal web-child exact-database
guard, a temporary session-artifact fault flag set only that request's DB_PORT
to an invalid non-secret string. The real Laravel/PDO binding then failed the
unchanged configuration validator, before opening another database connection.
`Invoke-WebRequest -Uri http://127.0.0.1:8081/ -SkipHttpErrorCheck -TimeoutSec 10`
returned 500 with exactly the original sanitized unavailability message,
nosniff and CSP; no exception/connection data leaked in the response.
The actual server log contains
`RuntimeException: Invalid MySQL host, port, database name, or username.`
at `src\Database.php:21`. Removing the flag restored HTTP 200 on the same server;
the final full HTTP/repository runs above followed this recovery. The fault was
test-only process configuration, not an application endpoint or substituted DB.
No validation is declared independent of unavailable database/list reads.

**Cleanup and remaining gate:** Every HTTP run cleaned only its unique
run-prefixed/recorded IDs; final exact database guard again found only `tasks`
with **zero rows**. Stopped the exact attached `step2-laravel-server` process
tree; verified PID 55840 gone and port 8081 no longer listening. Removed the
ignored local credential/key files, temporary launch proof, fault flag, cookie
jars and generated framework/session files. Existing application port 8000,
other services, databases and the ordinary eight-task dataset were not contacted
or changed. Final database-free `artisan route:list --path=/` still passed after
the credential/key files were removed. Only approved source/tests/docs/this
Step 2 evidence are included in the local Step 2 commit.

Step 2 is **done for actual Laravel HTTP integration**, not final visual or user
acceptance. The six scenario groups are prepared but unexecuted; **Step 3 remains
pending** and requires a new approved session, real screenshots/evaluation and
explicit user acceptance. No push, pull request or deployment was performed.

## Step 3: Run AI-assisted user acceptance testing

**Status:** done

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
must succeed except for only the explicitly accepted missing-license warning
described in Step 1, whose actual strict exit/warning must still be recorded;
Step 0's best-effort rule does not waive any other requirement. Then run the scenario
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
deployment without authorization. The approved Step 3 assignment authorizes local
verified progress/results commits only; publication requires separate user approval.

**Step 3 execution evidence (2026-11-18, UTC+08:00):**

Executed only the final acceptance step in app session
`abda7cdc-944d-4480-b569-f99e9c9f6abb`, branch
`luxu-ms-php-step-3-acceptance`, starting at verified Step 2 commit
`dc222b8916d53ca1ce25bc3fed36fd2b4ed2c049`. The initial working tree was clean.
The **latest plan root** is this worktree:
`C:\source\github-copilot\copilot-worktrees\test-php-add-laravel\luxu-ms-scaling-disco\docs\php-x-to-y\plan.md`.
Earlier worktree references above are historical evidence, not alternate progress
roots. Read the full committed plan, both acceptance/HTTP runners, the map index,
mandatory AI-assisted UAT article and supported-alternatives guidance.

Only this plan and `tests\acceptance.md` changed. The guide now explicitly handles
missing viewport actions, installed-browser alternatives, true viewport versus
full-page captures, fresh-document waits, and the limits of screenshot evidence.
Its six scenario expectations were fixed before capture and were not weakened.
`tests\http.php` needed no correction. No application, CSS, schema, SQL, manifest,
lock, original repository assertions, or dated readiness snapshot changed.

**Tools, exact commands and final checks:** Commands ran from this worktree with
PowerShell; the following variables abbreviate the actual paths:

```powershell
$php = 'C:\Users\luxu\.copilot\session-state\fff568d2-f6a3-4d39-b958-e799e20b2828\files\php-runtime\php.exe'
$composer = 'C:\Users\luxu\.copilot\session-state\fff568d2-f6a3-4d39-b958-e799e20b2828\files\composer\composer.phar'
$a = @('-d','extension=curl','-d','extension=fileinfo','-d','extension=mbstring','-d','extension=zip')
$artifacts = 'C:\Users\luxu\.copilot\session-state\5315fc23-7c3b-4f89-9031-74e19ec4e8b1\files'
& $php @a $composer install --no-interaction --prefer-dist
& $php @a $composer validate --strict --no-interaction
& $php @a $composer check-platform-reqs
$files = @(git diff --name-only 0b2b2d111b2f6585de011502a1ab46433b08f057 HEAD -- '*.php')
foreach ($file in $files) { & $php @a -l ($file.Replace('/', '\')) }
& $php @a -l artisan
$env:DB_DATABASE = 'taskboard_php84_test'
& $php @a "$artifacts\step3-environment.php" prepare
& $php @a "$artifacts\step3-environment.php" repository
# Dedicated attached server shell; no shared PHP/PATH changes:
$env:PHP_INI_SCAN_DIR = "$artifacts\step3-runtime"
& $php "$artifacts\step3-environment.php" serve
# Separate ordinary module-flagged CLI shell:
& $php @a "$artifacts\step3-environment.php" http
# The wrapper launches this exact committed runner after its empty-DB guard:
& $php @a tests\http.php --base-url=http://127.0.0.1:8081 --server-proof="$artifacts\step3-server-proof.json"
& $php @a artisan route:list --path=/ -vv
git hash-object composer.lock
git diff --check
```

Ordinary restore exited 0: the same 74 locked libraries, no updates/removals,
normal hooks and no platform bypass. Lock Git object remained
`32e2ac8a66f1135186a1bd6586db0693e4ad15f3`. Strict validation exited **1 with only
the explicitly approved unspecified-license warning**; this is not strict exit-0
success. All 18 platform requirements succeeded. All 17 transformation-changed
PHP files plus `artisan` parsed (18 files, zero syntax failures). Route listing
showed exactly the task root route and active `web` middleware group.

Both original repository runs passed with all retained assertions, exact
database/empty-table guards before execution, and zero rows after rollback.
The runner does not emit a numeric assertion count. The first and final complete
HTTP runs each passed **836 assertions / 133 requests** through the real Laravel
entry point, with status counts **200:49, 303:17, 403:11, 404:4, 405:10, 422:42**.
Actual cookie decryption and token/file-session matching authenticated this
checkout before writes; middleware/CSRF protection was never disabled.

A repeat after visual UAT hit a single **10.008-second transport timeout** during
the Unicode/invalid-value phase; the runner still cleaned its exact owned rows
and verified zero remaining rows. No application error or root cause was
established. Readiness immediately returned 200/empty on the same server;
the complete unchanged HTTP suite then passed again with the full counts above.
The timeout is retained as a failed intermediate attempt, not hidden or waived.
No retry timeout was increased and no expectation relaxed.

**Isolated environment and provenance:** Only the approved restricted application
settings source was reused, from the explicitly authorized
`luxu-ms-congenial-couscous\config\database.local.php` checkout. Created this
worktree's ignored local configuration/key placeholders with inheritance removed
and exactly current-user/SYSTEM ACL rules, verified protection/ignore status, then
wrote the settings. Both local configuration and `DB_DATABASE` selected exactly
`taskboard_php84_test`. Before mutation/live access, actual `SELECT DATABASE()`
matched that exact name, only the existing `tasks` table existed, and it had zero
rows. No unknown tables/data were cleared and no schema/setup command was added.

The session-only `step3-environment.php` launches the real application's Artisan
`serve --host=127.0.0.1 --port=8081 --no-reload` command. Its isolated INI overlay
loads the four already packaged modules. The actual `cli-server` child PID
**75884** ran the session-only auto-prepend guard before application requests,
verifying native cURL/Fileinfo/Mbstring/Zip/PDO MySQL/DOM, explicit DB_DATABASE and
actual database identity. A fresh non-secret launch record named this exact root,
URL, database and child. Readiness returned 200/intentional empty state.
HTTP checks independently verified UTC and the real session/key provenance.
No stale Step 2 helper path/proof, substitute server, debug endpoint, stub, SQLite,
disabled protections, shared INI change or tool installation was used.

**Distinct browser acceptance, not another HTTP-suite alias:** Inspected Browser
canvas capabilities and opened/read/captured the actual empty app in
`step3-taskboard-review`. Canvas has no viewport-setting action. Used the existing
`C:\Program Files\Google\Chrome\Application\chrome.exe`, version
**134.0.6998.178**, headless, in a new ACL-restricted session-only profile with
loopback DevTools port 9223, not the user's normal browser profile. A session-only
Python standard-library CDP helper performed actual browser typing, native form
submissions/navigation, select/date input, reloads and screenshots. Later clicks
were coordinate-dispatched after scroll/hit testing, not hidden-control clicks.
No package/browser/extension installation, application JavaScript or Node tooling
was added. Exact `innerWidth`/`innerHeight` were **1280x900** and **390x844**, DPR 1;
paired full-page captures retain that viewport rather than simulate mobile by CSS.
All expected-state waits remained bounded at 10 seconds.

Executed the six groups separately with
`python "$artifacts\step3-browser.py" 1` through `6`; there are **69 final
interaction/state checks** (3/6/15/25/8/12). The guide-display command is not
counted as UAT. Group 1/2 were repeated from verified emptiness for complete
below-fold screenshots. Visual fixtures used prefix `uat-abda7cdc-` and IDs
**73, 74** (lifecycle/search/delete), **75** (Unicode recapture) and **76**
(responsive checks); the HTTP runner used its own unique fixtures.
Sanitized direct PDO reads verified the Unicode title, literal multiline
description, high priority, completion=0 and overdue date **2026-11-17** for both
creation captures. Browser form submissions normalize newlines to CRLF.

Screenshot/evaluation artifacts are retained outside Git under `$artifacts`.
Each PNG has a same-name JSON recording browser, true viewport, URL, summary,
form values and visible task state, without cookies/tokens/keys. `-full.png`
files supplement the same-name 1280x900 or 390x844 viewport PNGs. The
image-capable coding model inspected the actual images against the unchanged
expectations, not CSS alone; detailed per-expectation judgments are in
`step3-acceptance-evidence.md`.

| Group | Result and supporting visible evidence |
|---|---|
| 1. Empty desktop | **Pass.** `g1-empty-desktop[-full].png`: Taskboard branding, Your day/in order, 0/0/0, fresh start and all labeled creation controls. |
| 2. Create/reload/overdue | **Pass.** `g2-created-overdue-desktop[-full].png`, `g2-reloaded-overdue-desktop[-full].png`: quoted Unicode/emoji/Japanese title, separate description lines, literal script-like text and ampersand, High/In progress/Overdue 2026-11-17; notice present then absent. Persistence/escaping/once-only behavior also have independent DB/browser/HTTP evidence. |
| 3. Lifecycle/order/counts | **Pass.** `g3-edited-desktop-full.png`, `g3-completed-desktop-full.png`, `g3-completed-editor-desktop-full.png`, `g3-completed-date-cleared-desktop-full.png`, `g3-reopened-desktop-full.png`: edited low priority/leap date, incomplete above completed, 2/1/1, completed metadata retained with no date, then 2/2/0 reopened. |
| 4. Search/filter/actions | **Pass.** `g4-search-{percent,underscore,quote,report}-full.png`, `g4-filtered-{active,editor,empty,completed,deleted}-desktop-full.png`: literal and case-insensitive description search, selected filters, populated/completed or no-match list, global counts; browser/HTTP evidence verifies q/status preserved and edit dropped on Cancel/save/toggle/delete, Clear returns root. |
| 5. Invalid/correct/delete/missing | **Pass.** `g5-inline-error-desktop-full.png`, `g5-corrected-desktop-full.png`, `g5-missing-id-desktop-full.png`, `g5-deleted-empty-desktop-full.png`: inline validation with retained text, corrected task, missing-task error/reset editor, final fresh start. Native-prohibited limits/priority/date inputs and 303/403/non-string-CSRF-422/404/405/422/no-write contracts passed in the unchanged HTTP suite, not inferred from screenshots. |
| 6. Desktop/mobile usability | **Pass.** `g6-{populated,editor,filtered,error}-{desktop,mobile}[-full].png`: labels/controls/content/actions fit their columns, actual mobile editor-first single-column order, readable error/filter/task states; vertical scrolling exposes below-fold content with no overlap/horizontal obstruction. Hit-tested clicks, edits, filter, error submission and exact-ID deletion succeeded. |

Two **capture-helper** failures were preserved and corrected without application
changes: Group 4 initially inspected the old document after changing filter
controls, before navigation settled. Its saved assertion failure and
`g4-attempt1-settled-completed[-full].png` show the correctly completed result
after load; the helper now requires a new `performance.timeOrigin` plus document
readiness. Restored only the owned task's active precondition and reran the entire
group, all 25 checks passing. Group 5's optional missing Cancel serialized as an
omitted JavaScript `undefined` property, causing a Python KeyError; normalized
that absent snapshot field to null and reran all eight unchanged expectations.
`g4-attempt1-results.json` and `g5-attempt1-results.json` preserve those attempts.
There was no confirmed application regression and no scope expansion.

**Cleanup and approval:** Each run deleted only its recorded/prefixed owned
fixtures. IDs 73/74/76 were deleted through actual browser actions; ID 75 alone
was removed by exact ID plus prefix after the recapture. Final exact database
checks found only `tasks` and **zero rows**, including after final HTTP/repository
runs. Stopped the exact attached Laravel process tree; verified web PID 75884 gone
and port 8081 closed. Stopped only isolated Chrome PID **39524** and its ten
owned descendants; port 9223 closed. Removed this run's restricted browser
profile/cookies, ignored local configuration/key, temporary launch proof and 14
generated ignored session/cache/view files. Kept non-secret scripts, results,
metadata and **60 screenshots** (including the preserved timing investigation).
Port 8000, other services/databases and the normal eight-task dataset were not
contacted or changed. No push, PR, publication or deployment occurred.

All required final execution/visual expectations now pass, with intermediate
failures/recoveries disclosed above and the narrow license exception unchanged.
The initial evidence commit `5ae864cc03fe76791e5d6098a52c6d921b0e33f5`
correctly left Step 3 pending explicit user acceptance at that time.

**Explicit final acceptance (relayed 2026-11-18 10:18, UTC+08:00):**
Canvas parent `fff568d2-f6a3-4d39-b958-e799e20b2828` obtained the actual
`ask_user` selection **"Accept the Laravel transformation"** after presenting
the technical/visual results, a review link to screenshot/evidence artifacts,
and disclosure of the intermittent timeout with the unchanged passing rerun.
The question explicitly stated no push or PR. Coordinating parent
`2f1d6824-b55d-453e-a19a-f7d48b119deb` relayed that decision to this same
Step 3 session with authorization only to record acceptance and commit locally.

The explicit user acceptance gate is now satisfied: **Step 3 is done and the
Laravel transformation is accepted. All four plan steps are done.** Prior
failures, recoveries, historical evidence boundaries and the approved
missing-license exception remain recorded above; acceptance does not turn them
into unqualified strict-validation success. Finalization changes only this plan
and session evidence, without application edits, test reruns, resource restarts
or access to live data. Cleanup remains intact. No push, PR, deployment or other
publication is authorized by this acceptance or performed during finalization.

**Planning references:** Relevant map guidance:
`web-frameworks\procedural-php-to-framework-knowledge.md`,
`core-php\manual-dependencies-to-composer-knowledge.md`, and
`testing\no-user-acceptance-testing-to-ai-assisted-user-acceptance-testing-knowledge.md`.
Official documentation consulted on 2026-11-18:
https://laravel.com/docs/13.x/releases,
https://laravel.com/docs/13.x/deployment,
https://laravel.com/docs/13.x/structure, and
https://laravel.com/docs/13.x/csrf.
