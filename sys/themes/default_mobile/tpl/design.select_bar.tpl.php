<div class="select_bar invert border radius">
    <?php
    foreach($select AS $option){
        if (empty($option[2]))
            echo '<a class="border radius padding" href="'.$option[0].'">'.$option[1].'</a>';
        else
            echo '<span class="border radius padding">'.$option[1].'</span>';
    }
    ?>
</div>