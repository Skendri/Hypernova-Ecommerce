-- Run once against the application's database before deploying the email verification flow.
ALTER TABLE userdata
    ADD COLUMN email_verified_at DATETIME NULL DEFAULT NULL,
    ADD COLUMN email_verification_token_hash CHAR(64) NULL DEFAULT NULL,
    ADD COLUMN email_verification_expires_at DATETIME NULL DEFAULT NULL;

-- Existing accounts have no verification token to redeem. Preserve their access;
-- only accounts created after this migration must verify their email.
UPDATE userdata
SET email_verified_at = CURRENT_TIMESTAMP
WHERE email_verified_at IS NULL;

CREATE UNIQUE INDEX uq_userdata_email_verification_token_hash
    ON userdata (email_verification_token_hash);
