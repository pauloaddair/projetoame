CREATE DATABASE IF NOT EXISTS crm_db;
USE crm_db;

-- Promoters table
CREATE TABLE promoters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    cnpj VARCHAR(20),
    website VARCHAR(255),
    main_phone VARCHAR(20),
    email VARCHAR(255),
    address TEXT,
    segment VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Exhibitors table
CREATE TABLE exhibitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    cnpj VARCHAR(20),
    website VARCHAR(255),
    main_phone VARCHAR(20),
    email VARCHAR(255),
    address TEXT,
    segment VARCHAR(100),
    lead_source VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Events table
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    start_date DATE,
    end_date DATE,
    city VARCHAR(100),
    state VARCHAR(50),
    venue VARCHAR(255),
    promoter_id INT,
    website VARCHAR(255),
    main_segment VARCHAR(100),
    exhibitors_list TEXT,
    observations TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (promoter_id) REFERENCES promoters(id)
);

-- Contacts table
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    position VARCHAR(100),
    email VARCHAR(255),
    phone VARCHAR(20),
    linkedin VARCHAR(255),
    observations TEXT,
    entity_type ENUM('promoter', 'exhibitor'),
    entity_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Opportunities table
CREATE TABLE opportunities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    entity_type ENUM('promoter', 'exhibitor'),
    entity_id INT,
    stage VARCHAR(50),
    event_id INT,
    assigned_to INT,
    expected_close_date DATE,
    value DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id)
);

-- Interactions table
CREATE TABLE interactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_time DATETIME,
    type VARCHAR(50),
    user_id INT,
    contact_id INT,
    entity_type ENUM('promoter', 'exhibitor'),
    entity_id INT,
    event_id INT,
    subject VARCHAR(255),
    description TEXT,
    next_steps TEXT,
    follow_up_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (contact_id) REFERENCES contacts(id),
    FOREIGN KEY (event_id) REFERENCES events(id)
);