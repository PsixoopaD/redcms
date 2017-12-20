<?php


class avatar
{
 protected $_data = array(); // информация о файле
    protected $_screens = array(); // скриншоты (имена файлов)
 function __construct($path_dir_abs, $filename) {
     

        if ($cfg_ini = ini::read($path_dir_abs . '/.' . $filename . '.ini', true)) {
            // загружаем конфиг
            $this->_data = array_merge($this->_data, (array)@$cfg_ini['CONFIG']);
            $this->_screens = array_merge($this->_screens, (array)@$cfg_ini['SCREENS']);
        }
 }
 public function getScreen($img_max_width, $num =24)
    {
       
        if (!empty($this->_screens[$num])) {
            $screen_path_rel = '/sys/tmp/public.' . md5($this->path_file_rel) . '.time_add' . $this->time_add . '.num' . $num . '.width' . $img_max_width . '.jpg';

            if (file_exists(H . $screen_path_rel))
                return $screen_path_rel;
            if (!$img = @imagecreatefromjpeg($this->path_dir_abs . '/' . $this->_screens[$num]))
                return false;
            $img_screen = imaging::to_screen($img, $img_max_width);
            if (imagejpeg($img_screen, H . $screen_path_rel, 85))
                return $screen_path_rel;
        }
        return false;
    }
  
 function __get($n)
    {
        global $dcms;
        switch ($n) {
          
            default:
                return isset($this->_data[$n]) ? $this->_data[$n] : false;
        }
    }

    

}