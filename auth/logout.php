<?php
require_once __DIR__ . '/../includes/session.php';

destroyAppSession();

header('Location: login.php');
exit();
