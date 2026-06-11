CREATE DATABASE IF NOT EXISTS riichi;
USE riichi;

CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_code VARCHAR(8) NOT NULL UNIQUE,
    comp_name VARCHAR(100) DEFAULT '',
    comp_date DATE DEFAULT NULL,
    player1 VARCHAR(50) DEFAULT 'East',
    player2 VARCHAR(50) DEFAULT 'South',
    player3 VARCHAR(50) DEFAULT 'West',
    player4 VARCHAR(50) DEFAULT 'North',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS hanchans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    hanchan_num INT NOT NULL,
    score1 INT NOT NULL,
    score2 INT NOT NULL,
    score3 INT NOT NULL,
    score4 INT NOT NULL,
    points1 INT NOT NULL,
    points2 INT NOT NULL,
    points3 INT NOT NULL,
    points4 INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE
);