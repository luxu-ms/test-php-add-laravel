<?php

declare(strict_types=1);

final class TaskRepository
{
    public function __construct(private readonly PDO $database)
    {
        $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $database->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function all(string $search = '', string $status = 'all'): array
    {
        $search = trim($search);

        if (preg_match('/\A.{0,200}\z/us', $search) !== 1) {
            throw new InvalidArgumentException('Keep your search to 200 characters or fewer.');
        }

        if (!in_array($status, ['all', 'active', 'completed'], true)) {
            throw new InvalidArgumentException('Choose a valid task status filter.');
        }

        $conditions = [];
        $parameters = [];

        if ($search !== '') {
            $conditions[] = '(instr(lower(title), lower(?)) > 0 OR instr(lower(description), lower(?)) > 0)';
            $parameters = [$search, $search];
        }

        if ($status !== 'all') {
            $conditions[] = 'completed = ?';
            $parameters[] = $status === 'completed' ? 1 : 0;
        }

        $where = $conditions === [] ? '' : ' WHERE ' . implode(' AND ', $conditions);
        $statement = $this->database->prepare('SELECT * FROM tasks' . $where . ' ORDER BY completed, id DESC');
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->database->prepare('SELECT * FROM tasks WHERE id = ?');
        $statement->execute([$id]);

        return $statement->fetch() ?: null;
    }

    public function save(?int $id, string $title, string $description, string $priority = 'medium', string $dueDate = ''): int
    {
        $title = trim($title);
        $description = trim($description);

        if (preg_match('/\A.{1,200}\z/us', $title) !== 1) {
            throw new InvalidArgumentException('Enter a task title between 1 and 200 characters.');
        }

        if (preg_match('/\A.{0,5000}\z/us', $description) !== 1) {
            throw new InvalidArgumentException('Keep the description to 5,000 characters or fewer.');
        }

        if (!in_array($priority, ['low', 'medium', 'high'], true)) {
            throw new InvalidArgumentException('Choose a low, medium, or high priority.');
        }

        if ($dueDate !== '') {
            if (preg_match('/\A[0-9]{4}-[0-9]{2}-[0-9]{2}\z/', $dueDate) !== 1) {
                throw new InvalidArgumentException('Enter a valid due date in YYYY-MM-DD format.');
            }

            $date = DateTimeImmutable::createFromFormat('!Y-m-d', $dueDate);

            if ($date === false || $date->format('Y-m-d') !== $dueDate || (int) $date->format('Y') < 1000) {
                throw new InvalidArgumentException('Enter a valid due date between 1000-01-01 and 9999-12-31.');
            }
        }

        if ($id === null) {
            $statement = $this->database->prepare('INSERT INTO tasks (title, description, priority, due_date) VALUES (?, ?, ?, ?)');
            $statement->execute([$title, $description, $priority, $dueDate === '' ? null : $dueDate]);

            return (int) $this->database->lastInsertId();
        }

        $statement = $this->database->prepare('UPDATE tasks SET title = ?, description = ?, priority = ?, due_date = ? WHERE id = ?');
        $statement->execute([$title, $description, $priority, $dueDate === '' ? null : $dueDate, $id]);
        $this->requireChangedRow($statement);

        return $id;
    }

    public function toggle(int $id): void
    {
        $statement = $this->database->prepare('UPDATE tasks SET completed = 1 - completed WHERE id = ?');
        $statement->execute([$id]);
        $this->requireChangedRow($statement);
    }

    public function delete(int $id): void
    {
        $statement = $this->database->prepare('DELETE FROM tasks WHERE id = ?');
        $statement->execute([$id]);
        $this->requireChangedRow($statement);
    }

    private function requireChangedRow(PDOStatement $statement): void
    {
        if ($statement->rowCount() === 0) {
            throw new OutOfBoundsException('This task no longer exists.');
        }
    }
}
