CREATE DATABASE wybory_portal;
USE wybory_portal;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    surname VARCHAR(100),
    pesel CHAR(11) UNIQUE,
    email VARCHAR(100),
    password_hash VARCHAR(255),
    is_admin BOOLEAN DEFAULT FALSE
);

CREATE TABLE elections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    start_time DATETIME,
    end_time DATETIME
);

CREATE TABLE candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    election_id INT,
    name VARCHAR(100),
    votes INT DEFAULT 0,
    FOREIGN KEY (election_id) REFERENCES elections(id) ON DELETE CASCADE
);

CREATE TABLE vote_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    election_id INT,
    token VARCHAR(255),
    used BOOLEAN DEFAULT FALSE,
    expires_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (election_id) REFERENCES elections(id)
);
