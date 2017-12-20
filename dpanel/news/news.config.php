<?php
include_once '../../sys/inc/start.php';
dpanel::check_access();
$doc = new document(2);
if(!$user->access('news_settings')) $doc->access_denied(__('У Вас нет доступа!'));
$doc->title = __('Настройки новостей');
$doc->ret(__('Админка'), '../');
$doc->ret(__('Новости'), '/news/');
if (isset($_POST ['save'])) {
if (empty($_POST ['captcha_session']) || !captcha::check($_POST ['captcha'], $_POST ['captcha_session'])) {
$doc->err(__('Проверочное число введено неверно'));
} else {
$dcms->log('Новости', 'Редактирование параметров раздела новостей');
$dcms->count_news = (int) $_POST['count_news'];
$dcms->save_settings($doc);
$filename = '../sys/tmp/cache.widgets_content.ser';
if ( !(@unlink($filename)) ) die('Кеш виджетов уже очищен');
$doc->msg(__('Настройки успешно изменены. Кеш обновлен.'));
header('Refresh: 1; url=/news/config.php');
}
}
$form = new form('?' . passgen());
$options = array();
$options [] = array(1, __('1'), $dcms->count_news == 1);
$options [] = array(2, __('2'), $dcms->count_news == 2);
$options [] = array(3, __('3'), $dcms->count_news == 3);
$options [] = array(4, __('4'), $dcms->count_news == 4);
$options [] = array(5, __('5'), $dcms->count_news == 5);
$options [] = array(6, __('6'), $dcms->count_news == 6);
$options [] = array(7, __('7'), $dcms->count_news == 7);
$options [] = array(8, __('8'), $dcms->count_news == 8);
$form->select('count_news', __('Сколько выводит новостей на главной?'), $options);
$form->captcha();
$form->button(__('Применить настройки'), 'save');
$form->display();
