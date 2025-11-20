<?php
// Script opcional para crear columnas o tablas extras si es necesario.
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth/helpers.php';
require_role('admin');

$queries = [
  "ALTER TABLE users ADD COLUMN IF NOT EXISTS nickname VARCHAR(100) DEFAULT NULL",
  // messages table (already handled elsewhere)
];

foreach($queries as $q){
    $db->query($q);
}

echo "Instalación extra ejecutada (no hay cambios críticos).";
?>
