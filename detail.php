<?php
    //Page name
    $pageTitle = 'MyJournal';

    //Required documents
    require 'inc/functions.php';

    //Variables to read DB
    $entry_id = $title = $date = $time = $learned = $resources = '';
    //If it is set
    if (isset($_GET['id'])) {
        //Get array values TO individual values from DB
        list($entry_id, $title, $date, $time, $learned, $resources) = get_specific_entry(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));
        $resources =  explode(',', $resources);
    }

    //Check if POST Method
    if($_SERVER['REQUEST_METHOD'] =='POST'){
        $entry_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        //If sql query it is correct through function
        if(delete_entry($entry_id)) {
            //Redirect to index.php
            header('Location: index.php?deleted=success');
            exit;
        }
        else{
            //Set message in case query is not correct
            $error_message = 'Could not add entry';
        }
        
    }

    include 'inc/header.php';

?>

    <div class="container row">
        <!-- Journal details -->
        <div class="entry-list single col-8">
            <article>
                <h1><?php echo $title; ?></h1>
                <time datetime="2016-01-31"><?php echo formated_date($date); ?></time>
                <div class="entry">
                    <h3>Time Spent: </h3>
                    <p><?php echo $time; ?></p>
                </div>
                <div class="entry">
                    <h3>What I Learned:</h3>
                    <p><?php echo $learned;?> 
                </div>
                <div class="entry">
                    <h3>Resources to Remember:</h3>
                    <ul>
                        <?php 
                            foreach ($resources as $item) {
                                echo '<li>' . $item . '</li>';
                            }
                        ?>
                    </ul>
                </div>
            </article>
        </div>
        <!-- Tags used in this journal-->
        <div class="entry-list single col-2">
            <article>
                <div class="entry">
                    <h3>Tags:</h3>
                    <ul class="list-group">
                        <div style="margin-top:20px;">
                            <?php 
                                foreach (get_tags_entry($entry_id) as $item) {
                                    echo '<li class="list-group-item">' .
                                        '<a href="tags.php?id=' . $item['id'] .'">' . $item['tag'] . 
                                        '</a></li>';
                                }
                            ?>
                        </div>
                    </ul>
                </div>
            </article>
        </div>
    </div>
    <div class="edit">
        <style>	.inner{ 
            display: inline-block;
            margin-left: 40;
            margin-right: 40;
        }
        </style>
        <p class="inner"><?php echo'<a href="edit.php?id=' . $entry_id . '">';?>Edit Entry</a></p>
        <form class="inner" method="post" action="detail.php" onsubmit="return confirm('Are you sure you want to delete this task?')">
            <?php
                echo "<input type='hidden' name='id' value='".$entry_id."' />";
            ?>
            <input type="submit" value="Delete Entry" class="btn btn-outline-danger" >
        </form>
    </div>

<?php
     //Include footer site
    include 'inc/footer.php';
?>
    