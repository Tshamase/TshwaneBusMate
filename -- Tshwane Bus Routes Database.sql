-- Tshwane Bus Routes Database
-- Based on information from https://www.tshwane.gov.za/?page_id=723

-- Drop views if they exist (must drop views before tables)
DROP VIEW IF EXISTS active_routes;
DROP VIEW IF EXISTS route_details;
DROP VIEW IF EXISTS todays_schedule;
DROP VIEW IF EXISTS fare_prices;

-- Drop tables if they exist (must drop child tables before parent tables due to foreign keys)
DROP TABLE IF EXISTS fares;
DROP TABLE IF EXISTS schedules;
DROP TABLE IF EXISTS route_stops;
DROP TABLE IF EXISTS routes;

-- Drop database if it exists
DROP DATABASE IF EXISTS tshwane_bus_routes;

-- Create database and use it
CREATE DATABASE IF NOT EXISTS tshwane_bus_routes;
USE tshwane_bus_routes;

-- Main Routes Table
CREATE TABLE routes (
    route_id INT PRIMARY KEY AUTO_INCREMENT,
    route_number VARCHAR(10) NOT NULL,
    route_name VARCHAR(100) NOT NULL,
    origin VARCHAR(100) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    operating_hours VARCHAR(50),
    frequency VARCHAR(50),
    distance_km DECIMAL(5,2),
    estimated_travel_time VARCHAR(20),
    status ENUM('Active', 'Inactive', 'Temporary') DEFAULT 'Active'
);

-- Route Stops Table
CREATE TABLE route_stops (
    stop_id INT PRIMARY KEY AUTO_INCREMENT,
    route_id INT NOT NULL,
    stop_name VARCHAR(100) NOT NULL,
    stop_sequence INT NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    is_transfer_point BOOLEAN DEFAULT FALSE,
    has_shelter BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (route_id) REFERENCES routes(route_id)
);

-- Schedule Table
CREATE TABLE schedules (
    schedule_id INT PRIMARY KEY AUTO_INCREMENT,
    route_id INT NOT NULL,
    direction ENUM('Outbound', 'Inbound') NOT NULL,
    departure_time TIME NOT NULL,
    weekday_type ENUM('Weekday', 'Saturday', 'Sunday', 'Public Holiday') DEFAULT 'Weekday',
    valid_from DATE,
    valid_to DATE,
    notes TEXT,
    FOREIGN KEY (route_id) REFERENCES routes(route_id)
);

-- Fares Table
CREATE TABLE fares (
    fare_id INT PRIMARY KEY AUTO_INCREMENT,
    route_id INT,
    from_stop_id INT,
    to_stop_id INT,
    fare_amount DECIMAL(6,2) NOT NULL,
    fare_type ENUM('Cash', 'Card', 'Monthly') DEFAULT 'Cash',
    effective_date DATE,
    FOREIGN KEY (route_id) REFERENCES routes(route_id),
    FOREIGN KEY (from_stop_id) REFERENCES route_stops(stop_id),
    FOREIGN KEY (to_stop_id) REFERENCES route_stops(stop_id)
);

-- Insert Sample Data Based on Tshwane Bus Routes

-- Insert Routes
INSERT INTO routes (route_number, route_name, origin, destination, operating_hours, frequency, distance_km, estimated_travel_time) VALUES
('A1', 'CBD - Mamelodi', 'Church Square', 'Mamelodi CBD', '05:00-22:00', '15-20 mins', 18.5, '45-60 mins'),
('A2', 'CBD - Atteridgeville', 'Church Square', 'Atteridgeville Mall', '05:30-21:30', '20-25 mins', 15.2, '40-50 mins'),
('A3', 'CBD - Soshanguve', 'Church Square', 'Soshanguve Plaza', '05:15-22:15', '25-30 mins', 22.8, '55-70 mins'),
('B1', 'Hatfield - Menlyn', 'Hatfield Plaza', 'Menlyn Park', '06:00-22:30', '15-20 mins', 8.3, '25-35 mins'),
('B2', 'Centurion - Midrand', 'Centurion Mall', 'Midrand Station', '05:45-21:45', '30-40 mins', 16.7, '40-50 mins'),
('C1', 'Mamelodi - Silverton', 'Mamelodi CBD', 'Silverton Crossing', '05:30-21:00', '20-25 mins', 12.4, '30-40 mins'),
('C2', 'Atteridgeville - Laudium', 'Atteridgeville Mall', 'Laudium Centre', '06:00-20:30', '25-30 mins', 9.8, '25-35 mins'),
('T1', 'Tshwane BRT - Red Line', 'Pretoria Station', 'Hermanstad Depot', '04:45-23:00', '10-15 mins', 14.2, '35-45 mins'),
('T2', 'Tshwane BRT - Blue Line', 'Hatfield Station', 'Fountains Valley', '05:00-22:30', '12-18 mins', 11.7, '30-40 mins');

-- Insert Stops for Route A1 (CBD - Mamelodi)
INSERT INTO route_stops (route_id, stop_name, stop_sequence, is_transfer_point, has_shelter) VALUES
(1, 'Church Square', 1, TRUE, TRUE),
(1, 'Bosman Station', 2, TRUE, TRUE),
(1, 'Pretoria Station', 3, TRUE, TRUE),
(1, 'Madiba Street', 4, FALSE, TRUE),
(1, 'Pretoria West', 5, FALSE, FALSE),
(1, 'Lucas Mangope', 6, FALSE, FALSE),
(1, 'Mamelodi Crossing', 7, TRUE, TRUE),
(1, 'Mamelodi CBD', 8, TRUE, TRUE);

-- Insert Stops for Route T1 (Tshwane BRT - Red Line)
INSERT INTO route_stops (route_id, stop_name, stop_sequence, is_transfer_point, has_shelter) VALUES
(8, 'Pretoria Station', 1, TRUE, TRUE),
(8, 'Marabastad', 2, TRUE, TRUE),
(8, 'Boom Street', 3, FALSE, TRUE),
(8, 'Hamilton Street', 4, FALSE, TRUE),
(8, 'Civic Centre', 5, TRUE, TRUE),
(8, 'Pretoria Art Museum', 6, FALSE, TRUE),
(8, 'Union Buildings', 7, TRUE, TRUE),
(8, 'Hermanstad Depot', 8, TRUE, TRUE);

-- Insert Schedules for Route A1 (Weekday)
INSERT INTO schedules (route_id, direction, departure_time, weekday_type) VALUES
(1, 'Outbound', '05:00:00', 'Weekday'),
(1, 'Outbound', '05:20:00', 'Weekday'),
(1, 'Outbound', '05:40:00', 'Weekday'),
(1, 'Outbound', '06:00:00', 'Weekday'),
(1, 'Outbound', '06:20:00', 'Weekday'),
(1, 'Outbound', '06:40:00', 'Weekday'),
(1, 'Outbound', '07:00:00', 'Weekday'),
(1, 'Outbound', '07:20:00', 'Weekday'),
(1, 'Outbound', '07:40:00', 'Weekday'),
(1, 'Outbound', '08:00:00', 'Weekday'),
(1, 'Outbound', '08:20:00', 'Weekday'),
(1, 'Outbound', '08:40:00', 'Weekday'),
(1, 'Outbound', '09:00:00', 'Weekday'),
(1, 'Inbound', '16:00:00', 'Weekday'),
(1, 'Inbound', '16:20:00', 'Weekday'),
(1, 'Inbound', '16:40:00', 'Weekday'),
(1, 'Inbound', '17:00:00', 'Weekday'),
(1, 'Inbound', '17:20:00', 'Weekday'),
(1, 'Inbound', '17:40:00', 'Weekday'),
(1, 'Inbound', '18:00:00', 'Weekday'),
(1, 'Inbound', '18:20:00', 'Weekday'),
(1, 'Inbound', '18:40:00', 'Weekday'),
(1, 'Inbound', '19:00:00', 'Weekday'),
(1, 'Inbound', '19:20:00', 'Weekday'),
(1, 'Inbound', '19:40:00', 'Weekday'),
(1, 'Inbound', '20:00:00', 'Weekday'),
(1, 'Inbound', '20:20:00', 'Weekday'),
(1, 'Inbound', '20:40:00', 'Weekday'),
(1, 'Inbound', '21:00:00', 'Weekday');

-- Insert Sample Fares
-- NOTE: Make sure stop_id values (1, 3, 8, 25, 32) match actual stop IDs generated in your DBMS.
INSERT INTO fares (route_id, from_stop_id, to_stop_id, fare_amount, fare_type) VALUES
(1, 1, 8, 15.50, 'Cash'),
(1, 1, 8, 12.50, 'Card'),
(1, 1, 3, 8.00, 'Cash'),
(1, 3, 8, 10.00, 'Cash'),
(8, 25, 32, 12.00, 'Cash'),
(8, 25, 32, 9.50, 'Card');

-- Create Views for Common Queries

-- View for all active routes
CREATE VIEW active_routes AS
SELECT route_number, route_name, origin, destination, operating_hours, frequency
FROM routes
WHERE status = 'Active'
ORDER BY route_number;

-- View for route details with stops
CREATE VIEW route_details AS
SELECT r.route_number, r.route_name, rs.stop_name, rs.stop_sequence, 
       rs.is_transfer_point, rs.has_shelter
FROM routes r
JOIN route_stops rs ON r.route_id = rs.route_id
ORDER BY r.route_number, rs.stop_sequence;

-- View for today's schedule (assuming weekday)
CREATE VIEW todays_schedule AS
SELECT r.route_number, r.route_name, s.direction, s.departure_time
FROM schedules s
JOIN routes r ON s.route_id = r.route_id
WHERE s.weekday_type = 'Weekday'
ORDER BY s.departure_time;

-- View for fares
CREATE VIEW fare_prices AS
SELECT r.route_number, r.route_name, 
       fs.stop_name as from_stop, 
       ts.stop_name as to_stop,
       f.fare_amount, f.fare_type
FROM fares f
JOIN routes r ON f.route_id = r.route_id
JOIN route_stops fs ON f.from_stop_id = fs.stop_id
JOIN route_stops ts ON f.to_stop_id = ts.stop_id
ORDER BY r.route_number, f.fare_amount;

-- Sample Queries for Demonstration

-- Get all active routes
SELECT route_number, route_name, origin, destination, operating_hours 
FROM active_routes;

-- Get stops for a specific route
SELECT stop_name, stop_sequence, is_transfer_point, has_shelter
FROM route_details
WHERE route_number = 'A1'
ORDER BY stop_sequence;

-- Get schedule for a specific route
SELECT direction, departure_time
FROM todays_schedule
WHERE route_number = 'A1'
ORDER BY departure_time;

-- Get fares for a specific route
SELECT from_stop, to_stop, fare_amount, fare_type
FROM fare_prices
WHERE route_number = 'A1';

-- Find routes that stop at a specific location
SELECT DISTINCT r.route_number, r.route_name
FROM routes r
JOIN route_stops rs ON r.route_id = rs.route_id
WHERE rs.stop_name LIKE '%Pretoria Station%';

-- Find transfer points between routes
SELECT rs.stop_name, GROUP_CONCAT(DISTINCT r.route_number ORDER BY r.route_number) as routes
FROM route_stops rs
JOIN routes r ON rs.route_id = r.route_id
WHERE rs.is_transfer_point = TRUE
GROUP BY rs.stop_name
HAVING COUNT(DISTINCT r.route_id) > 1;
