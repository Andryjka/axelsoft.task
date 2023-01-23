<?php

if (!check_bitrix_sessid()) {
    return;
}

if ($errorException = $APPLICATION->getException()) {
    CAdminMessage::showMessage(
        'Ошибка при установке модуля: ' . $errorException->GetString()
    );
} else {
    CAdminMessage::showNote(
        'Модуль успешно установлен'
    );
}
?>