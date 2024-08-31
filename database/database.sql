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


-- PRODUCTS
CREATE TABLE products (
    id VARCHAR(100) NOT NULL PRIMARY key,
    name VARCHAR(100) NOT NULL,
    description text null,
    price int not null,
    category_id VARCHAR(100) not null,
    created_at timestamp not null default current_timestamp,
    constraint fk_category_id foreign key (category_id) references categories(id)
)engine innodb;