CREATE DATABASE IF NOT EXISTS lobsterdb;

USE lobsterdb;

CREATE TABLE `login_users` (
    `fullname` VARCHAR(30) NOT NULL,
    `email` VARCHAR(50) NOT NULL,
    `dob` VARCHAR(10) NOT NULL,
    `gender` VARCHAR(1) NOT NULL,
    `username` VARCHAR(30) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`username`),
    UNIQUE (`email`)
) ENGINE=MyISAM;

CREATE TABLE `user_details` (
    `username` VARCHAR(30) NOT NULL,
    `fullname` VARCHAR(30) NOT NULL,
    `email` VARCHAR(30) NOT NULL,
    `bio` VARCHAR(255) NULL DEFAULT NULL,
    `profile_pic` MEDIUMBLOB NULL DEFAULT NULL,
    PRIMARY KEY (`username`),
    INDEX (`username`),
    FOREIGN KEY (`username`) REFERENCES `login_users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MyISAM;

CREATE TABLE `follow_details` (
    `requested_user` VARCHAR(30) NOT NULL,
    `following_user` VARCHAR(30) NOT NULL,
    `request_condition` VARCHAR(1) NOT NULL,
    `date_time` VARCHAR(19) NOT NULL,
    `id` INT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE TABLE `post_details` (
    `post_id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(30) NOT NULL,
    `post_media` LONGBLOB NOT NULL,
    `post_caption` TEXT NOT NULL,
    `post_date` VARCHAR(19) NOT NULL,
    PRIMARY KEY (`post_id`),
    INDEX (`username`),
    FOREIGN KEY (`username`) REFERENCES `login_users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MyISAM;
