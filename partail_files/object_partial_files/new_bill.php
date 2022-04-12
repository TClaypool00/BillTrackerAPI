<?php
include_once '../../models/BaseClass.php';
include_once '../../models/Bill.php';
include_once '../../config/Database.php';

$database = new Database();
$db = $database->connect();

$bill = new Bill($db);