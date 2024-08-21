-- CREATE DATABASE laravel_database;

-- USE laravel_database;

-- SHOW tables;

-- Create Categories
CREATE TABLE categories (
    id VARCHAR(100) not NULL primary key,
    name VARCHAR(100) not null,
    description text,
    created_at timestamp
)

-- COUNTERS

CREATE TABLE counters (
    id VARCHAR(100) NOT NULL primary key,
    counter int not null default 0
) engine innodb;

insert into counters(id, counter) values ('sample', 0);

select * from counters;