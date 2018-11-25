DELETE FROM sem_uzivatel WHERE id = 1;

/**
 * Admin user (email: admin@admin.cz, password: admin).
 */
INSERT INTO sem_uzivatel (id, email, heslo, admin) VALUES (1, 'admin@admin.cz', '$2y$10$5uWL4bwdr5f9RuZ.eNtJVO9kT5FYgJePD6XqmT7jRFNy50z2uVoXC', 1);