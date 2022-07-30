<?php
include_once '../../models/ValidateClass.php';
include_once '../../models/BaseClass.php';
include_once '../../models/Post.php';
include_once '../../config/Database.php';

$database = new Database();
$db = $database->connect();

$post = new Post($db);