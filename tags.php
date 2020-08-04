<?php
    //Page name
    $pageTitle = 'MyJournal-Tags';

    //Required documents
    require 'inc/functions.php';

    //Variables to read DB
    $tag_id = '';
    //If it is set
    if (isset($_GET['id'])) {
        //Get array values TO individual values from DB
        $tag_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    }

    include 'inc/header.php';

?>
    <div class="container row">
        <!-- Journal details -->
        <div class="entry-list single col-8">
            <article>
                <?php
                    foreach ($a = get_entries_tag($tag_id) as $item) {
                        // Repeat same patron for each item in DB
                        echo '<article><h2>' .
                        '<a href="detail.php?id=' . $item['id'] .'">' . $item['title'] . 
                        '</a></h2>' .
                        '<time datetime="' . $item['date'] . '">'. formated_date($item['date']) . '</time>' . 
                        '</article>';
                    }
                ?>
            </article>
        </div>
        <!-- Tags used in this journal-->
        <div class="entry-list single col-2">
            <article>
                <div class="entry">
                    <h3>Search by Tags:</h3>
                    <ul class="list-group">
                        <?php 
                            foreach (get_all_tags() as $item) {
                                echo '<li class="list-group-item">'
                                     . '<a href="tags.php?id=' . $item['id'] .'">' . $item['tag'] .  '</a>'
                                     . '</li>';
                            }
                        ?>
                    </ul>
                </div>
            </article>
        </div>
    </div>

<?php
     //Include footer site
    include 'inc/footer.php';
?>
    