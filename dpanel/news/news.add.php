<?php
include_once '../../sys/inc/start.php';
dpanel::check_access();
$doc = new document(2);
if(!$user->access('news_add')) $doc->access_denied(__('У Вас нет доступа!'));
$doc->title = __('Создание новости');
$doc->ret(__('Админка'), '../');
$doc->ret(__('К новостям'), '/news/');

if (isset($_POST['send']) && isset($_POST['title']) && isset($_POST['text'])) {
 $title = text::for_name($_POST['title']);
    $text = text::input_text($_POST['text']);
  
    if (!$title){$doc->err(__('Заполните "Заголовок новости"'));}elseif (!$text) {$doc->err(__('Заполните "Полное описание новости"'));}else {
        $res = $db->prepare("INSERT INTO `news` (`title`, `time`, `text`,`id_user`) VALUES (?,?,?,?)");
        $res->execute(Array($title, TIME, $text, $user->id));
        $doc->msg(__('Новость успешно опубликована'));
 $id = $db->lastInsertId();
$dcms->log('Новости', 'Создание новости '.$title);
        header('Refresh: 1; /news/comments.php?id='.$id);
        exit;
    }
} 
$form = new form('?' . passgen());
$form->text('title', __('Заголовок новости'));
$form->textarea('text', __('Описание новости'));
$form->button(__('Опубликовать'), 'send', false);
$form->display();