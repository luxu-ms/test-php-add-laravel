<?php

$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Taskboard | Your day, in order</title>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
<main class="shell">
    <header class="page-header">
        <a class="brand" href="/">taskboard<span>.</span></a>
        <span class="badge">A little focus goes a long way</span>
    </header>
    <section class="intro">
        <p class="eyebrow">MAKE ROOM FOR WHAT MATTERS</p>
        <h1>Your day,<br><span>in order.</span></h1>
        <p>Big plans start with small steps. Keep yours all in one place.</p>
    </section>
    <div class="summary" aria-label="Task summary">
        <div><strong><?= count($allTasks) ?></strong><span>Total tasks</span></div>
        <div><strong><?= count($allTasks) - $completed ?></strong><span>In progress</span></div>
        <div><strong><?= $completed ?></strong><span>Completed</span></div>
    </div>
    <?php if ($notice !== null): ?>
        <p class="notice" role="status"><?= $escape($notice) ?></p>
    <?php endif; ?>
    <?php if ($error !== null): ?>
        <p class="error" role="alert"><?= $escape($error) ?></p>
    <?php endif; ?>
    <div class="workspace">
        <section class="task-list" aria-labelledby="tasks-heading">
            <div class="section-heading">
                <h2 id="tasks-heading">Your tasks</h2>
                <span><?= count($tasks) ?> of <?= count($allTasks) ?> tasks</span>
            </div>
            <form method="get" action="/" class="filters" role="search" aria-label="Find tasks">
                <div class="search-field">
                    <label for="search">Search tasks</label>
                    <input type="search" id="search" name="q" maxlength="200"
                        placeholder="Search titles and descriptions" value="<?= $escape($search) ?>">
                </div>
                <div>
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <?php foreach (['all' => 'All tasks', 'active' => 'In progress', 'completed' => 'Completed'] as $value => $label): ?>
                            <option value="<?= $value ?>" <?= $status === $value ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="filter-button" type="submit">Apply</button>
                <?php if ($search !== '' || $status !== 'all'): ?><a class="reset" href="/">Clear</a><?php endif; ?>
            </form>
            <?php if ($tasks === []): ?>
                <div class="empty">
                    <span class="empty-mark" aria-hidden="true">+</span>
                    <h3><?= $allTasks === [] ? 'A fresh start.' : 'No matching tasks.' ?></h3>
                    <p><?= $allTasks === [] ? 'Add your first task and make a little progress today.' : 'Try a different search or clear your filters.' ?></p>
                </div>
            <?php endif; ?>
            <?php foreach ($tasks as $task): ?>
                <article class="task <?= (int) $task['completed'] === 1 ? 'is-complete' : '' ?>">
                    <form method="post" action="<?= $escape($listUrl) ?>" class="toggle-form">
                        <input type="hidden" name="csrf" value="<?= $escape($csrf) ?>">
                        <input type="hidden" name="action" value="toggle">
                        <input type="hidden" name="id" value="<?= $task['id'] ?>">
                        <button class="toggle" type="submit"
                            aria-label="<?= $escape('Mark ' . $task['title'] . ((int) $task['completed'] === 1 ? ' incomplete' : ' complete')) ?>">
                            <?= (int) $task['completed'] === 1 ? '&#10003;' : '' ?>
                        </button>
                    </form>
                    <div class="task-content">
                        <h3><?= $escape($task['title']) ?></h3>
                        <?php if ($task['description'] !== ''): ?>
                            <p><?= nl2br($escape($task['description'])) ?></p>
                        <?php endif; ?>
                        <span class="task-status"><?= (int) $task['completed'] === 1 ? 'Completed' : 'In progress' ?></span>
                        <span class="priority priority-<?= $escape($task['priority']) ?>"><?= $escape(ucfirst($task['priority'])) ?> priority</span>
                        <?php if ($task['due_date'] !== null): ?>
                            <?php $overdue = (int) $task['completed'] === 0 && $task['due_date'] < $today; ?>
                            <span class="due-date <?= $overdue ? 'overdue' : '' ?>">
                                <?= $overdue ? 'Overdue' : 'Due' ?>
                                <time datetime="<?= $escape($task['due_date']) ?>"><?= $escape($task['due_date']) ?></time>
                            </span>
                        <?php endif; ?>
                        <div class="task-actions">
                            <a href="/?<?= $escape(http_build_query([...$filters, 'edit' => $task['id']])) ?>">Edit<span class="sr-only"> <?= $escape($task['title']) ?></span></a>
                            <form method="post" action="<?= $escape($listUrl) ?>">
                                <input type="hidden" name="csrf" value="<?= $escape($csrf) ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $task['id'] ?>">
                                <button class="delete" type="submit">Delete<span class="sr-only"> <?= $escape($task['title']) ?></span></button>
                            </form>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
        <aside class="editor" aria-labelledby="form-heading">
            <p class="eyebrow"><?= $editing ? 'A SMALL ADJUSTMENT' : 'ONE STEP AT A TIME' ?></p>
            <h2 id="form-heading"><?= $editing ? 'Edit your task' : 'What comes next?' ?></h2>
            <p class="editor-hint">Give your next step a name.</p>
            <form method="post" action="<?= $escape($listUrl) ?>">
                <input type="hidden" name="csrf" value="<?= $escape($csrf) ?>">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" value="<?= $form['id'] ?? '' ?>">
                <label for="title">Task title</label>
                <input id="title" name="title" maxlength="200" required
                    placeholder="What would you like to get done?" value="<?= $escape($form['title']) ?>">
                <label for="description">Description <span>(optional)</span></label>
                <textarea id="description" name="description" rows="5" maxlength="5000"
                    placeholder="A few details to help you get started..."><?= $escape($form['description']) ?></textarea>
                <div class="task-options">
                    <div>
                        <label for="priority">Priority</label>
                        <select id="priority" name="priority">
                            <?php foreach (['low', 'medium', 'high'] as $priority): ?>
                                <option value="<?= $priority ?>" <?= $form['priority'] === $priority ? 'selected' : '' ?>><?= ucfirst($priority) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="due-date">Due date <span>(optional)</span></label>
                        <input id="due-date" name="due_date" type="date" min="1000-01-01" max="9999-12-31"
                            value="<?= $escape($form['due_date'] ?? '') ?>">
                    </div>
                </div>
                <button class="primary" type="submit"><?= $editing ? 'Save changes' : '+ Add task' ?></button>
                <?php if ($editing): ?><a class="cancel" href="<?= $escape($listUrl) ?>">Cancel editing</a><?php endif; ?>
            </form>
            <p class="editor-footer">Less in your head. More space to focus.</p>
        </aside>
    </div>
    <footer class="page-footer">Small steps. Meaningful progress.</footer>
</main>
</body>
</html>
