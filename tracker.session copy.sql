-- Active: 1764858635456@@127.0.0.1@3306@tracker
use tracker;


CREATE DATABASE Kari;

use Kari;



CREATE TABLE IF NOT EXISTS users(
  id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
  name VARCHAR(50) NOT NULL,
  UserName VARCHAR(30) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  Email VARCHAR(60) NOT NULL,
  Password VARCHAR(255) NOT NULL
);

ALTER TABLE users DROP COLUMN role;

ALTER TABLE users ADD COLUMN role ENUM('visitor', 'traveler', 'host', 'admin') NOT NULL DEFAULT 'visitor';

CREATE TABLE favorites(
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  rental_id INT NOT NULL,
  acc_name VARCHAR(100) NOT NULL,
  CONSTRAINT FK_rental_id FOREIGN KEY(rental_id) REFERENCES users(id)
);


CREATE TABLE profile(
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  photo MEDIUMBLOB NOT NULL,
  bio TEXT NOT NULL DEFAULT "no bio there",
  CONSTRAINT FK_user_id FOREIGN KEY(user_id) REFERENCES users(id)
);


CREATE TABLE IF NOT EXISTS reviews(
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  username VARCHAR(50) NOT NULL,
  comment TEXT NOT NULL,
  review INT NOT NULL,
  CONSTRAINT FK_userC_id FOREIGN KEY(user_id) REFERENCES users(id) 
);


CREATE TABLE IF NOT EXISTS accommodation(
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  host_id INT NOT NULL,
  host_name VARCHAR(50) NOT NULL,
  start_date DATE NOT NULL DEFAULT CURRENT_TIME,
  end_date DATE NOT NULL DEFAULT CURRENT_TIME,
  location VARCHAR(255) NOT NULL,
  price DECIMAL(10.2) NOT NULL,
  CONSTRAINT FK_host_id FOREIGN KEY(host_id) REFERENCES users(id) 
);

CREATE TABLE IF NOT EXISTS images(
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  acc_id INT NOT NULL,
  image MEDIUMBLOB NOT NULL,
  CONSTRAINT FK_acc_id FOREIGN KEY(acc_id) REFERENCES accommodation(id) 
);

ALTER TABLE images DROP COLUMN IF EXISTS description;
ALTER TABLE accommodation DROP COLUMN IF EXISTS is_available;


SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE accommodation ADD COLUMN is_available ENUM('Available', 'Booked') NOT NULL DEFAULT 'Available';

ALTER TABLE accommodation DROP COLUMN favorites;
ALTER TABLE accommodation DROP COLUMN is_available;
ALTER TABLE accommodation ADD COLUMN favorites BOOLEAN NOT NULL DEFAULT false;
ALTER TABLE accommodation ADD COLUMN guest_count BOOLEAN NOT NULL DEFAULT 1;
ALTER TABLE accommodation ADD COLUMN status ENUM("Confirmed", "Pending", "Cancelled") NOT NULL DEFAULT "Pending";
ALTER TABLE reservations ADD COLUMN reservation_status ENUM("Confirmed", "Pending", "Canceled") NOT NULL DEFAULT "Pending";
ALTER TABLE reservations ADD COLUMN reservation_status ;

ALTER TABLE users ADD COLUMN status ENUM("Active", "Inactive") NOT NULL DEFAULT "Active";
ALTER TABLE users ADD COLUMN status ENUM("Active", "Inactive") NOT NULL DEFAULT "Active";

ALTER TABLE reviews ADD COLUMN rental_id INT;
ALTER TABLE reviews ADD COLUMN  rating INT(1);
ALTER TABLE reviews ADD COLUMN  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE accommodation DROP COLUMN rental_status;

ALTER TABLE reviews
ADD CONSTRAINT fk_rentalR_id
FOREIGN KEY (rental_id)
REFERENCES accommodation(id)
ON DELETE CASCADE;

CREATE TABLE reservations (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  rental_id INT,
  user_id INT,
  start_date DATE NOT NULL DEFAULT CURRENT_TIME,
  end_date DATE NOT NULL DEFAULT CURRENT_TIME,
  CONSTRAINT FK_rentalRE_id FOREIGN KEY(rental_id) REFERENCES accommodation(id),
  CONSTRAINT FK_userRE_id FOREIGN KEY(user_id) REFERENCES users(id)
);


ALTER TABLE reservations ADD COLUMN name VARCHAR(50) NOT NULL;
TRUNCATE TABLE users;

TRUNCATE TABLE accommodation;

