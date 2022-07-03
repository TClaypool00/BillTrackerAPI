<?php
include_once '../../models/ValidateClass.php';
include_once '../../models/BaseClass.php';
include_once '../../models/All.php';
include_once '../../config/Database.php';

$database = new Database();
$db = $database->connect();

$all = new All($db);