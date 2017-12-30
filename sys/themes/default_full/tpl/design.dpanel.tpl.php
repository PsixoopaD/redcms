<?php
?>
<style>
.post.content .post_content {font-size: 11px !important; }
</style>
<div class="box_dpanel">
<?if($user->access('news_dopusk')) {?><a class="block_dpanel" href="./news/"><i class="fa fa-rss icon_bar" aria-hidden="true"></i>
<br/> Новости</a><?}?>
<?if($user->access('dpanel_sys_dopusk')) {?><a class="block_dpanel" href="./sys/"><i class="fa fa-cog icon_bar" aria-hidden="true"></i>
<br/> Настройки</a><?}?>
<?if($user->access('dpanel_db_dopusk')) {?><a class="block_dpanel" href="./db/"><i class="fa fa-database icon_bar" aria-hidden="true"></i>
<br/> База Д.</a><?}?>
<?if($user->access('dpanel_user_dopusk')){?><a class="block_dpanel" href="./user/"><i class="fa fa-users icon_bar" aria-hidden="true"></i>
<br/> Юзеры</a><?}?>
<?if($user->access('dpanel_info_dopusk')){?><a class="block_dpanel" href="./info/"><i class="fa fa-info-circle  icon_bar" aria-hidden="true"></i>
<br/> Информ.</a><?}?>
</div>
<div class="boxs_dpanel boxs_inline box_height">
<div class="box_title"><i class="fa fa-users" aria-hidden="true"></i> Администрация (<?=DB::me()->query("SELECT COUNT(*) FROM `users` WHERE `group` > '1' ORDER BY `id` ASC")->fetchColumn()?>)</div>
<div class="box_content">
<?
$q = DB::me()->query("SELECT `id` FROM `users` WHERE `group` > '1' ORDER BY `id` ASC");
$listing = new listing();
if ($arr = $q->fetchAll()) {
foreach ($arr AS $ank) {
$post = $listing->post();
$p_user = new user($ank['id']);
$post->image = $p_user->ava();
$post->title = $p_user->nick();
$post->url = '/profile.view.php?id=' . $p_user->id;
$name = ($p_user->surname && $p_user->patronymic) ? "$p_user->surname $p_user->realname $p_user->patronymic": $p_user->realname . ($p_user->patronymic ? " " . $p_user->patronymic:'') . ($p_user->surname ? " " . $p_user->surname:'');
$post->content = array();
if($name){
$nn = ($p_user->surname && $p_user->patronymic) ? __('ФИО') : __('Имя');
$post->content[] = '[b]'. __('%s',$nn).' :[/b] '.__('%s',$name);
}else {
$post->content[] = '[b]'.__('ФИО').':[/b] '.__('не заполнено');
}
$post->content[] = '[b]'.__('Должность').':[/b] '.__(' %s',$p_user->group_name);
$q = DB::me()->prepare("SELECT `id_adm` FROM `log_of_user_status` WHERE `id_user` = ? ORDER BY `id` DESC LIMIT 1");
$q->execute(Array($p_user->id));
if ($row = $q->fetch()) {
$adm = new user($row['id_adm']);
$post->content[] = ''.__('На должность назначил' . ($adm->sex ? '' : 'а')) . ' "' . $adm->nick . '"';
}
if ($p_user->group_us) {
$q = DB::me()->prepare("SELECT `id_adm` FROM `log_of_user_status` WHERE `id_user` = ? ORDER BY `id` DESC LIMIT 1");
$q->execute(Array($p_user->id));
if ($roww = $q->fetch()) {
$admm = new user($roww['id_adm']);
$post->content[] = '[b]'.__('Назначение').':[/b] '.$p_user->group_us.' [small]('.__('назначил' . ($admm->sex ? '' : 'а')) . ' "' . $admm->nick . '")[/small]';
}
}
$post->content[]= '[b]' . __('Последний визит') . ':[/b] ' . ($p_user->last_visit ? misc::when($p_user->last_visit) : misc::when($p_user->reg_date));
}
}
$listing->display();
?>
<br/><br/>
</div>
</div>
<div class="boxs_dpanel boxs_inline box_height">
<div class="box_title"><i class="fa fa-list-alt" aria-hidden="true"></i> Последние действия (20) <span class="box_act"><a href="./log.actions.php"><i class="fa fa-share-alt-square" aria-hidden="true"></i></a></span></div>
<div class="box_content">
<? $listing = new listing();
$res = DB::me()->query("SELECT * FROM `action_list_administrators` ORDER BY `time` DESC LIMIT 20");
if ($arr = $res ->fetchAll()) {
foreach ($arr AS $action) {
$ank = new user($action['id_user']);
$post = $listing->post();
$post->title = $ank->nick();
$post->image = $ank->ava();
$post->time = misc::when($action['time']);
$post->content = text::toOutput($action['description']);
}
}
$listing->display(__('Действия отсутствуют'));
?>
</div>
</div>
<div class="boxs_dpanel boxs">
<div class="box_title"><i class="fa fa-area-chart" aria-hidden="true"></i> Статистика посещения <span class="box_act"><a href="./statistics.php"><i class="fa fa-share-alt-square" aria-hidden="true"></i></a></span></div>
<?
$res = DB::me()->query("SELECT * FROM `log_of_visits_for_days` ORDER BY `time_day` DESC LIMIT 14");
$chart_hosts = new line_chart(__("Посетители за последние 2 недели"));
$chart_hosts->series[] = $s_hosts_full = new line_chart_series(__('С компьютера'));
$chart_hosts->series[] = $s_hosts_mobile = new line_chart_series(__('Со смартфона'));
$chart_hosts->series[] = $s_hosts_lite = new line_chart_series(__('С телефона'));
$chart_hosts->series[] = $s_hosts_robot = new line_chart_series(__('Поисковые роботы'));
$chart_hits = new line_chart(__("Переходы за последний 2 недели"));
$chart_hits->series[] = $s_hits_full = new line_chart_series(__('С компьютера'));
$chart_hits->series[] = $s_hits_mobile = new line_chart_series(__('Со смартфона'));
$chart_hits->series[] = $s_hits_lite = new line_chart_series(__('С телефона'));
$chart_hits->series[] = $s_hits_robot = new line_chart_series(__('Поисковые роботы'));
$all = $res->fetchAll();
$all = array_reverse($all);
foreach ($all as $data) {
$chart_hosts->categories[] = date('d', $data['time_day']);
$chart_hits->categories[] = date('d', $data['time_day']);
$s_hosts_full->data[] = (int)$data['hosts_full'];
$s_hosts_mobile->data[] = (int)$data['hosts_mobile'];
$s_hosts_lite->data[] = (int)$data['hosts_light'];
$s_hosts_robot->data[] = (int)$data['hosts_robot'];
$s_hits_full->data[] = (int)$data['hits_full'];
$s_hits_mobile->data[] = (int)$data['hits_mobile'];
$s_hits_lite->data[] = (int)$data['hits_light'];
$s_hits_robot->data[] = (int)$data['hits_robot'];
}
?><div class="box_statistic"><?
$chart_hosts->display();
?></div><?
?><div class="box_statistic"><?
$chart_hits->display();
?></div>
</div>
<?