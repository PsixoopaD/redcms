<?php
include_once '../../sys/inc/start.php';
dpanel::check_access();

if (!empty($_GET['theme']) && themes::exists($_GET['theme'])) {
    $probe_theme = $_GET['theme'];
}
$doc = new document(2);
if(!$user->access('dpanel_theme')) $doc->access_denied(__('У Вас нет доступа!'));
$doc->title = __('Настройки темы оформления');
$doc->ret(__('Темы оформления'), 'themes.php');
$doc->ret(__('Настр. сайта'), './');
$doc->ret(__('Админка'), '../');


if (empty($_GET['theme']) || !themes::exists($_GET['theme'])) {
    $doc->err(__('Тема оформления не найдена'));
    exit;
}

$theme = themes::getThemeByName($_GET['theme']);

$doc->title = __('Настройки темы оформления "%s"', $theme->getViewName());

if (!is_file(H . '/sys/themes/' . $theme->getName() . '/settings.php')) {
    $doc->err(__('Файл настроек темы оформления не найден'));
    exit;
}

include H . '/sys/themes/' . $theme->getName() . '/settings.php';