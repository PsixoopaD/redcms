<?php
include_once '../sys/inc/start.php';
$groups = groups::load_ini(); // загружаем массив групп
$doc = new document(2);
if(!$user->access('forum_edit_kat'))$doc->access_denied(__('У Вас нет доступа!'));
$doc->title = __('Редактирование категории');
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Refresh: 1; url=./');
    $doc->err(__('Ошибка выбора категории'));
    exit;
}
$id_category = (int) $_GET['id'];
$q = $db->prepare("SELECT * FROM `forum_categories` WHERE `id` = ?");
$q->execute(Array($id_category));
if (!$category = $q->fetch()) {
    header('Refresh: 1; url=./');
    $doc->err(__('Категория не существует'));
    exit;
}
if (isset($_POST['save'])) {
    if (isset($_POST['name']) && isset($_POST['description'])) {
        $name = text::for_name($_POST['name']);
        $description = text::input_text($_POST['description']);
        if ($name && $name != $category['name']) {
            $dcms->log('Форум', 'Изменение названия категории "' . $category['name'] . '" на [url=/forum/category.php?id=' . $category['id'] . ']"' . $name . '"[/url]');
            $category['name'] = $name;
            $res = $db->prepare("UPDATE `forum_categories` SET `name` = ? WHERE `id` = ? LIMIT 1");
            $res->execute(Array($category['name'], $category['id']));
            $doc->msg(__('Название категории успешно изменено'));
        }
        if ($description != $category['description']) {
            $category['description'] = $description;
            $res = $db->prepare("UPDATE `forum_categories` SET `description` = ? WHERE `id` = ? LIMIT 1");
            $res->execute(Array($category['description'], $category['id']));
            $doc->msg(__('Описание категории успешно изменено'));
            $dcms->log('Форум', 'Изменение описания категории [url=/forum/category.php?id=' . $category['id'] . ']"' . $category['name'] . '"[/url]');
        }
    }
    if (isset($_POST['position'])) { // позиция
        $position = (int) $_POST['position'];
        if ($position != $category['position']) {
            $dcms->log('Форум', 'Изменение позиции категории [url=/forum/category.php?id=' . $category['id'] . ']"' . $category['name'] . '"[/url] с ' . $category['position'] . ' на ' . $position);
            $category['position'] = $position;
            $res = $db->prepare("UPDATE `forum_categories` SET `position` = ? WHERE `id` = ? LIMIT 1");
            $res->execute(Array($category['position'], $category['id']));
            $doc->msg(__('Позиция категории успешно изменена'));
            $dcms->log('Форум', 'Изменение позиции категории [url=/forum/category.php?id=' . $category['id'] . ']"' . $category['name'] . '"[/url] на ' . $position);
        }
    }
}
$doc->title = __('Редактирование категории "%s"', $category['name']); // шапка страницы
$form = new form(new url());
$form->text('name', __('Название'), $category['name']);
$form->textarea('description', __('Описание'), $category['description']);
$form->text('position', __('Позиция'), $category['position']);
$form->button(__('Применить'), 'save');
$form->display();
if($user->access('forum_del_kat'))$doc->act(__('Удалить категорию'), 'category.delete.php?id=' . $category['id']);
if (isset($_GET['return']))
    $doc->ret(__('В категорию'), text::toValue($_GET['return']));
else
    $doc->ret(__('В категорию'), 'category.php?id=' . $category['id']);
$doc->ret(__('Форум'), './');