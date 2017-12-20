<?php
include_once '../../sys/inc/start.php';
dpanel::check_access();
$doc = new document(2);
$doc->title = __('Удаление новости');
$id = (int)@$_GET['id'];
$doc->ret(__('Вернутся в новость'), '../news/comments.php?id='.$id);
$q = $db->prepare("SELECT * FROM `news` WHERE `id` = ? LIMIT 1");
$q->execute(Array($id));
if (!$news = $q->fetch())
$doc->access_denied(__('Новость не найдена или уже удалена'));
$ank = new user($news['id_user']);
if ($ank->group > $user->group || !$user->access('news_delete'))
$doc->access_denied(__('У Вас нет прав для удаления данной новости'));
if (isset($_POST['delete'])) {
if (empty($_POST['captcha']) || empty($_POST['captcha_session']) || !captcha::check($_POST['captcha'], $_POST['captcha_session'])) {
$doc->err(__('Проверочное число введено неверно'));
} else {
$dcms->log('Новости', 'Удаление новости '.$news['title']);
$res = $db->prepare("DELETE FROM `news` WHERE `id` = ? LIMIT 1");
$res->execute(Array($id));
$res = $db->prepare("DELETE FROM `news_comments` WHERE `id_news` = ?");
$res->execute(Array($id));
$doc->msg(__('Новость успешно удалена'));
header('Refresh: 1; url=/news/');
exit;
}
}
$form = new form(new url());
$form->captcha();
$form->bbcode(__('Новость "%s" будет удалена без возможности восстановления', $news['title']));
$form->button(__('Удалить'), 'delete');
$form->display();