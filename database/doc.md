# Laravel Database

## Config Database 
buat database mysql:
```sql
CREATE DATABASE laravel_database;

USE DATABASE laravel_database;
```
masukkan config ke .env

## Create Table
buat table categories:
```sql
CREATE TABLE categories (
    id VARCHAR(100) not NULL primary key,
    name VARCHAR(100) not null,
    description text,
    created_at timestamp
)
```
