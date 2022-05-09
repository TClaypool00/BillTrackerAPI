<?php
include_once '../../models/BaseClass.php';
include_once '../../models/Subscription.php';
include_once '../../config/Database.php';

$database = new Database();
$db = $database->connect();

$sub = new Subscription($db);