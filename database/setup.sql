-- ==================================================
-- DATABASE CREATION
-- ==================================================
CREATE DATABASE IF NOT EXISTS db_phast_auth;
USE db_phast_auth;

-- ==================================================
-- TABLE: tb_users
--
-- Stores all user account information
-- Includes timestamps for creation, updates, and soft deletion
-- ==================================================
CREATE TABLE IF NOT EXISTS tb_users (
    pk_user BIGINT PRIMARY KEY AUTO_INCREMENT,
    txt_name VARCHAR(255) NOT NULL,
    txt_email VARCHAR(255) UNIQUE NOT NULL,
    txt_password_hash VARCHAR(255) NOT NULL,
    dat_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    dat_updated_at TIMESTAMP DEFAULT NULL,
    dat_deleted_at TIMESTAMP DEFAULT NULL,
    boo_is_active BOOLEAN DEFAULT TRUE
);

-- ==================================================
-- FUNCTION: fc_check_user_exists
--
-- Validates if a user exists and is active
--
-- Parameters:
--   param_pk_user: User ID to check
--
-- Returns: TRUE if user exists and is active
--
-- Throws: SQLSTATE 45001 if user not found/inactive
-- ==================================================
DELIMITER $$
CREATE FUNCTION IF NOT EXISTS fc_check_user_exists (
    param_pk_user BIGINT
)
    RETURNS BOOLEAN DETERMINISTIC
BEGIN
    DECLARE err_user_not_found CONDITION FOR SQLSTATE '45001';
    DECLARE var_user_exists BIGINT;
    DECLARE msg_user_not_found VARCHAR(255);

    SELECT COUNT(us.pk_user) INTO var_user_exists
    FROM tb_users us
    WHERE us.pk_user = param_pk_user AND us.boo_is_active = TRUE;

    IF var_user_exists = 0 THEN
        SET msg_user_not_found = CONCAT('Error: User with ID ',
                                        param_pk_user,
                                        ' was not found or is inactive.');
        SIGNAL err_user_not_found
        SET MESSAGE_TEXT = msg_user_not_found;
    ELSE
        RETURN TRUE;
    END IF;
END $$
DELIMITER ;

-- ==================================================
-- STORED PROCEDURE: sp_create_user
--
-- Creates a new user account
--
-- Parameters:
--   param_txt_name: User's full name
--   param_txt_email: User's email (must be unique)
--   param_txt_password_hash: Hashed password
-- ==================================================
DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS sp_create_user (
    IN param_txt_name VARCHAR(255),
    IN param_txt_email VARCHAR(255),
    IN param_txt_password_hash VARCHAR(255)
)
BEGIN
    INSERT INTO tb_users (
        txt_name,
        txt_email,
        txt_password_hash
    ) VALUES (
        param_txt_name,
        param_txt_email,
        param_txt_password_hash
    );
END $$
DELIMITER ;

-- ==================================================
-- STORED PROCEDURE: sp_get_user_by_id
--
-- Retrieves a user's details by ID
-- Only returns data if user exists and is active
-- (Uses fc_check_user_exists for validation).
--
-- Parameters:
--   param_pk_user: User ID to retrieve
--
-- Returns:
--   User details including:
--     - Name
--     - Email
--     - Password hash
--     - Timestamps
--     - Active status
-- ==================================================
DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS sp_get_user_by_id (
    IN param_pk_user BIGINT
)
BEGIN
    IF fc_check_user_exists(param_pk_user) THEN
        SELECT us.txt_name,
               us.txt_email,
               us.txt_password_hash,
               us.dat_created_at,
               us.dat_updated_at,
               us.dat_deleted_at,
               us.boo_is_active
        FROM tb_users us
        WHERE us.pk_user = param_pk_user
        AND us.boo_is_active = TRUE;
    END IF;
END $$
DELIMITER ;

-- ==================================================
-- STORED PROCEDURE: sp_validate_user_login
--
-- Validates user credentials for login.
--
-- Parameters:
--   param_txt_email: User's email
--   param_txt_password_hash: Hashed password
--
-- Returns: User ID if credentials are valid
-- ==================================================
DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS sp_validate_user_login (
    IN param_txt_email VARCHAR(255),
    IN param_txt_password_hash VARCHAR(255)
)
BEGIN
    SELECT us.pk_user
    FROM tb_users us
    WHERE us.txt_email = param_txt_email
      AND us.txt_password_hash = param_txt_password_hash;
END $$
DELIMITER ;

-- ==================================================
-- STORED PROCEDURE: sp_update_user
--
-- Updates user information
-- Only updates fields that contain non-empty values.
-- (Uses fc_check_user_exists for validation).
--
-- Parameters:
--   param_pk_user: User ID to update
--   param_txt_name: New name (optional)
--   param_txt_email: New email (optional)
--   param_txt_password_hash: New password hash (optional)
-- ==================================================
DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS sp_update_user (
    IN param_pk_user BIGINT,
    IN param_txt_name VARCHAR(255),
    IN param_txt_email VARCHAR(255),
    IN param_txt_password_hash VARCHAR(255)
)
BEGIN
    IF fc_check_user_exists(param_pk_user) THEN
        UPDATE tb_users
        SET txt_name = IF(
                param_txt_name <> '',
                param_txt_name,
                txt_name
            ),
            txt_email = IF(
                param_txt_email <> '',
                param_txt_email,
                txt_email
            ),
            txt_password_hash = IF(
                param_txt_password_hash <> '',
                param_txt_password_hash,
                txt_password_hash
            ),
            dat_updated_at = CURRENT_TIMESTAMP
        WHERE pk_user = param_pk_user;
    END IF;
END $$
DELIMITER ;

-- ==================================================
-- STORED PROCEDURE: sp_delete_user
--
-- Performs a soft delete of user account
-- Uses transaction for data integrity.
--
-- Parameters:
--   param_pk_user: User ID to deactivate
-- ==================================================
DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS sp_delete_user (
    IN param_pk_user BIGINT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        RESIGNAL;
    END;

    IF fc_check_user_exists(param_pk_user) THEN
        UPDATE tb_users
        SET boo_is_active = FALSE,
            dat_deleted_at = CURRENT_TIMESTAMP
        WHERE pk_user = param_pk_user;
    END IF;
END $$
DELIMITER ;
