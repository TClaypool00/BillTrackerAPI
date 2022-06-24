<?php
include_once '../../models/ValidateClass.php';
include_once '../../models/BaseClass.php';
include_once '../../models/Company.php';
include_once '../../config/Database.php';

$database = new Database();
$db = $database->connect();

$company = new Company($db);
