CREATE DATABASE mrp_project_manager;
USE mrp_project_manager;

CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(255),
    observations TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT,
    project_name VARCHAR(255) NOT NULL,
    project_type ENUM('site_manutencao', 'site_apenas', 'manutencao') NOT NULL,
    description TEXT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    maintenance_start_date DATE,
    maintenance_end_date DATE,
    maintenance_monthly_value DECIMAL(10,2) DEFAULT 0.00,
    payment_received_date DATE,
    total_budget DECIMAL(15,2) DEFAULT 0.00,
    current_spent DECIMAL(15,2) DEFAULT 0.00,
    status ENUM('planejamento', 'em_andamento', 'concluido', 'atrasado', 'cancelado') DEFAULT 'planejamento',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

CREATE TABLE activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    activity_name VARCHAR(255) NOT NULL,
    description TEXT,
    observation TEXT,
    activity_date DATE NOT NULL,
    activity_type ENUM('desenvolvimento', 'manutencao', 'reuniao', 'teste', 'outro') NOT NULL,
    hours_spent DECIMAL(5,2) DEFAULT 0.00,
    cost DECIMAL(10,2) DEFAULT 0.00,
    responsible_person VARCHAR(100),
    status ENUM('pendente', 'em_andamento', 'concluido') DEFAULT 'pendente',
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

CREATE TABLE project_costs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    cost_type ENUM('material', 'mao_de_obra', 'software', 'infraestrutura', 'outro') NOT NULL,
    description VARCHAR(255),
    amount DECIMAL(10,2) NOT NULL,
    cost_date DATE NOT NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

CREATE TABLE project_access_credentials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    access_type VARCHAR(100) NOT NULL COMMENT 'ex: FTP, SSH, Database, CMS, etc',
    server_url VARCHAR(255),
    username VARCHAR(255),
    password VARCHAR(255),
    port INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);