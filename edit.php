<?php
    $pageTitle = 'MyJournal - Edit entry';

    //Required documents
    require 'inc/functions.php';

    //Variables to read DB
    $entry_id = $title = $date = $time = $learned = $resources = $tag ='' ;
    $selected_tags = array();
    //If it is set ID
    if (isset($_GET['id'])) {
        //Get array values TO individual values from DB
        list($entry_id, $title, $date, $time, $learned, $resources)  = get_specific_entry(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));
        
        //Read selected tags
        foreach(get_tags_entry($entry_id) as $item) {
            array_push($selected_tags, $item['id']);
        }
    }

    //Check if POST Method
    if($_SERVER['REQUEST_METHOD'] =='POST'){

        //Different form in PHP: https://stackoverflow.com/questions/34381649/2-forms-on-same-page-php
        
        switch ($_POST['form']) {
                case "update":
                            //Sanitize variables and trim whitespaces
                            $entry_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
                            $title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING));
                            $date = trim(filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING));
                            $time = trim(filter_input(INPUT_POST, 'timeSpent', FILTER_SANITIZE_NUMBER_INT));
                            $learned = trim(filter_input(INPUT_POST, 'whatILearned', FILTER_SANITIZE_STRING));
                            $resources = trim(filter_input(INPUT_POST, 'ResourcesToRemember', FILTER_SANITIZE_STRING));

                            //Separate String / to get correct date
                            $date_match = explode('-', $date);
                            
                            //If is something missing, put data to error_message
                            if(empty($title) || empty($date) || empty($time) || empty($learned) || empty($resources) ){
                                $error_message = 'Please fill required fields [Title, Date, Time, Learned, Resources]';
                            }else if(count($date_match) != 3 
                                    || strlen($date_match[0]) != 4
                                    || strlen($date_match[1]) != 2
                                    || strlen($date_match[2]) != 2
                                    || !checkdate($date_match[1], $date_match[2], $date_match[0])){  
                                $error_message = "Invalid Date";
                            }
                            else if(empty($_POST['tag_list'])){
                                $error_message = "Please select a tag";
                            }else{
                                //If sql query it is correct through function
                                if(update_specific_entry($entry_id, $title, $date, $time, $learned, $resources)) {
                                    //Add tags to database
                                    foreach($_POST['tag_list'] as $check) {
                                        associate_tags($entry_id, $check);
                                    }
                                    //Redirect to index.php
                                    header("Location: detail.php?id=".$entry_id);
                                    exit;
                                }
                                else{
                                    //Set message in case query is not correct
                                    $error_message = 'Could not edit entry';
                                }
                            }
        
                    break;
        
                case "newtag":
                    $entry_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
                    $tag = filter_input(INPUT_POST, 'new-tag', FILTER_SANITIZE_STRING);
                    if (empty($tag)) {
                        $error_message = "Please add a tag";
                    }
                    else{
                        if(create_new_tag($tag)){
                            header("Location: edit.php?id=".$entry_id);
                            exit;
                        }
                        else{
                            $error_message = "Tag already exist";
                        }
                    }

                    break;
                default:
                    $error_message = 'Please go to home page.';
                }
    }
        
    include 'inc/header.php';


?>

<div class="container row">
    <!-- Journal details -->
    <div class="col-9">  
        <?php 
        //Print at top error message
        if (isset($error_message)) {
            echo '<div class="alert alert-danger" role="alert">'. $error_message . '</div>';
        }?>
        <div class="edit-entry">
            <h2>Edit Entry</h2>
                <!-- Form to update entrie -->
                <form method="post" action="edit.php">
                        <label for="title"> Title</label>
                        <input id="title" type="text" name="title" value="<?php echo htmlspecialchars($title)?>"><br>

                        <label for="date">Date</label>
                        <input id="date" type="date" name="date" value="<?php echo htmlspecialchars($date)?>"><br>

                        <label for="time-spent"> Time Spent</label>
                        <input id="time-spent" type="text" name="timeSpent" value="<?php echo htmlspecialchars($time)?>"><br>

                        <label for="what-i-learned">What I Learned</label>
                        <textarea id="what-i-learned" rows="5" name="whatILearned"><?php echo htmlspecialchars($learned)?></textarea>

                        <label for="resources-to-remember">Resources to Remember (separate resources with commas)</label>
                        <textarea id="resources-to-remember" rows="5" name="ResourcesToRemember"><?php echo htmlspecialchars($resources)?></textarea>

                        <label for="tags">Select Existing Tags</label>
                        <!--cycle to show all tags -->
                        <?php
                            foreach (get_all_tags() as $item) {
                                echo    '<div class="custom-control custom-radio">';
                                //If to know if already have in DB this tag
                                if (in_array($item['id'], $selected_tags)) {
                                    echo '<input type="checkbox" class="custom-control-input" name="tag_list[]" id="tag' . $item['id'] . '" value="' . $item['id'] .'" checked>';
                                }
                                else{
                                    echo '<input type="checkbox" class="custom-control-input" name="tag_list[]" id="tag' . $item['id'] . '" value="' . $item['id'] .'">';
                                }
                                echo '<label class="custom-control-label" for="tag' . $item['id'] . '">' . $item['tag'] . '</label>'
                                        . '</div>';
                                echo '<br>';
                            }                            
                        ?>
        
                        <?php
                            //This code will pass the the id to the $_POST to make sure that the update values belong to journal_id
                            if(!empty($entry_id)){
                                echo "<input type='hidden' name='id' value='".$entry_id."' />";
                            }
                        ?>
                        <input type="hidden" name="form" value="update">
                        <input type="submit" value="Edit Entry" class="button">
                    <?php echo '<a href="detail.php?id=' . $entry_id .'"' . 'class="button button-secondary">' ?> Cancel</a>
                </form>
        </div>
    </div>
     <!-- Tags used in this journal-->
    <div class="col-3"> 
        <div class="entry-list single">
            <h2>Add tags</h2>

            <form method="post" action="edit.php">
                 <label for="new-tag">New Tag</label>
                 <input id="new-tag" type="text" name="new-tag" value=""><br>
                 <?php
                    //This code will pass the the id to the $_POST to make sure we keep updating same entry
                    if(!empty($entry_id)){
                        echo "<input type='hidden' name='id' value='" . $entry_id . "' />";
                    }
                ?>
                <input type="hidden" name="form" value="newtag">
                <input type="submit" value="Add Tag" class="button">
            </form>
        </div>
    </div>
</div>


<?php
    //Include footer site
    include 'inc/footer.php';
?>