<div class="pages">
    <?php
    echo $page == 1 ? '<span class="invert border radius">1</span>' : '<a class="border radius" href="' . $link . 'page=1">1</a>';
    for ($i = max(2, $page - 4); $i < min($k_page, $page + 3); $i++) {
        if ($i == $page)
            echo '<span class="invert border radius">' . $i . '</span>';
        else
            echo '<a class="border radius" href="' . $link . 'page=' . $i . '">' . $i . '</a>';
    }
    echo $page == $k_page ? '<span class="invert border radius">' . $k_page . '</span>' : '<a class="border radius" href="' . $link . 'page=' . $k_page . '">' . $k_page . '</a>'
    ?>
</div>
