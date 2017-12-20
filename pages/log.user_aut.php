<?php /* журнал авторизаций 2.5 for DCMS 7.5.1.151+ by S1S13AF7 */
include_once '../sys/inc/start.php'; /* подключаем ядро системы */
$ank = (empty($_GET ['id'])) ? $user : new user((int)$_GET ['id']);
$doc = new document($user->id === $ank->id ? 1:5); // своє+адмінам;
$doc->title = __('Журнал авторизаций') . ($user->id != $ank->id ? " $ank->login":'');
$res = $db->prepare("SELECT COUNT(*) FROM `log_of_user_aut` WHERE `id_user` = ?");
$res->execute(Array($ank->id));
if(!$ank->group)
    $doc->access_denied(__('Нет данных'));
if ($ank->group >= $user->group && $ank->id != $user->id)
    $doc->access_denied(__('Доступ к данной странице запрещен'));
static $browsers = array(); // массив с браузерами
$pages = new pages;
$pages->posts = $res->fetchColumn();
/* оригинальный запрос *
$q = $db->prepare("SELECT
        `log_of_user_aut`.`time` AS `time`,
        `log_of_user_aut`.`method` AS `method`,
        `log_of_user_aut`.`status` AS `status`,
        `log_of_user_aut`.`iplong` AS `iplong`,
        `browsers`.`name` AS `browser`
        FROM `log_of_user_aut`
LEFT JOIN `browsers` ON `browsers`.`id` = `log_of_user_aut`.`id_browser`
WHERE `log_of_user_aut`.`id_user` = ?
ORDER BY `time` DESC
LIMIT " . $pages->limit . ";");*/
$q = $db->prepare("SELECT * FROM `log_of_user_aut` WHERE `id_user` = ? ORDER BY `time` DESC LIMIT " . $pages->limit . ";");
$q->execute(Array($ank->id));

$listing = new listing();
while ($log = $q->fetch()) {
    $post = $listing->post();
    $post->counter = $log['count']; /* к-сть */
    $post->title = $log['method'] . ': ' . __($log['status'] ? 'Удачно' : 'Не удачно');
    $post->content[] = "[b]IP: ". long2ip($log['iplong']) . "[/b]";
    if ($log['browser']) $post->content[] = __('Браузер') . ": $log[browser]"; else {
    if(!isset($browsers [$log['id_browser']]) && (int) $log['id_browser'] > 0) {
        $b = $db->prepare("SELECT * FROM `browsers` WHERE `id` = ? LIMIT 1;");
        $b->execute(Array($log['id_browser']));
    if ($t = $b->fetch()) $browsers[$t['id']] = $t['name']; else
                          $browsers[$log['id_browser']] = false; }
    if ($browsers [$log['id_browser']])
        $post->content[] = __('Браузер') . ": " . $browsers[$log['id_browser']]; }
    $post->time = misc::when($log['time']);
    $post->highlight = !$log['status']; }
$listing->display(__('Журнал пуст'));

$pages->display('?'); // вывод страниц

$doc->ret(__('Личное меню'), '/menu.user.php');
/* Edited by «S1S13AF7»; upd: 11.05.2k15 */