-- Run once against the application's database before deploying login throttling.
CREATE TABLE login_rate_limits (
    limiter_key CHAR(64) NOT NULL,
    failures TINYINT UNSIGNED NOT NULL DEFAULT 0,
    window_started_at DATETIME NOT NULL,
    locked_until DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (limiter_key),
    INDEX idx_login_rate_limits_locked_until (locked_until)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
