<?php
class install_db_connect {
    private $is_connected = false;
    private $err_connect = false;
    private $err_db = false;
    private $settings = [];

    function __construct()
    {
        $this->settings = &$_SESSION['settings'];

        $this->settings['mysql_host'] = $_POST['mysql_host'] ?? null;
        $this->settings['mysql_user'] = $_POST['mysql_user'] ?? null;
        $this->settings['mysql_pass'] = $_POST['mysql_pass'] ?? null;
        $this->settings['mysql_base'] = $_POST['mysql_base'] ?? null;

        $dsn = 'mysql:host=' . $this->settings['mysql_host'] . ';dbname=' . $this->settings['mysql_base'] . ';charset=utf8';
        $opt = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
          new \PDO($dsn, $this->settings['mysql_user'], $this->settings['mysql_pass'], $opt);
          $this->is_connected = true;
        } catch (Exception $e) {
            $this->err_db = true;
        }
    }

    function actions(): bool
    {
        return $this->is_connected;
    }

    function form(): bool
    {
        echo "<div  style='color: white; padding: 5px; background-color:" . ($this->err_connect?'#FF9498':'#008AD5') . "'>";
        echo __('Сервер MySQL').":<br /><input type='text' name='mysql_host' value='" . text::toValue($this->settings['mysql_host']) . "' /><br />";
        echo __('Пользователь').":<br /><input type='text' name='mysql_user' value='" . text::toValue($this->settings['mysql_user']) . "' /><br />";
        echo __('Пароль').":<br /><input type='text' name='mysql_pass' value='" . text::toValue($this->settings['mysql_pass']) . "' />";
        echo "</div>";
        echo "<div style='padding: 5px; color: white; background-color:" . ($this->err_db?'#FF9498':'#008AD5') . "'>";
        echo __('База данных').":<br /><input type='text' name='mysql_base' value='" . text::toValue($this->settings['mysql_base']) . "' />";
        echo "</div>";
        return $this->is_connected;
    }
}