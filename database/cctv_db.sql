-- --------------------------------------------------------
-- ✅ Create the Database
-- --------------------------------------------------------
CREATE DATABASE IF NOT EXISTS cctv_db;
USE cctv_db;

-- --------------------------------------------------------
-- ✅ Table: users (for authentication)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    type ENUM('Admin', 'Staff') NOT NULL DEFAULT 'Admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (name, username, password, type) VALUES
('Admin User', 'admin', '$2y$10$VJ1Fj.eMw9j0tHht/EGXO.q.A1M54J0Zk97y1WhEq8onF38hPprqa', 'Admin')
ON DUPLICATE KEY UPDATE username = username;

-- --------------------------------------------------------
-- ✅ Table: daily_logs
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS daily_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    time TIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    incident_type VARCHAR(100) NOT NULL,
    details TEXT NOT NULL,
    action_taken TEXT NOT NULL,
    agency VARCHAR(100) NOT NULL,
    agency_others VARCHAR(255) DEFAULT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    is_checked TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO daily_logs (date, time, location, incident_type, details, action_taken, agency, image_path, is_checked) VALUES
('2025-01-01', '10:25:00', 'Mall Entrance', 'Theft', 'Stolen wallet near entrance', 'Seen', 'PNP', 'uploads/log1.jpg', 0),
('2025-01-02', '11:30:00', 'Don Carlos St', 'Traffic Accident', 'Minor collision', 'Monitored', 'TMC', 'uploads/log2.jpg', 0);

-- --------------------------------------------------------
-- ✅ Table: footages
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS footages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    requesting_party VARCHAR(100) NOT NULL,
    phone_no VARCHAR(20) NOT NULL,
    date_requested DATE NOT NULL,
    time_requested TIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    incident_date DATE NOT NULL,
    incident_time TIME NOT NULL,
    incident_type VARCHAR(100) NOT NULL,
    incident_type_others VARCHAR(255) DEFAULT NULL,
    description TEXT NOT NULL,
    agency VARCHAR(100) NOT NULL,
    agency_others VARCHAR(255) DEFAULT NULL,
    caught_on_cam ENUM('Captured', 'Uncaptured') NOT NULL DEFAULT 'Uncaptured',
    status ENUM('Pending', 'Reviewed', 'Resolved') NOT NULL DEFAULT 'Pending',
    release_status ENUM('Released', 'Unreleased') NOT NULL DEFAULT 'Unreleased',
    release_date DATETIME DEFAULT NULL,
    usefulness ENUM('Useful', 'Somehow Useful', 'Not Useful') NOT NULL DEFAULT 'Not Useful',
    client_feedback TEXT DEFAULT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    log_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (log_id) REFERENCES daily_logs(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Alter the table to ensure the `date_requested` and `incident_date` columns are NOT NULL
ALTER TABLE footages 
MODIFY date_requested DATE NOT NULL;

ALTER TABLE footages 
MODIFY incident_date DATE NOT NULL;

-- Insert sample data into footages table
INSERT INTO footages (
    requesting_party, phone_no, date_requested, time_requested, location,
    incident_date, incident_time, incident_type, description, agency,
    caught_on_cam, status, release_status, release_date,
    usefulness, client_feedback, image_path, log_id
) VALUES
('Juan Dela Cruz', '09123456789', '2025-03-25', '10:25:00', 'Mall Entrance',
 '2025-03-24', '09:30:00', 'Theft', 'Stolen wallet near entrance', 'PNP',
 'Captured', 'Reviewed', 'Released', '2025-03-26 12:00:00',
 'Useful', 'Captured clearly, thief identified.', 'uploads/footage1.jpg', 1),
('Maria Santos', '09987654321', '2025-03-24', '11:45:00', 'Don Carlos St',
 '2025-03-23', '10:15:00', 'Traffic Accident', 'Minor collision reported', 'MCPS',
 'Uncaptured', 'Pending', 'Unreleased', NULL,
 'Somehow Useful', 'Some vehicles partially visible.', 'uploads/footage2.jpg', 2);

-- --------------------------------------------------------
-- ✅ Table: playback_requests
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS playback_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone_no VARCHAR(20) NOT NULL,
    date_requested DATE NOT NULL,
    time_requested TIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    incident_date DATE NOT NULL,
    incident_time TIME NOT NULL,
    incident_type VARCHAR(100) NOT NULL,
    incident_type_others VARCHAR(255) DEFAULT NULL,
    description TEXT NOT NULL,
    usefulness ENUM('Useful', 'Somehow Useful', 'Not Useful') NOT NULL,
    caught_on_cam ENUM('Captured', 'Uncaptured') NOT NULL,
    agency VARCHAR(100) NOT NULL,
    agency_others VARCHAR(255) DEFAULT NULL,
    client_feedback TEXT DEFAULT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    release_status ENUM('Released', 'Unreleased') NOT NULL DEFAULT 'Unreleased',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO playback_requests (
    name, phone_no, date_requested, time_requested, location,
    incident_date, incident_time, incident_type, description,
    usefulness, caught_on_cam, agency, client_feedback, image_path, release_status
) VALUES
('Juan Dela Cruz', '09123456789', '2025-03-25', '10:00:00', 'Mall Entrance',
 '2025-03-24', '09:30:00', 'Theft', 'Stolen wallet near entrance',
 'Useful', 'Captured', 'PNP', 'Captured clearly, thief identified.', 'uploads/request1.jpg', 'Released');

-- --------------------------------------------------------
-- ✅ Table: complaints
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    complainant VARCHAR(100) NOT NULL,
    contact_no VARCHAR(20) NOT NULL,
    date_filed DATE NOT NULL,
    incident_date DATE NOT NULL,
    location VARCHAR(255) NOT NULL,
    location_others VARCHAR(255) DEFAULT NULL,
    details TEXT NOT NULL,
    status ENUM('Pending', 'Resolved') NOT NULL DEFAULT 'Pending',
    remarks TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO complaints (complainant, contact_no, date_filed, incident_date, location, details, status, remarks) VALUES
('Carlos Reyes', '09091234567', '2025-03-20', '2025-03-19', 'Parking Lot', 'Car break-in incident', 'Pending', NULL);

-- --------------------------------------------------------
-- ✅ Table: external_services
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS external_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_date DATE NOT NULL,
    description TEXT NOT NULL,
    agency VARCHAR(100) NOT NULL,
    agency_others VARCHAR(255) DEFAULT NULL,
    service_type ENUM('CCTV', 'Maintenance', 'Technical Support') NOT NULL,
    service_type_others VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO external_services (service_date, description, agency, service_type) VALUES
('2025-01-10', 'CCTV camera troubleshooting at mall entrance', 'Tech Solutions', 'CCTV'),
('2025-02-05', 'Routine maintenance of CCTV system', 'Tech Solutions', 'Maintenance'),
('2025-03-15', 'Technical support for CCTV storage issues', 'Support Services', 'Technical Support');

-- --------------------------------------------------------
-- ✅ Table: history
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    source_table ENUM('daily_logs', 'footages') NOT NULL,
    source_id INT NOT NULL,
    details TEXT NOT NULL,
    action_taken TEXT NOT NULL,
    agency VARCHAR(100) NOT NULL,
    agency_others VARCHAR(255) DEFAULT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO history (source_table, source_id, details, action_taken, agency, image_path) VALUES
('daily_logs', 1, 'Stolen wallet near entrance', 'Seen', 'PNP', 'uploads/log1.jpg'),
('footages', 1, 'Stolen wallet near entrance', 'Reviewed', 'PNP', 'uploads/footage1.jpg');

-- --------------------------------------------------------
-- ✅ Table: monthly_troubleshooting_summary
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS monthly_troubleshooting_summary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    month ENUM('January', 'February', 'March', 'April', 'May', 'June', 'July',
               'August', 'September', 'October', 'November', 'December') NOT NULL,
    year INT NOT NULL,
    troubleshooting_count INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO monthly_troubleshooting_summary (month, year, troubleshooting_count) VALUES
('January', 2025, 2),
('February', 2025, 1),
('March', 2025, 3);

-- --------------------------------------------------------
-- ✅ Done
-- --------------------------------------------------------
COMMIT;
