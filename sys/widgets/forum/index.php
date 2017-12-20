<?php
defined('DCMS') or die;
global $user;
$db = DB::me();
if (false === ($new_posts = cache_counters::get('forum.new_posts.' . $user->group))) {
    $res = $db->prepare("SELECT COUNT(*)
FROM `forum_themes` AS `th`
INNER JOIN `forum_topics` AS `tp` ON `tp`.`id` = `th`.`id_topic` AND `tp`.`theme_view` = :v
LEFT JOIN `forum_categories` AS `cat` ON `cat`.`id` = `th`.`id_category`
WHERE `th`.`group_show` <= :g
AND `tp`.`group_show` <= :g
AND `cat`.`group_show` <= :g
AND `th`.`time_last` > :t");
    $res->execute(Array(':v' => 1, ':g' => $user->group, ':t' => NEW_TIME));
    $new_posts = $res->fetchColumn();
    cache_counters::set('forum.new_posts.' . $user->group, $new_posts, 60);
}
if (false === ($new_themes = cache_counters::get('forum.new_themes.' . $user->group))) {
    $res = $db->prepare("SELECT COUNT(*)
FROM `forum_themes` AS `th`
INNER JOIN `forum_topics` AS `tp` ON `tp`.`id` = `th`.`id_topic` AND `tp`.`theme_view` = :v
LEFT JOIN `forum_categories` AS `cat` ON `cat`.`id` = `th`.`id_category`
WHERE `th`.`group_show` <= :g
AND `tp`.`group_show` <= :g
AND `cat`.`group_show` <= :g
AND `th`.`time_create` > :t");
    $res->execute(Array(':v' => 1, ':g' => $user->group, ':t' => NEW_TIME));
    $new_themes = $res->fetchColumn();
    cache_counters::set('forum.new_themes.' . $user->group, $new_themes, 60);
}
$users = $db->query("SELECT COUNT(*) FROM `users_online` WHERE `request` LIKE '/forum/%'")->fetchColumn();
$listing = new listing('title_box');
$post = $listing->post('title_box');
$post->title = __('Форум');
$post->url = '/forum/';
$post->fa_icon = 'comment';
$post->counter = __('%s ' . misc::number($users, 'человек', 'человека', 'человек'), $users);
$listing->display();
function forum_getMessagesCounters($themes_ids = array(), $time_from = 0, $group = 0)
{
    $counters = array();
    $sql_prep = db::me()->prepare('
SELECT COUNT(*) AS `count`, `id_theme` FROM forum_messages WHERE group_show < :g AND id_theme IN (' .
        join(',', array_map('intval', $themes_ids)) . ') AND `time` > :t GROUP BY id_theme');
    $sql_prep->execute(array(':t' => $time_from, ':g' => $group));
    while ($res = $sql_prep->fetch()) {
        $counters[$res['id_theme']] = $res['count'];
    }
    return $counters;
}
/**
 * Возвращает массив с количеством просмотров каждой теме
 * @param array $themes_ids массив идентификаторов тем
 * @return array
 * @throws Exception
 * @throws ExceptionPdoNotExists
 */
function forum_getViewsCounters($themes_ids = array())
{
    $counters = array();
    $sql_prep = db::me()->prepare('
SELECT COUNT(*) AS `count`, `id_theme` FROM forum_views WHERE id_theme IN (' .
        join(',', array_map('intval', $themes_ids)) . ') GROUP BY id_theme');
    $sql_prep->execute();
    while ($res = $sql_prep->fetch()) {
        $counters[$res['id_theme']] = $res['count'];
    }
    foreach ($themes_ids as $id) {
        if (!array_key_exists($id, $counters)) {
            $counters[$id] = 0;
        }
    }
    return $counters;
}
/**
 * Возвращает массив последних просмотров тем для указанного пользователя
 * @param array $themes_ids
 * @param int $id_user идентификатор пользователя
 * @return array
 * @throws Exception
 * @throws ExceptionPdoNotExists
 */
function forum_getLastViewsTimes($themes_ids = array(), $id_user = 0)
{
    $counters = array();
    $sql_prep = db::me()->prepare('
SELECT MAX(`time`) AS `last_view_time`, `id_theme` FROM forum_views WHERE id_theme IN (' .
        join(',', array_map('intval', $themes_ids)) . ') AND `id_user` = :uid GROUP BY id_theme');
    $sql_prep->execute(array(':uid' => $id_user));
    while ($res = $sql_prep->fetch()) {
        $counters[$res['id_theme']] = $res['last_view_time'];
    }
    return $counters;
}
$db = DB::me();
$today = mktime(0, 0, 0);
$yesterday = $today - 3600 * 24;
$cache_id = 'forum.last.posts_all';
if (false === ($themes_all = cache::get($cache_id))) {
    $themes_all = array();
    $q = $db->prepare("SELECT `th`.* ,
        `tp`.`name` AS `topic_name`,
        `cat`.`name` AS `category_name`,
        `tp`.`group_write` AS `topic_group_write`,
            GREATEST(`th`.`group_show`, `tp`.`group_show`, `cat`.`group_show`) AS `group_show`
FROM `forum_themes` AS `th`
INNER JOIN `forum_topics` AS `tp` ON `tp`.`id` = `th`.`id_topic` AND `tp`.`theme_view` = :v
JOIN `forum_categories` AS `cat` ON `cat`.`id` = `th`.`id_category`
ORDER BY `th`.`time_last` DESC");
    $q->execute(Array(':v' => 1));
    $themes_all = $q->fetchAll();
}
$count = count($themes_all);
$themes_for_view = array();
for ($i = 0; $i < $count; $i++) {
    if ($themes_all[$i]['group_show'] > current_user::getInstance()->group) {
        continue;
    }
    $themes_for_view[] = $themes_all[$i];
}
$themes_ids = array();
$last_views = array(); // массив вида [id темы] => [дата последнего просмотра текущим пользователем]
$views_counters = array(); // счетчики просмотров тем
$new_messages = array(); // кол-во новых сообщений в теме за сегодня
$all_messages = array(); // общее кол-во сообщений в темах
$users_for_preload = array();
$count_themes = count($themes_for_view);
if ($count_themes) {
    for ($i = 0; $i < $count_themes; $i++) {
        $themes_ids[] = $themes_for_view[$i]['id'];
        $users_for_preload[] = $themes_for_view[$i]['id_autor'];
        $users_for_preload[] = $themes_for_view[$i]['id_last'];
    }
    $last_views = forum_getLastViewsTimes($themes_ids, current_user::getInstance()->id);
    $new_messages = forum_getMessagesCounters($themes_ids, NEW_TIME, current_user::getInstance()->group);
    $all_messages = forum_getMessagesCounters($themes_ids, 0, current_user::getInstance()->group);
    $views_counters = forum_getViewsCounters($themes_ids);
    new user($users_for_preload); // предзагрузка всех возможных пользователей одним SQL запросом
}
$listing = new listing();
for ($z = 0; $z < 3 && $z < $count_themes; $z++) {
    $theme = $themes_for_view[$z];
    $post = $listing->post();
    if (current_user::getInstance()->id) {
        if (array_key_exists($theme['id'], $last_views)) {
            $post->highlight = $theme['time_last'] > $last_views[$theme['id']];
        } else {
            $post->highlight = true;
        }
    }
    $is_open = (int)($theme['group_write'] <= $theme['topic_group_write']);
$autor = new user($theme['id_autor']);
    $post->icon("forum.theme.{$theme['top']}.$is_open.png");
    $post->time = misc::when($theme['time_last']);
    $post->title = text::toValue($theme['name']);
    $post->counter = (isset($new_messages[$theme['id']]) ? '+' . $new_messages[$theme['id']] : '');
    $post->url = '/forum/theme.php?id=' . $theme['id'] . '&amp;page=end';
    
    $last_msg = new user($theme['id_last']);
    $post->content = ''.($autor->id != $last_msg->id ? 'Автор: '.$autor->nick . ' / Посл. ответ от: ' . $last_msg->nick : 'Автор: '.$autor->nick) . '<br />';
    $post->content .= "Раздел: <a href='/forum/category.php?id=$theme[id_category]'>" . text::toValue($theme['category_name']) . "</a> - <a href='/forum/topic.php?id=$theme[id_topic]'>" . text::toValue($theme['topic_name']) . "</a><br />";
    $post->bottom = __('Просмотров: %s', $views_counters[$theme['id']]).' | Комментариев: '.$all_messages[$theme['id']];
}
$listing->display(__('Пока еще никто не создал тему на форуме'));