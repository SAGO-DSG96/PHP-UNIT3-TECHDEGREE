<?php
    //Page name
    $pageTitle = 'MyJournal';
    //Required documents
    require 'inc/functions.php';

    if(isset($_GET['deleted'])) {
        $msg_param = trim(filter_input(INPUT_GET, 'success', FILTER_SANITIZE_STRING));
        $success_msg = "Item was successfully $msg_param deleted!";
    }

    include 'inc/header.php';

?>

    <div class="container row">
        <div class="entry-list col-8">
            <?php
                if(isset($success_msg)) {
                    echo '<div class="alert alert-warning mx-auto" style="width: 500px; text-align: center; " role="alert">' 
                        . $success_msg . '</div>';
                }
                // Print all content in DB generate by query in get_entries_list()
                foreach (get_entries_list() as $item) {
                    // Repeat same patron for each item in DB
                        echo '<article><h2>' .
                            '<a href="detail.php?id=' . $item['id'] .'">' . $item['title'] . 
                            '</a></h2>' .
                            '<time datetime="' . $item['date'] . '">'. formated_date($item['date']) . '</time>' . 
                            '</article>';
                }
            ?>
        </div>
        <div class="col-2">
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
