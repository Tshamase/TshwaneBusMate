CREATE DATABASE tshwanebusmate;

USE tshwanebusmate;

CREATE TABLE drivers (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100),
    password VARCHAR(255) NOT NULL
);

-- Insert predefined drivers (use hashed passwords)
INSERT INTO drivers (id, name, password) VALUES
('DRV001', 'Thabo Mokoena', '$2y$10$eW5ZQk9vZ3VhZ2UuU3VwZXJTZWN1cmVQYXNz'), -- password: Thabo@123
('DRV002', 'Lerato Dlamini', '$2y$10$Z2V0U2VjdXJlUGFzc3dvcmQxMjM0'),         -- password: Lerato@456
('DRV003', 'Sipho Nkosi', '$2y$10$U2VjdXJlUGFzc3dvcmQxMjM0NTY3OA==');       -- password: Sipho@789
