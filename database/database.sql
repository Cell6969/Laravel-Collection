-- CREATE DATABASE laravel_database;

-- USE laravel_database;

-- SHOW tables;

CREATE TABLE categories (
    id VARCHAR(100) not NULL primary key,
    name VARCHAR(100) not null,
    description text,
    created_at timestamp
)