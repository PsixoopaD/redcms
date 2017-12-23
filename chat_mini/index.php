<?php
include_once '../sys/inc/start.php';
if (AJAX)
$doc = new document_json();
else
$doc = new document();
$doc->title = __('Мини чат');

$pages = new pages($db->query("SELECT COUNT(*) FROM `chat_mini`")->fetchColumn());
$can_write = true;
if (!$user->is_writeable) {
$doc->msg(__('Писать запрещено'), 'write_denied');
$can_write = false;
}
if ($can_write && $pages->this_page == 1) {
if (isset($_POST['send']) && isset($_POST['message']) && isset($_POST['token']) && $user->group) {
$message = (string)$_POST['message'];
$users_in_message = text::nickSearch($message);
$message = text::input_text($message);
if (!antiflood::useToken($_POST['token'], 'chat_mini')) {
// нет токена (обычно, повторная отправка формы)
} elseif ($dcms->censure && $mat = is_valid::mat($message)) {
$doc->err(__('Обнаружен мат: %s', $mat));
} elseif ($user->group < 2 && (empty($_POST ['captcha']) || empty($_POST ['captcha_session']) || !captcha::check($_POST ['captcha'], $_POST ['captcha_session']))){
        $doc->err(__('Проверочное число введено неверно'));
    } elseif ($message) {

$user->balls += $dcms->add_balls_chat ;
$res = $db->prepare("INSERT INTO `chat_mini` (`id_user`, `time`, `message`) VALUES (?, ?, ?)");
$res->execute(Array($user->id, TIME, $message));
header('Refresh: 1; url=?' . passgen() . '&' . SID);
$doc->ret(__('Вернуться'), '?' . passgen());
$doc->msg(__('Сообщение успешно отправлено'));
if ($doc instanceof document_json) {
$doc->form_value('message', '');
$doc->form_value('token', antiflood::getToken('chat_mini'));
}
exit;
} else {
$doc->err(__('Сообщение пусто'));
}
if ($doc instanceof document_json)
$doc->form_value('token', antiflood::getToken('chat_mini'));
}
if ($user->group) {
$message_form = '';
if (isset($_GET ['message']) && is_numeric($_GET ['message'])) {
$id_message = (int)$_GET ['message'];
$q = $db->prepare("SELECT * FROM `chat_mini` WHERE `id` = ? LIMIT 1");
$q->execute(Array($id_message));
if ($message = $q->fetch()) {
$ank = new user($message['id_user']);
if (isset($_GET['reply'])) {
$message_form = '@' . $ank->login . ',';
} elseif (isset($_GET['quote'])) {
$message_form = "[quote id_user=\"{$ank->id}\" time=\"{$message['time']}\"]{$message['message']}[/quote]";
}
}
}
if (!AJAX) {
$form = new form(new url());
$form->refresh_url('?' . passgen());
$form->setAjaxUrl('?');
$form->hidden('token', antiflood::getToken('chat_mini'));
$form->textarea('message', __('Сообщение'), $message_form, true);
if($user->group < 2) $form->captcha();
$form->button(__('Отправить'), 'send', false);
$form->display();
}
}
}
$listing = new listing();
// привязываем форму к листингу, чтобы листинг мог обновиться при отправке формы через AJAX
if (!empty($form))
$listing->setForm($form);
$q = $db->query("SELECT * FROM `chat_mini` ORDER BY `id` DESC LIMIT ".$pages->limit);
$after_id = false;
if ($arr = $q->fetchAll()) {
foreach ($arr AS $message) {
$ank = new user($message['id_user']);
$post = $listing->post();
$post->id = 'chat_post_' . $message['id'];
if($user->id)$post->url = '?message=' .$message['id'] . '&amp;reply';
$post->time = misc::when($message['time']);
$post->title = $ank->nick();
$post->image = $ank->ava();
$post->post = text::toOutput($message['message']);

if($user->id) { 
$post->action('quote', '?message=' . $message['id'] . '&amp;quote');
if($user->access('chat_del_post'))$post->action('delete', 'message.delete.php?id='.$message['id'].'' );
$post->action('user.1', '/profile.view.php?id='.$ank->id);
}

if (!$doc->last_modified)
$doc->last_modified = $message['time'];
if ($doc instanceof document_json)
$doc->add_post($post, $after_id);
$after_id = $post->id;
}
}
if ($doc instanceof document_json && !$arr){
$post = new listing_post(__('Сообщения отсутствуют'));
$post->icon('empty');
$doc->add_post($post);
}
$listing->setAjaxUrl('?page=' . $pages->this_page);
$listing->display(__('Сообщения отсутствуют'));
$pages->display('?'); // вывод страниц
if ($doc instanceof document_json)
$doc->set_pages($pages);
if($user->access('chat_del_post_all'))$doc->act(__('Удаление сообщений'), 'message.delete_all.php');