-- init.sql: crea tablas básicas para el proyecto
CREATE DATABASE IF NOT EXISTS crowdfunding_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE crowdfunding_db;

-- Usuarios
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('emprendedor','inversionista','admin') NOT NULL DEFAULT 'inversionista',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Categorías
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Campañas
CREATE TABLE IF NOT EXISTS campaigns (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(200) NOT NULL,
  description TEXT NOT NULL,
  category_id INT DEFAULT NULL,
  goal_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
  pledged_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
  status ENUM('draft','pending','approved','rejected') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  end_date DATE DEFAULT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Recompensas
CREATE TABLE IF NOT EXISTS rewards (
  id INT AUTO_INCREMENT PRIMARY KEY,
  campaign_id INT NOT NULL,
  title VARCHAR(200) NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  description VARCHAR(255) DEFAULT NULL,
  FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Donaciones
CREATE TABLE IF NOT EXISTS donations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  campaign_id INT NOT NULL,
  user_id INT NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  reward_id INT DEFAULT NULL,
  payment_status ENUM('pending','completed','failed') NOT NULL DEFAULT 'completed',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (reward_id) REFERENCES rewards(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Datos iniciales de categorías
INSERT IGNORE INTO categories (name) VALUES
('Tecnología'), ('Educación'), ('Salud'), ('Arte'), ('Comunidad');
