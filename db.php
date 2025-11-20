<?php
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'crowdfunding_db';

$db = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($db->connect_errno) {
    die("Error al conectar a la base de datos: (" . $db->connect_errno . ") " . $db->connect_error);
}
$db->set_charset('utf8mb4');
?>