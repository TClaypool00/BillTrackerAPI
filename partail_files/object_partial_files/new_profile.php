<?php
include_once '../../models/ValidateClass.php';
include_once '../../models/BaseClass.php';
include_once '../../models/Proflie.php';
include_once '../../config/Database.php';

$database = new Database();
$db = $database->connect();

$profile = new Proflie($db);