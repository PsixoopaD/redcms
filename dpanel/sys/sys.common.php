<?php

include_once '../../sys/inc/start.php';
dpanel::check_access();
$doc = new document(2);
if(!$user->access('set_site_dpanel')) $doc->access_denied(__('У Вас нет доступа!'));
$doc->ret(__('Настр. сайта'), './');
$doc->ret(__('Админка'), '../');
$doc->title = __('Общие настройки');
$languages = languages::getList(); // список доступных языковых пакетов

$browser_types = array('light', 'mobile', 'full');

if (isset($_POST ['save'])) {
    $dcms->debug = (int) !empty($_POST ['debug']);
    $dcms->align_html = (int) !empty($_POST ['align_html']);
    $dcms->new_time_as_date = (int) !empty($_POST ['new_time_as_date']);
    $dcms->censure = (int) !empty($_POST ['censure']);
    $dcms->https_only = (int) !empty($_POST ['https_only']);
    $dcms->https_hsts = (int) !empty($_POST ['https_hsts']);

    foreach ($browser_types as $b_types) {
        $key = 'theme_' . $b_types;
        if (!empty($_POST [$key])) {
            $theme_set = (string) $_POST [$key];
            if (themes::exists($theme_set, $b_types))
                $dcms->$key = $theme_set;
        }
    }

    $lang = text::input_text($_POST ['language']);
    if (isset($languages[$lang]))
        $dcms->language = $lang;

    $dcms->title = text::for_name($_POST ['title']);
    $dcms->sitename = text::for_name($_POST ['sitename']);
    $dcms->copyrighte = text::input_text($_POST ['copyrighte']);
    $dcms->system_nick = text::for_name($_POST ['system_nick']);
   $dcms->log_of_visits = (int) !empty($_POST['log_of_visits']);
    $dcms->log_of_referers = (int) !empty($_POST['log_of_referers']);
    $dcms->clear_tmp_dir = (int) !empty($_POST['clear_tmp_dir']);
  $dcms->forum_theme_captcha = (int)!empty($_POST['forum_theme_captcha']);
    $dcms->forum_message_captcha = (int)!empty($_POST['forum_message_captcha']);
    $dcms->forum_search_captcha = (int)!empty($_POST['forum_search_captcha']);
    $dcms->forum_search_reg = (int)!empty($_POST['forum_search_reg']);
    $dcms->forum_files_upload_size = (int)($_POST['forum_files_upload_size'] * 1024);
    $dcms->forum_rating_down_balls = (int)$_POST['forum_rating_down_balls'];
    $dcms->forum_rating_coefficient = floatval($_POST['forum_rating_coefficient']);
    $dcms->save_settings($doc);
}


$form = new form('?' . passgen());
$form->text('title', __('Заголовок по-умолчанию'), $dcms->title);
$form->text('sitename', __('Название сайта'), $dcms->sitename);
$form->text('system_nick', __('Системный ник') . ' *', $dcms->system_nick);

foreach ($browser_types as $b_types) {
    $key = 'theme_' . $b_types;
    $options = array();
    $themes_list = themes::getThemesByType($b_types);
    foreach ($themes_list as $theme)
        $options [] = array($theme->getName(), $theme->getViewName(), $dcms->$key === $theme->getName());
    $form->select($key, __('Тема оформления') . ' (' . $b_types . ')', $options);
}

$options = array();
foreach ($languages as $key => $l) {
    $options [] = array($key, $l['name'], $dcms->language === $key);
}
$form->select('language', __('Язык по-умолчанию'), $options);

$form->checkbox('new_time_as_date', __('Новые файлы (темы и т.д.) за текущие сутки') . ' **', $dcms->new_time_as_date);
$form->checkbox('debug', __('Режим разработчика') . ' ***', $dcms->debug);
$form->checkbox('align_html', __('Выравнивание HTML кода'), $dcms->align_html);
$form->checkbox('censure', __('Антимат') . ' ****', $dcms->censure);
$form->checkbox('https_hsts', __('Использовать HSTS при заходе через https'), $dcms->https_hsts);
$form->checkbox('https_only', __('Принудительное использование %s', 'https'), $dcms->https_only);
$form->checkbox('log_of_visits', __('Журнал посещений'), $dcms->log_of_visits);
$form->checkbox('log_of_referers', __('Журнал рефереров'), $dcms->log_of_referers);
$form->checkbox('clear_tmp_dir', __('Чистка папки с временными файлами'), $dcms->clear_tmp_dir);
$form->text('copyrighte', __('Копирайт'), $dcms->copyrighte);
$form->checkbox('forum_theme_captcha', __('Форум - Создание тем через капчу') . ' *#', $dcms->forum_theme_captcha);
$form->checkbox('forum_message_captcha', __('Форум - Сообщения через капчу') . ' *#', $dcms->forum_message_captcha);
$form->text('forum_files_upload_size', __('Макс. размер прикрепляемого файла (KB)'), (int)($dcms->forum_files_upload_size / 1024));
$form->text('forum_rating_coefficient', __('Форум - Соотношение рейтинга сообщений с рейтингом пользователя'), floatval($dcms->forum_rating_coefficient));
$form->text('forum_rating_down_balls', __('Форум - Цена отрицательного рейтинга в баллах'), intval($dcms->forum_rating_down_balls));
$form->checkbox('forum_search_captcha', __('Форум - Поиск через капчу'), $dcms->forum_search_captcha);
$form->checkbox('forum_search_reg', __('Форум - Поиск только для зарегистрированных'), $dcms->forum_search_reg);
$form->bbcode('*# - ' . __('На администрацию данные ограничения не распространяются'));
$form->bbcode('* - ' . __('Будет заключен в квадратные скобки'));
$form->bbcode('** - ' . __('В противном случае за последние 24 часа'));
$form->bbcode('*** - [url=/faq.php?info=debug]' . __('Информация о режиме разработчика') . '[/url]');
$form->bbcode('**** - ' . __('Только для русского языка'));
$form->button(__('Применить'), 'save');
$form->display();
