CREATE TABLE IF NOT EXISTS tasks (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    completed TINYINT UNSIGNED NOT NULL DEFAULT 0,
    priority VARCHAR(6) NOT NULL DEFAULT 'medium',
    due_date DATE DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT tasks_completed_valid CHECK (completed IN (0, 1)),
    CONSTRAINT tasks_priority_valid CHECK (priority IN ('low', 'medium', 'high')),
    INDEX tasks_completion_order (completed, id)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci;
