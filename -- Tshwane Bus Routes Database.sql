
-- Create dedicated user
DO $$
BEGIN
   IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'tshwane_user') THEN
      CREATE USER tshwane_user WITH PASSWORD 'busmate2025';
   END IF;
END
$$;

GRANT ALL PRIVILEGES ON DATABASE tshwane_bus_db TO tshwane_user;

-- Drop existing tables and views if they exist
DROP TABLE IF EXISTS trackerSR_fares CASCADE;
DROP TABLE IF EXISTS trackerSR_schedules CASCADE;
DROP TABLE IF EXISTS trackerSR_stop_routes CASCADE;
DROP TABLE IF EXISTS trackerSR_bus CASCADE;
DROP TABLE IF EXISTS trackerSR_stop CASCADE;
DROP TABLE IF EXISTS trackerSR_route CASCADE;
DROP TABLE IF EXISTS signups CASCADE;
DROP TABLE IF EXISTS logins CASCADE;

DROP VIEW IF EXISTS trackerSR_active_routes;
DROP VIEW IF EXISTS trackerSR_route_details;
DROP VIEW IF EXISTS trackerSR_todays_schedule;
DROP VIEW IF EXISTS trackerSR_fare_prices;

CREATE TYPE user_role AS ENUM ('commuter', 'driver', 'admin');

-- Signups table
CREATE TABLE signups (
  id SERIAL PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role user_role NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Logins table 
CREATE TABLE logins (
  id SERIAL PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role user_role NOT NULL,

  -- Role-specific fields (nullable)
  bus_card_no VARCHAR(50),     -- for commuters
  driver_id VARCHAR(50),       -- for drivers
  admin_id VARCHAR(50),        -- for admins

  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Routes
CREATE TABLE trackerSR_route (
    id SERIAL PRIMARY KEY,
    route_number VARCHAR(10) NOT NULL UNIQUE,
    route_name VARCHAR(100) NOT NULL,
    origin VARCHAR(100) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    operating_hours VARCHAR(50),
    frequency VARCHAR(50),
    distance_km NUMERIC(5,2),
    estimated_travel_time VARCHAR(20),
    status VARCHAR(20) DEFAULT 'Active' CHECK (status IN ('Active','Inactive','Temporary'))
);

-- Stops 
CREATE TABLE trackerSR_stop (
    id SERIAL PRIMARY KEY,
    stop_name VARCHAR(100) NOT NULL,
    latitude NUMERIC(10,8),
    longitude NUMERIC(11,8),
    is_transfer_point BOOLEAN DEFAULT FALSE,
    has_shelter BOOLEAN DEFAULT FALSE
);

-- Many-to-Many junction (Stop ↔ Route, moved stop_sequence here)
CREATE TABLE trackerSR_stop_routes (
    id SERIAL PRIMARY KEY,
    stop_id INTEGER NOT NULL REFERENCES trackerSR_stop(id) ON DELETE CASCADE,
    route_id INTEGER NOT NULL REFERENCES trackerSR_route(id) ON DELETE CASCADE,
    stop_sequence INTEGER NOT NULL,
    UNIQUE(stop_id, route_id)
);

-- Buses
CREATE TABLE trackerSR_bus (
    id SERIAL PRIMARY KEY,
    route_id INTEGER NOT NULL REFERENCES trackerSR_route(id) ON DELETE CASCADE,
    current_latitude NUMERIC(10,8),
    current_longitude NUMERIC(11,8),
    eta INTERVAL,
    last_updated TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    status VARCHAR(50) DEFAULT 'active'
);

-- Schedules
CREATE TABLE trackerSR_schedules (
    id SERIAL PRIMARY KEY,
    route_id INTEGER NOT NULL REFERENCES trackerSR_route(id) ON DELETE CASCADE,
    direction VARCHAR(20) NOT NULL CHECK (direction IN ('Outbound','Inbound')),
    departure_time TIME NOT NULL,
    weekday_type VARCHAR(20) DEFAULT 'Weekday' CHECK (weekday_type IN ('Weekday','Saturday','Sunday','Public Holiday')),
    valid_from DATE,
    valid_to DATE,
    notes TEXT
);

-- Fares
CREATE TABLE trackerSR_fares (
    id SERIAL PRIMARY KEY,
    route_id INTEGER REFERENCES trackerSR_route(id) ON DELETE SET NULL,
    from_stop_id INTEGER REFERENCES trackerSR_stop(id) ON DELETE SET NULL,
    to_stop_id INTEGER REFERENCES trackerSR_stop(id) ON DELETE SET NULL,
    fare_amount NUMERIC(6,2) NOT NULL,
    fare_type VARCHAR(20) DEFAULT 'Cash' CHECK (fare_type IN ('Cash','Card','Monthly')),
    effective_date DATE
);

-- Tshwane data 
-- Routes
INSERT INTO trackerSR_route (route_number, route_name, origin, destination, operating_hours, frequency, distance_km, estimated_travel_time) VALUES
('A1', 'CBD - Mamelodi', 'Church Square', 'Mamelodi CBD', '05:00-22:00', '15-20 mins', 18.5, '45-60 mins'),
('A2', 'CBD - Atteridgeville', 'Church Square', 'Atteridgeville Mall', '05:30-21:30', '20-25 mins', 15.2, '40-50 mins'),
('A3', 'CBD - Soshanguve', 'Church Square', 'Soshanguve Plaza', '05:15-22:15', '25-30 mins', 22.8, '55-70 mins'),
('B1', 'Hatfield - Menlyn', 'Hatfield Plaza', 'Menlyn Park', '06:00-22:30', '15-20 mins', 8.3, '25-35 mins'),
('B2', 'Centurion - Midrand', 'Centurion Mall', 'Midrand Station', '05:45-21:45', '30-40 mins', 16.7, '40-50 mins'),
('C1', 'Mamelodi - Silverton', 'Mamelodi CBD', 'Silverton Crossing', '05:30-21:00', '20-25 mins', 12.4, '30-40 mins'),
('C2', 'Atteridgeville - Laudium', 'Atteridgeville Mall', 'Laudium Centre', '06:00-20:30', '25-30 mins', 9.8, '25-35 mins'),
('T1', 'Tshwane BRT - Red Line', 'Pretoria Station', 'Hermanstad Depot', '04:45-23:00', '10-15 mins', 14.2, '35-45 mins'),
('T2', 'Tshwane BRT - Blue Line', 'Hatfield Station', 'Fountains Valley', '05:00-22:30', '12-18 mins', 11.7, '30-40 mins');

-- Insert unique stops 
INSERT INTO trackerSR_stop (stop_name, is_transfer_point, has_shelter, latitude, longitude) VALUES
('Church Square', TRUE, TRUE, -25.74610000, 28.18810000),
('Bosman Station', TRUE, TRUE, -25.75100000, 28.18900000),
('Pretoria Station', TRUE, TRUE, -25.75800000, 28.19000000),
('Madiba Street', FALSE, TRUE, -25.76200000, 28.19500000),
('Pretoria West', FALSE, FALSE, -25.77000000, 28.20000000),
('Lucas Mangope', FALSE, FALSE, -25.77500000, 28.21000000),
('Mamelodi Crossing', TRUE, TRUE, -25.71000000, 28.36000000),
('Mamelodi CBD', TRUE, TRUE, -25.70500000, 28.37000000),
('Marabastad', TRUE, TRUE, -25.74500000, 28.18500000),
('Boom Street', FALSE, TRUE, -25.74000000, 28.18000000),
('Hamilton Street', FALSE, TRUE, -25.73500000, 28.17500000),
('Civic Centre', TRUE, TRUE, -25.73000000, 28.17000000),
('Pretoria Art Museum', FALSE, TRUE, -25.72500000, 28.16500000),
('Union Buildings', TRUE, TRUE, -25.72000000, 28.16000000),
('Hermanstad Depot', TRUE, TRUE, -25.71500000, 28.15500000);

-- Junction table 
-- For A1 (route id 1, stops 1-8)
INSERT INTO trackerSR_stop_routes (stop_id, route_id, stop_sequence) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7),
(8, 1, 8);

-- For T1 (route id 8, stops 3 + 9-15)
INSERT INTO trackerSR_stop_routes (stop_id, route_id, stop_sequence) VALUES
(3, 8, 1),
(9, 8, 2),
(10, 8, 3),
(11, 8, 4),
(12, 8, 5),
(13, 8, 6),
(14, 8, 7),
(15, 8, 8);

-- Sample live buses
INSERT INTO trackerSR_bus (route_id, current_latitude, current_longitude, eta, status) VALUES
(1, -25.75000000, 28.19500000, '00:05:00'::interval, 'on_time'),
(1, -25.77000000, 28.26000000, '00:12:00'::interval, 'on_time'),
(8, -25.73500000, 28.17500000, '00:03:00'::interval, 'delayed');

-- Sample schedules (A1 weekday)
INSERT INTO trackerSR_schedules (route_id, direction, departure_time, weekday_type) VALUES
(1, 'Outbound', '05:00:00', 'Weekday'),
(1, 'Outbound', '05:20:00', 'Weekday'),
(1, 'Outbound', '05:40:00', 'Weekday'),
(1, 'Inbound', '16:00:00', 'Weekday'),
(1, 'Inbound', '16:20:00', 'Weekday'),
(1, 'Inbound', '16:40:00', 'Weekday');

-- Sample fares (adjusted stop IDs for deduplication)
INSERT INTO trackerSR_fares (route_id, from_stop_id, to_stop_id, fare_amount, fare_type) VALUES
(1, 1, 8, 15.50, 'Cash'),
(1, 1, 8, 12.50, 'Card'),
(1, 1, 3, 8.00, 'Cash'),
(8, 3, 15, 12.00, 'Cash');

-- Views 
CREATE OR REPLACE VIEW trackerSR_active_routes AS
SELECT route_number, route_name, origin, destination, operating_hours, frequency
FROM trackerSR_route
WHERE status = 'Active'
ORDER BY route_number;

CREATE OR REPLACE VIEW trackerSR_route_details AS
SELECT r.route_number, r.route_name, s.stop_name, sr.stop_sequence,
       s.is_transfer_point, s.has_shelter
FROM trackerSR_route r
JOIN trackerSR_stop_routes sr ON r.id = sr.route_id
JOIN trackerSR_stop s ON sr.stop_id = s.id
ORDER BY r.route_number, sr.stop_sequence;

CREATE OR REPLACE VIEW trackerSR_todays_schedule AS
SELECT r.route_number, r.route_name, s.direction, s.departure_time
FROM trackerSR_schedules s
JOIN trackerSR_route r ON s.route_id = r.id
WHERE s.weekday_type = 'Weekday'
ORDER BY s.departure_time;

CREATE OR REPLACE VIEW trackerSR_fare_prices AS
SELECT r.route_number, r.route_name,
       fs.stop_name AS from_stop,
       ts.stop_name AS to_stop,
       f.fare_amount, f.fare_type
FROM trackerSR_fares f
JOIN trackerSR_route r ON f.route_id = r.id
JOIN trackerSR_stop fs ON f.from_stop_id = fs.id
JOIN trackerSR_stop ts ON f.to_stop_id = ts.id
ORDER BY r.route_number, f.fare_amount;
