<?php
$classes = array('post', 'clearfix');
if ($highlight) {
    $classes[] = 'highlight';
}
if ($image) {
    $classes[] = 'image';
}
if ($icon || $icon_class) {
    $classes[] = 'icon';
}
if ($time) {
    $classes[] = 'time';
}
if ($actions) {
    $classes[] = 'actions';
}
if ($counter) {
    $classes[] = 'counter';
}
if ($bottom) {
    $classes[] = 'bottom';
}
if ($content) {
    $classes[] = 'content';
}
?>
<div id="<?= $id ?>"
     class="<?= implode(' ', $classes) ?>"
     data-ng-controller="ListingPostCtrl"
     data-post-url="<?= $url ?>">

    <div class="post_image avatar_post" style="background: url('<?= $image ?>');"></div>
    <div class="post_head">
        <span class="post_icon">
            <?php if ($icon_class) { ?>
                <span class="<?= $icon_class ?>"></span>
            <?php } else { ?>
                <img src="<?= $icon ?>" alt="">
            <?php } ?>
        </span>
        <a class="post_title" <?php if ($url) { ?>href="<?= $url ?>"<?php } ?>><?= $title ?></a>

        <span class="post_counter"><?= $counter ?></span>
        <span class="post_time"><?= $time ?></span>
    </div>
    <div class="post_content"><?= $content ?></div>
    <div class="post_bottom"><?= $bottom ?>         <span class="post_actions"><?= $this->section($actions,'<a href="{url}"><img class="icon_act" src="{icon}" alt="" /></a>') ?></span></div>
</div>