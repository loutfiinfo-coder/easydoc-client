<?php
require 'config.php'; // your file with $bdd connection

// Set database name
$dbNameQuery = $bdd->query("SELECT DATABASE() AS dbname");
$dbName = $dbNameQuery->fetch(PDO::FETCH_ASSOC)['dbname'];

echo "Converting database '$dbName' to utf8mb4...\n";

// 1. Change the database default charset and collation
$bdd->exec("ALTER DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

// 2. Fetch all tables
$tables = $bdd->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    echo "Processing table `$table`...\n";

    // 3. Change table default charset and collation
    $bdd->exec("ALTER TABLE `$table` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    // 4. Optional: Change each column individually if needed
    $columns = $bdd->query("SHOW FULL COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($columns as $col) {
        // Only change text-based columns
        $type = strtolower($col['Type']);
        if (preg_match('/^(varchar|text|mediumtext|longtext|char|tinytext)/', $type)) {
            $columnName = $col['Field'];
            $bdd->exec("ALTER TABLE `$table` MODIFY `$columnName` {$col['Type']} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci" . ($col['Null'] == 'NO' ? ' NOT NULL' : ''));
        }
    }
}

echo "All done. Database, tables, and text columns are now utf8mb4.\n";
