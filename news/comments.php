<?php
include_once '../sys/inc/start.php';
if (AJAX)
$doc = new document_json();
else
$doc = new document();
$doc->title = __('Комментарии к новости');
$doc->ret(__('Все новости'), './');
$id = (int) @$_GET['id'];
$q = $db->prepare("SELECT * FROM `news` WHERE `id` = ? LIMIT 1");
$q->execute(Array($id));
if (!$news = $q->fetch()) $doc->access_denied(__('Новость не найдена или удалена'));
$like = $db->query("SELECT * FROM `news_like` WHERE `id_news` = '" . intval($news['id']). "'")->fetchAll();
if (isset($_GET['like']) && $user->id) {
$doc->toReturn(new url('/news/comments.php?id='.$news['id']));
$qq = $db->query("SELECT * FROM `news_like` WHERE `id_user` = '" . intval($user->id) . "' AND `id_news` = '" . intval($news['id']) . "' LIMIT 1")->fetch();
if (!$qq) {
$res = $db->prepare("INSERT INTO `news_like` (`id_user`, `time`, `id_news`) VALUES (?, ?, ?)");
$res->execute(Array(intval($user->id),TIME,intval($news['id'])));
$doc->msg(__('Лайк засчитан'));
if (isset($_GET['return'])) $doc->ret('В тему', text::toValue($_GET['return']));
} else {
$doc->err(__('Лайк уже засчитан'));
if (isset($_GET['return'])) $doc->ret('В тему', text::toValue($_GET['return']));
}
}
include 'news.votes.php';
$listing = new listing();
$post = $listing->post();
$ank = new user((int) $news['id_user']);
$post->icon('news');
$post->content = text::toOutput($news['text']);
$post->title = text::toValue($news['title']);
$post->time = misc::when($news['time']);
$res = $db->prepare("SELECT COUNT(*) FROM `news_like` WHERE `id_news` = ?");
$res->execute(Array(intval($news['id'])));
$countlike = $res->fetchColumn();
$stt = $db->query("SELECT * FROM `news_like` WHERE `id_user` = '$user->id' AND `id_news` = '" . intval($news['id']) . "' LIMIT 1")->fetch();
if ($user->id && $user->id != $ank->id && !$stt) {
$post->bottom = '<a href="?id=' . $news['id'] . '&amp;like">' . __('Мне нравится') . '</a> • <a href="/news/like.php?id=' . $news['id'] . '">' . __('%s чел', $countlike) . '</a>';
} elseif ($user->id && $user->id != $ank->id) {
$countlike = $countlike - 1;
$post->bottom = "<a href='/news/like.php?id=$news[id]'>" . __('Понравилось: Вам и %s чел', $countlike) . "</a>";
} else { 
$post->bottom = $countlike > 0 ? '<a href="/news/like.php?id=' . $news['id'] . '">' . __('Понравилось: %s чел', $countlike) . '</a>' : __('Пока ни кому не понравилось');
}
$post->bottom .= ' Автор: <a href="/profile.view.php?id=' . $news['id_user'] . '">' . $ank->nick() . '</a>';
if (!$news['sended']) {
if ($user->access('news_send_email'))$doc->act(__('Рассылка на e-mail'),  "/dpanel/news/news.send.php?id=$news[id]");
}
if($news['id_vote']){
if ($user->access('news_vote_edit'))$doc->act(__('Редактировать голосование'), 'vote.edit.php?id='.$news['id']);
}else{
if ($user->access('news_vote_new'))$doc->act(__('Создать голосование'), 'vote.new.php?id='.$news['id']);
}
if ($user->access('news_edit'))$doc->act(__('Редактировать'), "/dpanel/news/news.edit.php?id=$news[id]"); // редактирование
if ($user->access('news_delete'))$doc->act(__('Удалить новость'), "/dpanel/news/news.delete.php?id=$news[id]"); // удаление
$listing->display();
$ank = new user($news['id_user']);
$can_write = true;
if (!$user->is_writeable) {
$doc->msg(__('Писать запрещено'), 'write_denied');
$can_write = false;
}
$pages = new pages($db->query("SELECT COUNT(*) FROM  `news_comments` WHERE `id_news` = '".$news['id']."'")->fetchColumn());
if ($can_write  && $pages->this_page == 1) {
if (isset($_POST['send']) && isset($_POST['comment']) && isset($_POST['token']) && $user->group) {
$text = (string) $_POST['comment'];
$users_in_message = text::nickSearch($text);
$text = text::input_text($text);
if (!antiflood::useToken($_POST['token'], 'news')) {
} elseif ($dcms->censure && $mat = is_valid::mat($text)) $doc->err(__('Обнаружен мат: %s', $mat));
elseif ($text) {
$user->balls++;
$res = $db->prepare("INSERT INTO `news_comments` (`id_news`, `id_user`, `time`, `text`) VALUES (?,?,?,?)");
$res->execute(Array($news['id'], $user->id, TIME, $text));
header('Refresh: 1; url=?id=' . $id . '&' . passgen());
$doc->ret(__('Вернуться'), '?id=' . $id . '&amp;' . passgen());
$doc->msg(__('Комментарий успешно отправлен'));
$id_message = $db->lastInsertId();
if ($users_in_message) {
for ($i = 0; $i < count($users_in_message) && $i < 20; $i++) {
$user_id_in_message = $users_in_message[$i];
if ($user_id_in_message == $user->id) {
continue;
}
$ank_in_message = new user($user_id_in_message);
if ($ank_in_message->notice_mention) {
$ank_in_message->mess("[user]{$user->id}[/user] упомянул" . ($user->sex ? '' : 'а') . " о Вас в [url=/news/comments.php?id={$news['id']}#comment{$id_message}]комментарии[/url] к новости");
}
}
}
if ($doc instanceof document_json) {
$doc->form_value('message', '');
$doc->form_value('token', antiflood::getToken('news'));
}
exit;
} else {
$doc->err(__('Комментарий пуст'));
}
if ($doc instanceof document_json)
$doc->form_value('token', antiflood::getToken('news'));
}
if ($user->group) {
$message_form = '';
if (isset($_GET ['com']) && is_numeric($_GET ['com'])) {
$id_message = (int) $_GET ['com'];
$q = $db->prepare("SELECT * FROM `news_comments` WHERE `id` = ? LIMIT 1");
$q->execute(Array($id_message));
if ($messag = $q->fetch()) {
$ank = new user($messag['id_user']);
if (isset($_GET['reply'])) {
$message_form = '@' . $ank->login . ',';
} elseif (isset($_GET['quote'])) {
$message_form = "[quote id_user=\"{$ank->id}\" time=\"{$messag['time']}\"]{$messag['text']}[/quote]";
}
}
}
if (!AJAX) {
$form = new form('?id=' . $id . '&' . passgen());
$form->refresh_url('?id=' . $id . '&' . passgen());
$form->setAjaxUrl('?id=' . $id . '&' . passgen());
$form->hidden('token', antiflood::getToken('news'));
$form->textarea('comment', __('Сообщение'), $message_form, true);
$form->button(__('Отправить'), 'send', false);
$form->display();
}
}
}
$listing = new listing();
if (!empty($form))
$listing->setForm($form);
$q = $db->prepare("SELECT * FROM `news_comments` WHERE `id_news` = ? ORDER BY `id` DESC LIMIT $pages->limit");
$q->execute(Array($news['id']));
$after_id = false;
if ($arr = $q->fetchAll()) {
foreach ($arr AS $message) {
$ank = new user($message['id_user']);
$post = $listing->post();
$post->id = 'news_' . $message['id'];
$post->url = '/profile.view.php?id=' . $message['id_user'];
$post->time = misc::when($message['time']);
$post->title = $ank->nick();
$post->image = $ank->ava();
$post->post = text::toOutput($message['text']);
if ($user->group) {
$post->action('reply', '?id=' . $news['id'] . '&amp;com=' . $message['id'] . '&amp;reply');
$post->action('quote', '?id=' . $news['id'] . '&amp;com=' . $message['id'] . '&amp;quote');
}
if($user->access('news_delete_comment'))$post->action('delete', "comment.delete.php?id=$message[id]&amp;return=" . URL);
if (!$doc->last_modified)
$doc->last_modified = $message['time'];
if ($doc instanceof document_json)
$doc->add_post($post, $after_id);
$after_id = $post->id;
}
}
if ($doc instanceof document_json && !$arr){
$post = new listing_post(__('Комментарии отсутствуют'));
$post->icon('empty');
$doc->add_post($post);
}
$listing->setAjaxUrl('?id=' . $id . '&amp;page=' . $pages->this_page);
$listing->display(__('Комментарии отсутствуют'));
$pages->display('?id=' . $id . '&amp;'); // вывод страниц
if ($doc instanceof document_json)
$doc->set_pages($pages);