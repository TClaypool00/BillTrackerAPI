<?php
include_once '../../models/ValidateClass.php';
include_once '../../models/BaseClass.php';
include_once '../../models/BaseCommunityClass.php';
include_once '../../models/Community.php';
include_once '../../config/Database.php';

$database = new Database();
$db = $database->connect();

$comm = new Community($db);