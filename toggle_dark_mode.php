<?php

include_once 'App.php';
App::init();

include_once 'services/StatusMessageService.php';

$_SESSION['darkTheme'] = !($_SESSION['darkTheme'] ?? false);