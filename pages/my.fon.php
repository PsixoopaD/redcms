<?php

include_once '../sys/inc/start.php';
$doc = new document(1); // инициализация документа для браузера
$doc->title = __('Мой фон профиля');
$doc->ret(__('Анкета'), '/profile.view.php');
$fon = new user_fon($user->id);
$avatar_file_name = $user->id . '.jpg';
$avatars_path = FILES . '/.fon'; // папка с фоном
$avatars_dir = new files($avatars_path);



if (!empty($_FILES ['file'])) {
    if ($_FILES ['file'] ['error']) {
        $doc->err(__('Ошибка при загрузке'));
    } elseif (!$_FILES ['file'] ['size']) {
        $doc->err(__('Содержимое файла пусто'));
    } elseif (!preg_match('#\.jpe?g$#ui', $_FILES ['file'] ['name'])) {
        $doc->err(__('Неверное расширение файла'));
    } elseif (!$img = @imagecreatefromjpeg($_FILES ['file'] ['tmp_name'])) {
        $doc->err(__('Файл не является изображением JPEG'));
    } elseif (@imagesx($img) < 400) {
        $doc->err(__('Ширина изображения должна быть не менее 128 px'));
    } elseif (@imagesy($img) < 250) {
        $doc->err(__('Высота изображения должна быть не менее 128 px'));
    } else {
        if ($avatars_dir->is_file($avatar_file_name)) {
            $avatar = new files_file($avatars_path, $avatar_file_name);
            $avatar->delete(); // удаляем старый фон
        }

        if ($files_ok = $avatars_dir->filesAdd(array($_FILES ['file'] ['tmp_name'] => $avatar_file_name))) {
            $avatars_dir->group_show = 0;
            $files_ok [$_FILES ['file'] ['tmp_name']]->group_show = 0;
            $files_ok [$_FILES ['file'] ['tmp_name']]->id_user = $user->id;
            $files_ok [$_FILES ['file'] ['tmp_name']]->group_edit = max($user->group, 2);

            unset($files_ok);
            $doc->msg(__('Фон успешно установлен'));
        } else {
            $doc->err(__('Не удалось сохранить выгруженный файл'));
        }
    }
}

// Аватар 
if ($path = $fon->getScreen($doc->img_max_width())) {

    if (!empty($_POST ['delete'])) {
        $avatar = new files_file($avatars_path, $avatar_file_name);
        if (empty($_POST ['captcha']) || empty($_POST ['captcha_session']) || !captcha::check($_POST ['captcha'], $_POST ['captcha_session']))
            $doc->err(__('Проверочное число введено неверно'));
        elseif ($avatar->delete()) {
            $doc->msg(__('Фон успешно удален'));

            $doc->ret(__('Мой фон профиля'), '?' . passgen());
            header('Refresh: 1; url=?' . passgen());
            exit;
        } else {

            $doc->err(__('Не удалось удалить фон'));
        }
    }

    echo "<img class='DCMS_photo' src='" . $path . "' alt='".__('Мой фон профиля')."' style='max-width: 300px;'/><br />\n";

    $form = new form('?' . passgen());
    $form->captcha();
    $form->button(__('Удалить'), 'delete');
    $form->display();
}

$form = new form('?' . passgen());
$form->file('file', __('Файл фона').' (*.jpg)');
$form->button(__('Выгрузить'));
$form->display();