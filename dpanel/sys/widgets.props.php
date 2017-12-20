<?php

include_once '../../sys/inc/start.php';
dpanel::check_access();
$doc = new document(2);
if(!$user->access('dpanel_set_widgets')) $doc->access_denied(__('У Вас нет доступа!'));
$doc->title = __('Виджеты');
$doc->ret(__('Настр. сайта'), './');
$doc->ret(__('Админка'), '../');

$types = array('light', 'mobile', 'full');

if (isset($_POST ['save'])) {
    foreach ($types AS $type) {
        $prop_name = "widget_items_count_" . $type;
        $dcms->$prop_name = min(max((int)$_POST [$prop_name], 0), 50);
    }

    if ($dcms->save_settings()) {
        $doc->msg(__('Настройки успешно сохранены'));
    } else {
        $doc->err(__('Нет прав на запись в файл настроек'));
    }
}

$form = new form('?' . passgen());
foreach ($types AS $type) {
    $prop_name = "widget_items_count_" . $type;
    $form->text($prop_name, __('Макс. кол-во пунктов в виджете') . ' [0-50] (' . strtoupper($type) . ')', $dcms->$prop_name);
}
$form->button(__('Применить'), 'save');
$form->display();