<?php

function send_json(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit();
}

function current_user_id(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function require_user_id(): int
{
    $userId = current_user_id();

    if (!$userId) {
        send_json(401, [
            'success' => false,
            'message' => 'Please log in to continue.',
        ]);
    }

    return $userId;
}

function trim_post_value(string $key): string
{
    return trim((string) ($_POST[$key] ?? ''));
}

