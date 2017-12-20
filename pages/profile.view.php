<?php
include_once '../sys/inc/start.php';
if (AJAX)
$doc = new document_json();
else
$doc = new document();
$doc->title = __('Анкета');
$ank = (empty($_GET ['id'])) ? $user : new user((int)$_GET ['id']);
if (!$ank->group)
$doc->access_denied(__('Нет данных'));
$doc->title = ($user->id && $ank->id == $user->id)? __('Моя анкета') : __('Анкета "%s"', $ank->nick);
$doc->description = __('Анкета "%s"', $ank->nick);
$doc->keywords [] = $ank->nick;
if ($user->group > $ank->group)$doc->act(__('Доступные действия'), "/dpanel/user/user.actions.php?id={$ank->id}");
if ($user->group && $ank->id && $user->id != $ank->id && isset($_GET ['friend'])) {
$q = $db->prepare("SELECT * FROM `friends` WHERE `id_user` = ? AND `id_friend` = ? LIMIT 1");
$q->execute(Array($user->id, $ank->id));
if ($friend = $q->fetch()) {
if ($friend ['confirm']) {
if (isset($_POST ['delete'])) {
$res = $db->prepare("DELETE FROM `friends` WHERE `id_user` = ? AND `id_friend` = ? OR `id_user` = ? AND `id_friend` = ?");
$res->execute(Array($user->id, $ank->id, $ank->id, $user->id));
$doc->msg(__('Пользователь успешно удален из друзей'));
}
} else {
if (isset($_POST ['no'])) {
$res = $db->prepare("DELETE FROM `friends` WHERE `id_user` = ? AND `id_friend` = ? OR `id_user` = ? AND `id_friend` = ?");
$res->execute(Array($user->id, $ank->id, $ank->id, $user->id));
$res = $db->prepare("UPDATE `users` SET `friend_new_count` = `friend_new_count` - '1' WHERE `id` = ? LIMIT 1");
$res->execute(Array($user->id));
$doc->msg(__('Предложение дружбы отклонено'));
} elseif (isset($_POST ['ok'])) {
$res = $db->prepare("UPDATE `friends` SET `confirm` = '1' WHERE `id_user` = ? AND `id_friend` = ? LIMIT 1");
$res->execute(Array($user->id, $ank->id));
$res = $db->prepare("UPDATE `users` SET `friend_new_count` = `friend_new_count` - '1' WHERE `id` = ? LIMIT 1");
$res->execute(Array($user->id));
$res = $db->prepare("INSERT INTO `friends` (`confirm`, `id_user`, `id_friend`) VALUES ('1', ?, ?)");
$res->execute(Array($ank->id, $user->id));
$doc->msg(__('Предложение дружбы принято'));
}
}
} else {
if (isset($_GET ['friend']) && isset($_POST ['add'])) {
$res = $db->prepare("INSERT INTO `friends` (`confirm`, `id_user`, `id_friend`) VALUES ('0', ?, ?)");
$res->execute(Array($ank->id, $user->id));
$res = $db->prepare("UPDATE `users` SET `friend_new_count` = `friend_new_count` + '1' WHERE `id` = ? LIMIT 1");
$res->execute(Array($ank->id));
$doc->msg(__('Предложение дружбы успешно отправлено'));
}
}
}
if ($user->group && $ank->id && $user->id != $ank->id) {
$q = $db->prepare("SELECT * FROM `friends` WHERE `id_user` = ? AND `id_friend` = ? LIMIT 1");
$q->execute(Array($user->id, $ank->id));
if ($friend = $q->fetch()) {
if ($friend ['confirm']) {
if (isset($_GET ['friend']) && $_GET ['friend'] == 'delete') {
$form = new form("?id={$ank->id}&amp;friend&amp;" . passgen());
$form->bbcode(__('Действительно хотите удалить пользователя "%s" из друзей?', $ank->nick));
$form->button(__('Да, удалить'), 'delete');
$form->display();
}
if (!$ank->is_friend($user))
echo "<b>" . __('Пользователь еще не подтвердил факт Вашей дружбы') . "</b><br />";
} else {
$form = new form("?id={$ank->id}&amp;friend&amp;" . passgen());
$form->bbcode(__('Пользователь "%s" предлагает Вам дружбу', $ank->nick));
$form->button(__('Принимаю'), 'ok', false);
$form->button(__('Не принимаю'), 'no', false);
$form->display();
}
} else {
if (isset($_GET ['friend']) && $_GET ['friend'] == 'add') {
$form = new form("?id={$ank->id}&amp;friend&amp;" . passgen());
$form->bbcode(__('Предложить пользователю "%s" дружбу?', $ank->nick));
$form->button(__('Предложить'), 'add', false);
$form->display();
}
}
}
if ($ank->is_ban) {
$ban_listing = new listing();
$q = $db->prepare("SELECT * FROM `ban` WHERE `id_user` = ? AND `time_start` < ? AND (`time_end` is NULL OR `time_end` > ?) ORDER BY `id` DESC");
$q->execute(Array($ank->id, TIME, TIME));
if ($arr = $q->fetchAll()) {
foreach ($arr AS $c) {
$post = $ban_listing->post();
$adm = new user($c ['id_adm']);
$post->title = ($adm->group <= $user->group ? '<a href="/profile.view.php?id=' . $adm->id . '">' . $adm->nick . '</a>: ' : '') . text::toValue($c ['code']);
if ($c ['time_start'] && TIME < $c ['time_start']) {
$post->content[] = '[b]' . __('Начало действия') . ':[/b]' . misc::when($c ['time_start']) . "\n";
}
if ($c['time_end'] === NULL) {
$post->content[] = '[b]' . __('Пожизненная блокировка') . "[/b]\n";
} elseif (TIME < $c['time_end']) {
$post->content[] = __('Осталось: %s', misc::when($c['time_end'])) . "\n";
}
if ($c['link']) {
$post->content[] = __('Ссылка на нарушение: %s', $c['link']) . "\n";
}
$post->content[] = __('Комментарий: %s', $c['comment']) . "\n";
}
}
$ban_listing->display();
}
$rs = $db->prepare("SELECT * FROM `countries` WHERE `code` = ?");
$rs->execute(Array($ank->country));
$rs = $rs->fetch();
$country = ($user->country == $ank->country)? $rs['country_n'] : $rs['english_n']; 


$listing = new listing();
$post = $listing->post();
$post->title = __(' %s',$ank->group_name).'<div style="float:right; vertical-align: middle;"><img src="/sys/images/icons/starbig.png" width="16px"/> '.$ank->rating.' <a href="/profile.reviews.php?id='.$ank->id.'"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i></a></div>' ;
$post->icon($ank->icon());
$q = $db->prepare("SELECT `id_adm` FROM `log_of_user_status` WHERE `id_user` = ? ORDER BY `id` DESC LIMIT 1");
$q->execute(Array($ank->id));
if ($row = $q->fetch()) {
$adm = new user($row['id_adm']);
$post->title .= '<small>('.__('назначил' . ($adm->sex ? '' : 'а')) . ' "' . $adm->nick . '")</small>';
}
$listing->display();

$fon = new user_fon($ank->id);
$d = new design(); 
$d->assign('fon', $fon->image()); 
$d->assign('avatar', array($ank->getAvatar($dcms->browser_type == 'full' ? '680':'420'), __("Аватар")));
$d->assign('set_ava', $ank->set_ava);
if ($user->id == $ank->id)
{ 
$d->assign('add', array('/my.fon.php', __('Сменить обложку'))); 
$d->assign('ava', array('/my.avatar.php', __('Сменить аватар')));
$d->assign('editank', array('/profile.edit.php', __('Ред. анкету')));
}
if ($user->group>0 & ($ank->id != $user->id)){
$d->assign('sms', array('my.mail.php?id='.$ank->id.'', __('Сообщение'))); 
if (!$friend ['confirm']) {
$d->assign('frend', array('?id='.$ank->id.'&amp;friend=add', __('Добавить в друзья'))); 
} else {
$d->assign('frend', array('?id='.$ank->id.'&amp;friend=delete', __('Удалить из друзей'))); 
}
}
$d->assign('login', $ank->nick); 
if ($ank->online) {
$d->assign('on', array('on', __("Онлайн"))); 
} else {
$d->assign('on', array('off', __('Последний визит: (%s)', misc::when($ank->last_visit)))); 
}
if ($ank->ank_d_r && $ank->ank_m_r && $ank->ank_g_r)
$d->assign('dr', __('Возраст: %s', misc::get_age($ank->ank_g_r, $ank->ank_m_r, $ank->ank_d_r, true))); // Возраст
if($country) {$d->assign('gorod', __('%s / %s', $country, $ank->region_t)); }
$d->display('design.profile.tpl');
$listing = new listing();
if(!$ank->vk_id){
$post = $listing->post();
$name = ($ank->surname && $ank->patronymic) ? "$ank->surname $ank->realname $ank->patronymic": $ank->realname . ($ank->patronymic ? " " . $ank->patronymic:'') . ($ank->surname ? " " . $ank->surname:'');
$nn = ($ank->surname && $ank->patronymic) ? __('ФИО') : __('Имя');
$post->title= '<b><i class="fa fa-caret-right" aria-hidden="true"></i> '.$nn.' :</b> '.($name? $name : __('не заполнено'));
}
$post = $listing->post();
if ($ank->ank_d_r && $ank->ank_m_r && $ank->ank_g_r) {
$post->title = '<b><i class="fa fa-caret-right" aria-hidden="true"></i> '.__('Дата рождения').':</b> '.__('%s %s %s',$ank->ank_d_r, misc::getLocaleMonth($ank->ank_m_r),$ank->ank_g_r); 
} elseif ($ank->ank_d_r && $ank->ank_m_r) {
$post->title= '<b><i class="fa fa-caret-right" aria-hidden="true"></i> '.__('День рождения').':</b> '.__('%s %s', $ank->ank_d_r, misc::getLocaleMonth($ank->ank_m_r));
} else {
$post->title= '<b><i class="fa fa-caret-right" aria-hidden="true"></i> '.__('Дата рождения').':</b> '.__('не заполнено'); 
}
$post = $listing->post();
$post->title = '<b><i class="fa fa-caret-right" aria-hidden="true"></i> '.__('Языки').':</b> '.($ank->languages? __('%s', $ank->languages) : __('не заполнено'));
$post = $listing->post();
if ($ank->is_friend($user) || $ank->vis_icq) {
$post->title= '<b><i class="fa fa-caret-right" aria-hidden="true"></i> ICQ UIN:</b> '.($ank->icq_uin? $ank->icq_uin : __('не заполнено'));
} else {
$post->title= '<b><i class="fa fa-caret-right" aria-hidden="true"></i> ICQ UIN:</b> Информация скрыта';
}
$post = $listing->post();
if ($ank->is_friend($user) || $ank->vis_skype) {
$post->title= '<b><i class="fa fa-caret-right" aria-hidden="true"></i> Skype:</b> '.($ank->skype? $ank->skype: __('не заполнено'));
} else {
$post->title= '<b><i class="fa fa-caret-right" aria-hidden="true"></i> Skype:</b> Информация скрыта';
}
$post = $listing->post();
if ($ank->is_friend($user) || $ank->vis_email) {
$post->title= '<b><i class="fa fa-caret-right" aria-hidden="true"></i> E-mail:</b> '.($ank->email? $ank->email : __('не заполнено'));
} else {
$post->title= '<b><i class="fa fa-caret-right" aria-hidden="true"></i> E-mail:</b> Информация скрыта';
}

if ($user->group > 2) {
$post = $listing->post();
$post->title= '<b><i class="fa fa-caret-right" aria-hidden="true"></i> '.__('Регистрационный E-mail').':</b> '.($ank->reg_mail? $ank->reg_mail : __('не заполнено'));
}
$post = $listing->post();
$post->title= '<b><i class="fa fa-caret-right" aria-hidden="true"></i> '.__('О себе').':</b> '.($ank->description? text::for_name($ank->description) : __('не заполнено'));
if ($ank->is_friend($user) || $ank->vis_friends) {
$res = $db->prepare("SELECT COUNT(*) FROM `friends` WHERE `id_user` = ? AND `confirm` = '1'");
$res->execute(Array($ank->id));
$k_friends = $res->fetchColumn();
$post = $listing->post();
$post->title = __('Друзья');
$post->url = $ank->id == $user->id ? "/my.friends.php" : "/profile.friends.php?id={$ank->id}";
$post->counter = $k_friends;
$post->icon('user.1');
$q = $db->prepare("SELECT * FROM `friends` WHERE `id_user` = ? AND `confirm` = '1' ORDER BY `confirm` ASC, `time` DESC LIMIT 6;"); 
$q->execute(Array($ank->id));
while ($ank2 = $q->fetch()) {
$p_user = new user($ank2['id_friend']);
$post->post .= ' <a href="/profile.view.php?id=' . $p_user->id . '"><span class="friend"><div class="avatar_post" style="    margin: 0 auto; background: url('.$p_user->ava().');">
                  </div>' . $p_user->nick() . '</span></a> ';
}
} else {
$post = $listing->post();
$post->title = __('Друзья');
$post->icon('user.1');
$post->url = '/faq.php?info=hide&amp;return=' . URL;
$post->content = __('Информация скрыта');
}
$post = $listing->post();
$post->highlight = true;
$post->icon('info');
$post->title = __('Активность');
$post->content[] = '[b]'.__('Помощь проекту').':[/b] '.__('%s руб.',$ank->donate_rub);
$post->content[] = '[b]'.__('Дата регистрации').':[/b] '.__('%s',date("d-m-Y", $ank->reg_date));
$post->content[]= '[b]'.__('Последний визит').':[/b] '.__('%s',misc::when($ank->last_visit));
$post->content[] = '[b]'.__('Всего переходов').':[/b] '.__('%s',$ank->conversions);
$post->content[] = '[b]'.__('Проведено времени на сайте').':[/b] '.__('%s', misc::vremja_sec($ank->time2));
if ($user->id == $ank->id)$post->content[] = '[b]'.__('Баллов').':[/b] '.__(' %s ',$ank->balls).'';

$listing->display();
if ($user->group)
$doc->ret(__('Личное меню'), '/menu.user.php');