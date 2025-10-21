

-- 1. Create dedicated user
DO $$
BEGIN
   IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'tshwane_user') THEN
      CREATE USER tshwane_user WITH PASSWORD 'busmate2025';
   END IF;
END
$$;

GRANT ALL PRIVILEGES ON DATABASE tshwane_bus_db TO tshwane_user;


DROP TABLE IF EXISTS trackerSR_fares CASCADE;
DROP TABLE IF EXISTS trackerSR_schedules CASCADE;
DROP TABLE IF EXISTS trackerSR_stop_routes CASCADE;
DROP TABLE IF EXISTS trackerSR_bus CASCADE;
DROP TABLE IF EXISTS trackerSR_stop CASCADE;
DROP TABLE IF EXISTS trackerSR_route CASCADE;

DROP VIEW IF EXISTS trackerSR_active_routes;
DROP VIEW IF EXISTS trackerSR_route_details;
DROP VIEW IF EXISTS trackerSR_todays_schedule;
DROP VIEW IF EXISTS trackerSR_fare_prices;



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
    route_id INTEGER NOT NULL REFERENCES trackerSR_route(id) ON DELETE CASCADE,
    stop_name VARCHAR(100) NOT NULL,
    stop_sequence INTEGER NOT NULL,
    latitude NUMERIC(10,8),
    longitude NUMERIC(11,8),
    is_transfer_point BOOLEAN DEFAULT FALSE,
    has_shelter BOOLEAN DEFAULT FALSE
);

-- Many-to-Many junction (Stop ↔ Route)
CREATE TABLE trackerSR_stop_routes (
    id SERIAL PRIMARY KEY,
    stop_id INTEGER NOT NULL REFERENCES trackerSR_stop(id) ON DELETE CASCADE,
    route_id INTEGER NOT NULL REFERENCES trackerSR_route(id) ON DELETE CASCADE,
    UNIQUE(stop_id, route_id)
);

-- Buses Dj and lindo, verify on your side
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

-- Stops for A1
INSERT INTO trackerSR_stop (route_id, stop_name, stop_sequence, is_transfer_point, has_shelter, latitude, longitude) VALUES
(1, 'Church Square', 1, TRUE, TRUE, -25.74610000, 28.18810000),
(1, 'Bosman Station', 2, TRUE, TRUE, -25.75100000, 28.18900000),
(1, 'Pretoria Station', 3, TRUE, TRUE, -25.75800000, 28.19000000),
(1, 'Madiba Street', 4, FALSE, TRUE, -25.76200000, 28.19500000),
(1, 'Pretoria West', 5, FALSE, FALSE, -25.77000000, 28.20000000),
(1, 'Lucas Mangope', 6, FALSE, FALSE, -25.77500000, 28.21000000),
(1, 'Mamelodi Crossing', 7, TRUE, TRUE, -25.71000000, 28.36000000),
(1, 'Mamelodi CBD', 8, TRUE, TRUE, -25.70500000, 28.37000000);

-- Stops for T1 (BRT Red Line)
INSERT INTO trackerSR_stop (route_id, stop_name, stop_sequence, is_transfer_point, has_shelter, latitude, longitude) VALUES
(8, 'Pretoria Station', 1, TRUE, TRUE, -25.75800000, 28.19000000),
(8, 'Marabastad', 2, TRUE, TRUE, -25.74500000, 28.18500000),
(8, 'Boom Street', 3, FALSE, TRUE, -25.74000000, 28.18000000),
(8, 'Hamilton Street', 4, FALSE, TRUE, -25.73500000, 28.17500000),
(8, 'Civic Centre', 5, TRUE, TRUE, -25.73000000, 28.17000000),
(8, 'Pretoria Art Museum', 6, FALSE, TRUE, -25.72500000, 28.16500000),
(8, 'Union Buildings', 7, TRUE, TRUE, -25.72000000, 28.16000000),
(8, 'Hermanstad Depot', 8, TRUE, TRUE, -25.71500000, 28.15500000);

-- Junction table (Many-to-Many)
INSERT INTO trackerSR_stop_routes (stop_id, route_id) VALUES
(1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1), -- A1
(9,8),(10,8),(11,8),(12,8),(13,8),(14,8),(15,8),(16,8); -- T1

-- Sample live buses
INSERT INTO trackerSR_bus (route_id, current_latitude, current_longitude, eta, status) VALUES
(1, -25.75000000, 28.19500000, '00:05:00', 'on_time'),
(1, -25.77000000, 28.26000000, '00:12:00', 'on_time'),
(8, -25.73500000, 28.17500000, '00:03:00', 'delayed');

-- Sample schedules (A1 weekday)
INSERT INTO trackerSR_schedules (route_id, direction, departure_time, weekday_type) VALUES
(1, 'Outbound', '05:00:00', 'Weekday'),
(1, 'Outbound', '05:20:00', 'Weekday'),
(1, 'Outbound', '05:40:00', 'Weekday'),
(1, 'Inbound', '16:00:00', 'Weekday'),
(1, 'Inbound', '16:20:00', 'Weekday'),
(1, 'Inbound', '16:40:00', 'Weekday');

-- Sample fares
INSERT INTO trackerSR_fares (route_id, from_stop_id, to_stop_id, fare_amount, fare_type) VALUES
(1, 1, 8, 15.50, 'Cash'),
(1, 1, 8, 12.50, 'Card'),
(1, 1, 3, 8.00, 'Cash'),
(8, 9, 16, 12.00, 'Cash');

-- 5. Views 

CREATE OR REPLACE VIEW trackerSR_active_routes AS
SELECT route_number, route_name, origin, destination, operating_hours, frequency
FROM trackerSR_route
WHERE status = 'Active'
ORDER BY route_number;

CREATE OR REPLACE VIEW trackerSR_route_details AS
SELECT r.route_number, r.route_name, s.stop_name, s.stop_sequence,
       s.is_transfer_point, s.has_shelter
FROM trackerSR_route r
JOIN trackerSR_stop s ON r.id = s.route_id
ORDER BY r.route_number, s.stop_sequence;

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

-- only "missing" parts are with connecting the database to the server permanently along with Slo and Mo's parts. additionaly idk how to facilitate the Ai part

-- Test 1: Active routes
-- SELECT * FROM trackerSR_active_routes;

-- Test 2: Stops for A1
-- SELECT * FROM trackerSR_route_details WHERE route_number = 'A1';

-- Test 3: Today’s schedule
-- SELECT * FROM trackerSR_todays_schedule WHERE route_number = 'A1';

-- Test 4: Fares
-- SELECT * FROM trackerSR_fare_prices WHERE route_number = 'A1';
