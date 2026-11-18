<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Cookie\CookieValuePrefix;

require dirname(__DIR__) . '/vendor/autoload.php';

$options = getopt('', ['base-url:', 'server-proof:', 'acceptance']);

if (array_key_exists('acceptance', $options)) {
    readfile(__DIR__ . '/acceptance.md');
    echo "\nThis is the separate acceptance guide, not an executed or approved UAT result.\n";
    exit(0);
}

$assertions = 0;
function verify(bool $condition, string $message): void
{
    global $assertions;
    $assertions++;
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function document(string $html): DOMXPath
{
    $dom = new DOMDocument();
    $previous = libxml_use_internal_errors(true);
    try {
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    } finally {
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
    }
    return new DOMXPath($dom);
}

function token(array $response): string
{
    $nodes = document($response['body'])->query('//input[@name="csrf"]/@value');
    verify($nodes->length > 0 && strlen($nodes->item(0)->nodeValue) > 20, 'Page must contain a usable CSRF token.');
    return $nodes->item(0)->nodeValue;
}

function titles(array $response): array
{
    $result = [];
    foreach (document($response['body'])->query('//article//h3') as $node) {
        $result[] = $node->textContent;
    }
    return $result;
}

function formValue(array $response, string $name): string
{
    $xpath = document($response['body']);
    $node = $xpath->query('//aside//input[@name="' . $name . '"]|//aside//textarea[@name="' . $name . '"]')->item(0);
    verify($node !== null, 'Expected form control: ' . $name);
    return $node->nodeName === 'textarea' ? $node->textContent : $node->getAttribute('value');
}

$cookieJar = null;
$database = null;
$ownedIds = [];
$prefix = 'http-' . bin2hex(random_bytes(6)) . '-';
$phase = 'checking explicit isolated resource approval and server provenance';
$failure = false;
$mutationAllowed = false;
$responseCounts = [];

try {
    $baseUrl = $options['base-url'] ?? '';
    verify(is_string($baseUrl) && preg_match('~\Ahttp://127\.0\.0\.1:([0-9]{1,5})\z~', $baseUrl, $match) === 1,
        'Supply an explicitly approved http://127.0.0.1:<test-port> base URL, without a trailing slash.');
    verify((int) $match[1] > 0 && (int) $match[1] <= 65535 && (int) $match[1] !== 8000, 'Use an unused test-only port, never the existing app on 8000.');
    verify(getenv('DB_DATABASE') === 'taskboard_php84_test', 'DB_DATABASE must explicitly select the approved taskboard_php84_test database.');
    $settings = require dirname(__DIR__) . '/config/database.php';
    verify($settings['database'] === 'taskboard_php84_test', 'Effective local database selection must match the exact approved resource.');
    $proofPath = $options['server-proof'] ?? getenv('TASKBOARD_HTTP_SERVER_PROOF');
    verify(is_string($proofPath) && is_file($proofPath), 'Supply --server-proof or TASKBOARD_HTTP_SERVER_PROOF with the verified launch record described in acceptance.md.');
    $proof = json_decode(file_get_contents($proofPath), true, flags: JSON_THROW_ON_ERROR);
    verify(($proof['base_url'] ?? null) === $baseUrl
        && realpath($proof['root'] ?? '') === dirname(__DIR__)
        && ($proof['database'] ?? null) === 'taskboard_php84_test'
        && ($proof['verified_database'] ?? null) === true,
        'Launch record must attest the exact URL, this worktree, and SELECT DATABASE() before starting this server.');
    verify(($proof['extensions'] ?? []) === ['curl', 'fileinfo', 'mbstring', 'zip', 'pdo_mysql', 'dom'],
        'Launch record must verify the actual web PHP process capabilities.');

    $database = Database::connect();
    verify($database->query('SELECT DATABASE()')->fetchColumn() === 'taskboard_php84_test', 'Connected database identity must match exactly before any mutation.');
    $tables = $database->query("SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE() AND table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);
    verify($tables === ['tasks'], 'Only the existing tasks table is expected; this runner never creates schema.');
    verify((int) $database->query('SELECT COUNT(*) FROM tasks')->fetchColumn() === 0, 'Test database must start empty; never clear unknown rows.');
    $repository = new TaskRepository($database);
    $app = require dirname(__DIR__) . '/bootstrap/app.php';
    $app->make(Kernel::class)->bootstrap();
    verify(date_default_timezone_get() === 'UTC', 'Overdue checks require the configured UTC server timezone.');

    $cookieJar = tempnam(sys_get_temp_dir(), 'taskboard-http-');
    verify($cookieJar !== false, 'Cannot create private test cookie jar.');
    $request = function (string $method = 'GET', string $path = '/', array|string|null $fields = null, array $headers = [], bool $cookies = true) use ($baseUrl, $cookieJar, &$responseCounts): array {
        $responseHeaders = [];
        $curl = curl_init($baseUrl . $path);
        $settings = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_PROXY => '',
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_HEADERFUNCTION => static function ($handle, string $line) use (&$responseHeaders): int {
                if (str_contains($line, ':')) {
                    [$name, $value] = explode(':', $line, 2);
                    $responseHeaders[strtolower($name)][] = trim($value);
                }
                return strlen($line);
            },
        ];
        if ($method === 'HEAD') {
            $settings[CURLOPT_NOBODY] = true;
        }
        if ($cookies) {
            $settings[CURLOPT_COOKIEFILE] = $cookieJar;
            $settings[CURLOPT_COOKIEJAR] = $cookieJar;
        }
        if ($fields !== null) {
            $settings[CURLOPT_POSTFIELDS] = is_array($fields) ? http_build_query($fields) : $fields;
        }
        curl_setopt_array($curl, $settings);
        $body = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error = curl_error($curl);
        unset($curl);
        verify($body !== false, 'HTTP transport failed: ' . $error);
        $responseCounts[$status] = ($responseCounts[$status] ?? 0) + 1;
        verify(($responseHeaders['x-content-type-options'][0] ?? '') === 'nosniff', 'Every task response must retain nosniff.');
        verify(($responseHeaders['content-security-policy'][0] ?? '') === "default-src 'self'; style-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'none'", 'Every task response must retain the exact CSP.');
        verify(strtolower($responseHeaders['content-type'][0] ?? '') === 'text/html; charset=utf-8', 'Every task response must retain the HTML content type.');
        return ['status' => $status, 'headers' => $responseHeaders, 'body' => $body];
    };

    $phase = 'verifying real Laravel session provenance before writes';
    $page = $request();
    verify($page['status'] === 200 && str_contains($page['body'], 'A fresh start.'), 'Approved empty app must render HTTP 200.');
    $csrf = token($page);
    $cookieName = $app['config']['session.cookie'];
    $cookieValue = null;
    foreach ($page['headers']['set-cookie'] ?? [] as $cookie) {
        if (str_starts_with($cookie, $cookieName . '=')) {
            $cookieValue = rawurldecode(explode(';', substr($cookie, strlen($cookieName) + 1), 2)[0]);
            verify(str_contains(strtolower($cookie), 'httponly') && str_contains(strtolower($cookie), 'samesite=lax'), 'Session cookie must remain HttpOnly and SameSite=Lax.');
        }
    }
    verify(is_string($cookieValue), 'Real Laravel encrypted session cookie must be present.');
    $encrypter = $app->make('encrypter');
    $sessionId = CookieValuePrefix::validate($cookieName, $encrypter->decrypt($cookieValue, false), $encrypter->getAllKeys());
    verify(is_string($sessionId) && preg_match('/\A[a-zA-Z0-9]{40}\z/', $sessionId) === 1, 'Session cookie must authenticate with this worktree application key.');
    $sessionPath = $app['config']['session.files'] . DIRECTORY_SEPARATOR . $sessionId;
    verify(is_file($sessionPath), 'HTTP session must live in this worktree, not a different server.');
    $sessionData = json_decode(file_get_contents($sessionPath), true, flags: JSON_THROW_ON_ERROR);
    verify(hash_equals($csrf, $sessionData['_token'] ?? ''), 'Actual HTTP form token must match the real Laravel session in this worktree.');
    $mutationAllowed = true;
    echo "Verified taskboard_php84_test empty; real Laravel cookie/token/session and launch provenance matched this worktree before writes.\n";

    $save = static fn (array $changes = []): array => array_replace([
        'csrf' => $csrf, 'action' => 'save', 'id' => '', 'title' => '', 'description' => '', 'priority' => 'medium', 'due_date' => '',
    ], $changes);
    $write = function (array $fields, string $path = '/', string $location = '/') use ($request): array {
        $response = $request('POST', $path, $fields);
        verify($response['status'] === 303, 'Successful write must return 303.');
        verify(($response['headers']['location'][0] ?? '') === $location,
            'Redirect must preserve filters and drop edit; actual Location: ' . ($response['headers']['location'][0] ?? '(missing)'));
        return $response;
    };
    $create = function (array $fields, string $path = '/', string $location = '/') use ($write, $database, $prefix, &$ownedIds): int {
        $write($fields, $path, $location);
        $ids = $database->query('SELECT id FROM tasks ORDER BY id')->fetchAll(PDO::FETCH_COLUMN);
        $new = array_values(array_diff($ids, $ownedIds));
        verify(count($new) === 1, 'Exactly one run-owned task should be inserted.');
        $id = (int) $new[0];
        $select = $database->prepare('SELECT title FROM tasks WHERE id = ?');
        $select->execute([$id]);
        verify(str_starts_with($select->fetchColumn(), $prefix), 'Only identifiable run-owned IDs may be recorded for deletion.');
        $ownedIds[] = $id;
        return $id;
    };
    $notice = function (string $text, string $path = '/') use ($request): array {
        $page = $request('GET', $path);
        verify($page['status'] === 200 && str_contains($page['body'], 'role="status">' . $text), 'Expected one-time notice: ' . $text);
        verify(!str_contains($request('GET', $path)['body'], 'role="status"'), 'Notice must be consumed once.');
        return $page;
    };
    $summary = static function (array $page, array $expected): void {
        $actual = [];
        foreach (document($page['body'])->query('//div[@aria-label="Task summary"]//strong') as $node) {
            $actual[] = (int) $node->textContent;
        }
        verify($actual === $expected, 'Summary counts must remain global.');
    };

    $phase = 'CRUD, escaping, reload, ordering, dates and notices';
    $summary($page, [0, 0, 0]);
    $title = $prefix . "Plan \u{1F680} <tag> \"quote\"";
    $description = "Notes \u{1F4DD}\n<script>alert('x')</script> & 100% done_yet\nOwner's notes";
    $past = (new DateTimeImmutable('yesterday'))->format('Y-m-d');
    $first = $create($save(['title' => '  ' . $title . '  ', 'description' => '  ' . $description . '  ', 'priority' => 'high', 'due_date' => $past]));
    verify($request('POST', '/', ['csrf' => 'stale'])['status'] === 403, 'An early CSRF rejection must not consume the pending notice.');
    verify($request('HEAD')['status'] === 405, 'An early method rejection must not consume the pending notice.');
    $page = $notice('Task added.');
    verify($repository->find($first)['title'] === $title && $repository->find($first)['description'] === $description, 'Persistence must trim and retain Unicode/multiline text.');
    verify(str_contains($page['body'], '&lt;script&gt;') && !str_contains($page['body'], "<script>alert"), 'Task content must be escaped, not executable markup.');
    verify(str_contains($page['body'], '&quot;quote&quot;') && str_contains($page['body'], 'overdue'), 'Attribute escaping and UTC overdue state must render.');
    verify(titles($request()) === [$title], 'Created task must survive independent reload.');
    verify(token($request()) === $csrf, 'Session token must remain stable across requests.');
    $secondTitle = $prefix . 'Second REPORT';
    $second = $create($save(['title' => $secondTitle, 'description' => 'review the report']));
    $notice('Task added.');
    verify(titles($request()) === [$secondTitle, $title], 'Newest incomplete task must appear first.');
    $write(['csrf' => $csrf, 'action' => 'toggle', 'id' => $second]);
    $page = $notice('Task status updated.');
    verify(titles($page) === [$title, $secondTitle], 'Incomplete tasks must precede completed tasks.');
    $summary($page, [2, 1, 1]);
    verify((int) $repository->find($second)['completed'] === 1, 'Completion must persist.');
    $write($save(['id' => $second, 'title' => $secondTitle, 'priority' => 'low', 'due_date' => '2028-02-29']));
    $notice('Task updated.');
    verify((int) $repository->find($second)['completed'] === 1 && $repository->find($second)['due_date'] === '2028-02-29', 'Edit must preserve completion and valid leap date.');
    $write($save(['id' => $second, 'title' => $secondTitle, 'priority' => 'low', 'due_date' => '2028-02-29']));
    $notice('Task updated.');
    $write($save(['id' => $second, 'title' => $secondTitle, 'description' => 'REPORT in description']));
    $notice('Task updated.');
    verify($repository->find($second)['due_date'] === null && (int) $repository->find($second)['completed'] === 1, 'Clearing date must persist without reopening.');
    $write(['csrf' => $csrf, 'action' => 'toggle', 'id' => $second]);
    $notice('Task status updated.');
    verify((int) $repository->find($second)['completed'] === 0, 'Toggle must reopen.');

    $phase = 'literal searches, combined filters, global counts and preserved URLs';
    foreach (['%', '_', "Owner's", '<tag>'] as $search) {
        $page = $request('GET', '/?' . http_build_query(['q' => $search]));
        verify($page['status'] === 200 && titles($page) === [$title], 'Search must match literal text: ' . $search);
        $summary($page, [2, 2, 0]);
    }
    verify(titles($request('GET', '/?q=report')) === [$secondTitle], 'Search must match case-insensitive title/description.');
    verify(titles($request('GET', '/?q=Notes')) === [$title], 'Search must match descriptions.');
    verify(titles($request('GET', '/?q=%20%20Notes%20%20&status=')) === [$title], 'Query search is trimmed and empty status means all.');
    $filters = '/?' . http_build_query(['q' => "Owner's", 'status' => 'active']);
    $page = $request('GET', $filters . '&edit=' . $first);
    verify(formValue($page, 'title') === $title, 'Edit must load the selected task.');
    $xpath = document($page['body']);
    verify($xpath->query('//select[@name="status"]/option[@selected and @value="active"]')->length === 1, 'Status filter must remain selected.');
    foreach ($xpath->query('//form[@method="post"]/@action|//a[@class="cancel"]/@href') as $node) {
        verify($node->nodeValue === $filters, 'Write actions and Cancel must preserve filters and omit edit.');
    }
    verify($xpath->query('//a[@class="reset" and @href="/"]')->length === 1, 'Clear must return to the root.');
    $write($save(['id' => $first, 'title' => $title, 'description' => $description]), $filters . '&edit=' . $first, $filters);
    $notice('Task updated.', $filters);
    $write(['csrf' => $csrf, 'action' => 'toggle', 'id' => $first], $filters, $filters);
    $page = $notice('Task status updated.', $filters);
    verify(titles($page) === [] && str_contains($page['body'], 'No matching tasks.'), 'Combined active filter must exclude completed search result.');
    $summary($page, [2, 1, 1]);
    verify(titles($request('GET', str_replace('active', 'completed', $filters))) === [$title], 'Search and completed filter must combine.');

    $phase = 'CSRF enforcement, stale forms, raw input and HTTP failure contracts';
    $before = $repository->all();
    foreach ([[], ['csrf' => 'wrong'], ['csrf' => ' ' . $csrf . ' '], ['_token' => $csrf]] as $badToken) {
        $fields = array_replace($save(['title' => $prefix . 'must-not-write']), $badToken);
        if (!array_key_exists('csrf', $badToken)) {
            unset($fields['csrf']);
        }
        $response = $request('POST', '/', $fields, ['Sec-Fetch-Site: same-origin', 'Origin: ' . $baseUrl]);
        verify($response['status'] === 403 && str_contains($response['body'], 'Reload the page'), 'Every POST must require csrf, even same-origin.');
    }
    $expired = $request('POST', '/', $save(['title' => $prefix . 'stale']), [], false);
    verify($expired['status'] === 403, 'A stale token without its session must be rejected.');
    $freshCookie = null;
    foreach ($expired['headers']['set-cookie'] ?? [] as $cookie) {
        if (str_starts_with($cookie, $cookieName . '=')) {
            $freshCookie = explode(';', $cookie, 2)[0];
        }
    }
    verify(is_string($freshCookie), 'An expired session must receive a fresh session cookie.');
    $freshPage = $request('GET', '/', null, ['Cookie: ' . $freshCookie], false);
    $freshToken = token($freshPage);
    verify($freshToken !== $csrf, 'Reload after expiration must issue a new usable token.');
    verify($request('POST', '/', ['csrf' => $freshToken, 'action' => 'unknown'], ['Cookie: ' . $freshCookie], false)['status'] === 422,
        'The recovered token must pass CSRF and reach ordinary action validation.');
    verify(token($request()) === $csrf, 'Reload must recover a usable current-session token.');
    foreach (['save', 'toggle', 'delete'] as $action) {
        verify($request('POST', '/', ['action' => $action, 'id' => $first])['status'] === 403, 'Every action must require CSRF.');
    }
    verify($request('POST', '/', $save(['csrf' => ['array'], 'title' => 'ignored']))['status'] === 422, 'Non-string csrf must retain field-validation 422.');
    verify($request('POST', '/?status=invalid', $save(['csrf' => 'wrong']))['status'] === 422, 'List/filter validation must precede CSRF rejection.');
    verify($repository->all() === $before, 'Rejected CSRF requests must not mutate tasks.');

    foreach (['HEAD', 'OPTIONS', 'PUT', 'PATCH', 'DELETE', 'TRACE', 'CONNECT', 'PROPFIND', 'COPY'] as $method) {
        $response = $request($method, '/', null, ['Origin: ' . $baseUrl, 'Access-Control-Request-Method: POST']);
        verify($response['status'] === 405 && ($response['headers']['allow'][0] ?? '') === 'GET, POST', 'Unsupported method must return exact 405/Allow: ' . $method);
        verify($method === 'HEAD' || $response['body'] === 'Method not allowed.', 'Unsupported methods must retain the plain failure body.');
    }
    verify($request('POST', '/?action=save&title=query-only&csrf=' . $csrf, [])['status'] === 403, 'Body fields must never fall back to query values.');
    verify($request('POST', '/?q[]=bad', $save(['title' => $prefix . 'ignored']))['status'] === 422, 'Array query input must return 422.');
    $response = $request('POST', '/?title=query-must-not-fill-form', $save(['title' => '']));
    verify($response['status'] === 422 && formValue($response, 'title') === '', 'Missing body values must never fall back to query values.');
    $fields = $save(['title' => $prefix . 'missing-priority']);
    unset($fields['priority']);
    verify($request('POST', '/', $fields)['status'] === 422, 'Missing priority must remain empty/invalid, not default to medium.');
    verify($request('POST', '/', json_encode($save(['title' => 'JSON must not write'])), ['Content-Type: application/json'])['status'] === 403, 'JSON must not acquire new form-write semantics.');
    $response = $request('POST', '/', $save(['action' => 'unknown']));
    verify($response['status'] === 422, 'Unknown action must return 422.');
    foreach (['id', 'action', 'title', 'description', 'priority', 'due_date'] as $name) {
        verify($request('POST', '/', $save([$name => ['array']]))['status'] === 422, 'Array body field must return 422: ' . $name);
    }
    foreach (['/?q[]=bad', '/?status[]=bad', '/?edit[]=bad', '/?status=active%20', '/?q=' . str_repeat('a', 201), '/?edit=0', '/?edit=-1', '/?edit=abc'] as $path) {
        verify($request('GET', $path)['status'] === 422, 'Invalid query/ID must return 422.');
    }
    $missing = 4294967295;
    verify($repository->find($missing) === null, 'Missing-ID fixture must really be absent.');
    foreach (['save', 'toggle', 'delete'] as $action) {
        $response = $request('POST', '/', $save(['id' => $missing, 'action' => $action, 'title' => 'Missing']));
        verify($response['status'] === 404 && formValue($response, 'title') === '', 'Missing write ID must give inline 404/reset editor.');
    }
    verify($request('GET', '/?edit=' . $missing)['status'] === 404, 'Missing edit ID must return 404.');
    foreach (['', '0', '-1', 'abc'] as $id) {
        verify($request('POST', '/', ['csrf' => $csrf, 'action' => 'toggle', 'id' => $id])['status'] === 422, 'Invalid toggle ID must return 422.');
    }

    $phase = 'Unicode limits, exact priorities/date limits, untrimmed invalid-form model';
    $before = $repository->all();
    $invalidCases = [
        ['title' => ''], ['title' => " \n "], ['title' => str_repeat("\u{00E9}", 201)], ['title' => "\xFF"],
        ['description' => str_repeat('d', 5001)], ['description' => "\xFF"],
        ['priority' => ''], ['priority' => ' high '], ['priority' => 'urgent'],
    ];
    foreach (['2027-02-29', '2028-02-30', '2028-13-01', '2028-2-01', '0000-01-01', '0999-12-31', "2028-01-01\0", '2028-01-01 '] as $date) {
        $invalidCases[] = ['due_date' => $date];
    }
    foreach ($invalidCases as $invalid) {
        $fields = $save(array_replace(['id' => $first, 'title' => '  entered title  ', 'description' => "  entered\nnotes  "], $invalid));
        $response = $request('POST', '/', $fields);
        verify($response['status'] === 422 && str_contains($response['body'], 'role="alert"'), 'Invalid value must render inline 422.');
        if (!array_key_exists('title', $invalid)) {
            verify(formValue($response, 'title') === '  entered title  ', 'Invalid forms must retain untrimmed title.');
        }
        if (!array_key_exists('description', $invalid)) {
            verify(formValue($response, 'description') === "  entered\nnotes  ", 'Invalid forms must retain untrimmed description.');
        }
        if (array_key_exists('due_date', $invalid) && !str_contains($invalid['due_date'], "\0")) {
            verify(formValue($response, 'due_date') === $invalid['due_date'], 'Invalid date model must remain in the HTML attribute.');
        }
        if (array_key_exists('priority', $invalid)) {
            verify(document($response['body'])->query('//aside//select[@name="priority"]/option[@selected]')->length === 0,
                'An invalid priority must not be silently replaced by a valid selected option.');
        }
    }
    verify($repository->all() === $before, 'All invalid edits must leave data untouched.');
    $unicode = $prefix . str_repeat("\u{00E9}", 200 - strlen($prefix));
    $limit = $create($save(['title' => $unicode, 'description' => str_repeat("\u{1F4DD}", 5000), 'due_date' => '1000-01-01']));
    $notice('Task added.');
    verify($repository->find($limit)['title'] === $unicode && mb_strlen($repository->find($limit)['description']) === 5000, 'Unicode limits must count characters, not bytes.');
    $write($save(['id' => $limit, 'title' => $prefix . 'Limits', 'due_date' => '9999-12-31', '_method' => 'DELETE']), '/', '/');
    $notice('Task updated.');
    verify($repository->find($limit)['due_date'] === '9999-12-31', 'Maximum date and ignored _method must preserve POST/save behavior.');
    $response = $request('POST', '/', $save(['id' => $limit, 'title' => $prefix . 'Limits']), ['X-HTTP-Method-Override: DELETE']);
    verify($response['status'] === 303 && $repository->find($limit)['due_date'] === null, 'Method override header must not change existing POST semantics.');
    $notice('Task updated.');
    $create($save(['title' => $prefix . 'Filtered create']), $filters . '&edit=' . $first, $filters);
    $notice('Task added.', $filters);

    $phase = 'deletion and verified cleanup of run-owned fixtures';
    foreach ($ownedIds as $index => $id) {
        $write(['csrf' => $csrf, 'action' => 'delete', 'id' => $id], $filters . '&edit=' . $id, $filters);
        $notice('Task deleted.', $filters);
        verify($repository->find($id) === null, 'Delete must remove only the selected fixture.');
        verify(count($repository->all()) === count($ownedIds) - $index - 1, 'Deleting one fixture must preserve the remaining tasks.');
    }
    verify((int) $database->query('SELECT COUNT(*) FROM tasks')->fetchColumn() === 0, 'Test database must be empty after own-fixture cleanup.');
} catch (Throwable $exception) {
    $failure = true;
    // Do not dump exceptions with connection strings, encrypted cookies, keys or tokens.
    fwrite(STDERR, 'HTTP suite failed during ' . $phase . ': ' . ($exception instanceof RuntimeException && !$exception instanceof PDOException
        ? $exception->getMessage() : $exception::class) . "\n");
} finally {
    if ($database instanceof PDO && $mutationAllowed) {
        try {
            verify($database->query('SELECT DATABASE()')->fetchColumn() === 'taskboard_php84_test', 'Cleanup database guard failed.');
            $recover = $database->prepare('SELECT id FROM tasks WHERE LEFT(title, ?) = ?');
            $recover->execute([strlen($prefix), $prefix]);
            $ownedIds = array_unique([...$ownedIds, ...$recover->fetchAll(PDO::FETCH_COLUMN)]);
            $delete = $database->prepare('DELETE FROM tasks WHERE id = ?');
            foreach ($ownedIds as $id) {
                $delete->execute([$id]);
            }
            verify((int) $database->query('SELECT COUNT(*) FROM tasks')->fetchColumn() === 0, 'Unexpected data remains; no unknown rows were deleted.');
            echo "Cleanup verified: exact test database, zero remaining rows; only recorded run-owned IDs deleted.\n";
        } catch (Throwable $exception) {
            $failure = true;
            fwrite(STDERR, "Cleanup failed; inspect recorded run-owned fixtures locally. No unknown rows were cleared.\n");
        }
    }
    if (is_string($cookieJar) && is_file($cookieJar) && !unlink($cookieJar)) {
        $failure = true;
        fwrite(STDERR, "Cannot remove the HTTP runner's temporary cookie jar.\n");
    }
}

if (!$failure) {
    ksort($responseCounts);
    echo "All task HTTP tests passed ({$assertions} assertions, " . array_sum($responseCounts)
        . ' requests; status counts ' . json_encode($responseCounts, JSON_THROW_ON_ERROR)
        . "); CRUD, filters, CSRF, raw input and HTTP contracts exercised through the real Laravel app.\n";
}

exit($failure ? 1 : 0);
