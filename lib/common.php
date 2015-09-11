<?php
session_start();

define('DROOT', $_SERVER['DOCUMENT_ROOT'] . 'skel/');

require_once DROOT.'lib/Config.php';
require_once DROOT.'lib/Database.php';