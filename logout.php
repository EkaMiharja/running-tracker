<?php
require_once 'config/database.php';
session_start();
session_destroy();
redirect('landing/index.html');
