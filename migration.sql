-- Adicionar tabela de clientes
ALTER TABLE projects ADD COLUMN customer_id INT AFTER id;
ALTER TABLE projects ADD COLUMN maintenance_monthly_value DECIMAL(10,2) DEFAULT 0.00 AFTER maintenance_end_date;
ALTER TABLE projects ADD COLUMN payment_received_date DATE AFTER maintenance_monthly_value;

-- Adicionar constraint de foreign key
ALTER TABLE projects ADD CONSTRAINT fk_projects_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL;

-- Se necessário, criar a tabela customers separadamente
-- CREATE TABLE IF NOT EXISTS customers (
--     id INT PRIMARY KEY AUTO_INCREMENT,
--     name VARCHAR(255) NOT NULL,
--     phone VARCHAR(20),
--     email VARCHAR(255),
--     observations TEXT,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
-- );
