<?php
include_once '../../models/BaseClass.php';
include_once '../../models/Miscellaneous.php';
include_once '../../config/Database.php';

$database = new Database();
$db = $database->connect();

$misc = new Miscellaneous($db);