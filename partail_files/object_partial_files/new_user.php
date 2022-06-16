<?php
include_once '../../models/ValidateClass.php';
include_once '../../models/BaseClass.php';
include_once '../../models/User.php';
include_once '../../config/Database.php';

$database = new Database();
$db = $database->connect();

$user = new User($db);