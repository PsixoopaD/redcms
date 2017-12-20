<?php
include_once '../../sys/inc/start.php';
dpanel::check_access();
$doc = new document(2);
$doc->title = __('Редактирование новости');
if (!$user->access('news_edit'))
$doc->access_denied(__('У Вас нет прав для редактирования данной новости'));

$id = (int) @$_GET['id'];
$doc->ret(__('Вернутся в новость'), '/news/comments.php?id='.$id);
$q = $db->prepare("SELECT * FROM `news` WHERE `id` = ? LIMIT 1");
$q->execute(Array($id));
if (!$news = $q->fetch())
$doc->access_denied(__('Новость не найдена или удалена'));
$ank = new user($news['id_user']);


if (isset($_POST['send']) && isset($_POST['title'])  && isset($_POST['text'])) {
$title = text::for_name($_POST['title']);
$text = text::input_text($_POST['text']);
if (!$title){
$doc->err(__('Заполните "Заголовок новости"'));}elseif(!$text){
$doc->err(__('Заполните "Текст новости"'));}else{
$dcms->log('Новости', 'Редактирование новости '.$news['title']);
$res = $db->prepare("UPDATE `news` SET `title` = ?, `id_user` = ?, `text` = ?, `sended` = '0' WHERE `id` = ? LIMIT 1");
$res->execute(Array($title, $user->id, $text, $id));
$doc->msg(__('Новость успешно отредактирована'));
}
header('Refresh: 1; ?id='.$id);
exit;
}

$form = new form(new url());
$form->text('title', __('Заголовок новости'), $news['title']);
$form->textarea('text', __('Текст новости'), $news['text']);
$form->button(__('Применить'), 'send', false);
$form->display();