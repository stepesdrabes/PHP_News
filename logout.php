<?php

include_once 'App.php';
App::init();

include_once 'services/StatusMessageService.php';
include_once 'services/AuthService.php';

if (AuthService::is_logged_in()) {
    AuthService::logout();
}

StatusMessageService::create_success_popup('Odhlášení bylo úspěšné');
App::redirect('index.php');