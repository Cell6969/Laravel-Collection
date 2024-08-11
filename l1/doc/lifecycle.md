# Lifecycle Laravel

## 1. public/index.php
entry point pertama berasal dari public/index.php

index.php hanya bertujuan untuk me-load framework laravel dan menjalankan kode program yang dibuat

## 2. Kernel
Dari index.php request dilanjutkan ke class Kernel. Pada kernel terdapat dua Class yaitu HTTP Kernel dan Console Kernel

## 3. Service Provider
Kernel merupakan core dari logic aplikasi dimana semua request masuk akan ditangani sampai tahap response. Kernel melakukan beberapa tahapan dimulai dari me-load Service Provider dst. Service Provider bertanggung jawab melakukan bootstrapping semua komponen seperti database, queue, validation, routing, dll.