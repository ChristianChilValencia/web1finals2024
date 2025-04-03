<?php

    $host = 'db';
    $dbname = 'mybd';
    $user = 'root';
    $pass = 'secret';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        echo "Connected to the database successfully!<br>";

        // Create a sample table
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (id INT AUTO INCREMENT PRIMARY KEY, name VARCHAR(255))")
        echo "Created the users table...";
    } catch(PDOException $error) {
        echo "Database connection failed: "
    }