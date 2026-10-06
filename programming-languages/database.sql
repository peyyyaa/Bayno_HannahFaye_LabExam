-- Run this once in phpMyAdmin (Import tab) or the MySQL console.

CREATE DATABASE IF NOT EXISTS programming_languages
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE programming_languages;

CREATE TABLE IF NOT EXISTS users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name    VARCHAR(50)  NOT NULL,
  last_name     VARCHAR(50)  NOT NULL,
  email         VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tokens for "Remember me"
CREATE TABLE IF NOT EXISTS auth_tokens (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  selector       CHAR(24)     NOT NULL UNIQUE,
  validator_hash CHAR(64)     NOT NULL,
  user_id        INT UNSIGNED NOT NULL,
  expires_at     DATETIME     NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
