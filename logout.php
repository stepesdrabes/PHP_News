<?php

include_once 'services/SuccessMessageService.php';
include_once 'services/AuthService.php';
AuthService::init();

if (AuthService::is_logged_in()) {
    AuthService::logout();
}

SuccessMessageService::create_popup_message(
    'fi-br-check',
    'Úspěch',
    'Odhlášení bylo úspěšné.',
    '#55d066',
    '#226329'
);

header('location: index.php');