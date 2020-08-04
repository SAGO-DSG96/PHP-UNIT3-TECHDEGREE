<?php
    //Page name
    $pageTitle = 'MyJournal - New entry';

    //Required documents
    require 'inc/functions.php';

    //Variables to fill data in DB
    $title = $date = $time = $learned = $resources ='';
    $selected_tags = array();

    //Check if POST Method
    if($_SERVER['REQUEST_METHOD'] =='POST'){

        //Different form in PHP: https://stackoverflow.com/questions/34381649/2-forms-on-same-page-php
        
            switch ($_POST['form']) {
                case "new":
                        //Sanitize variables and trim whitespaces
                        $title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING));
                        $date = trim(filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING));
                        $time = trim(filter_input(INPUT_POST, 'timeSpent', FILTER_SANITIZE_NUMBER_INT));
                        $learned = trim(filter_input(INPUT_POST, 'whatILearned', FILTER_SANITIZE_STRING));
                        $resources = trim(filter_input(INPUT_POST, 'ResourcesToRemember', FILTER_SANITIZE_STRING));
                        
                    //Read if have already one item checked https://stackoverflow.com/questions/4997252/get-post-from-multiple-checkboxes
                        if(!empty($_POST['check_list'])) {
                            //Read selected tags
                            foreach($_POST['tag_list'] as $check) {
                            array_push($selected_tags, $check);
                            }
                        }
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
                        }else if(empty($_POST['tag_list'])){
                            $error_message = "Please select a tag";
                        }else{
                            //If sql query it is correct through function
                            if(add_new_entry($title, $date, $time, $learned, $resources)) {
                                $entry_id = get_new_entry($title);
                                //Add tags to database
                                foreach($_POST['tag_list'] as $check) {
                                    associate_tags($entry_id['id'], $check);
                                }
                                //Redirect to index.php
                                header('Location: index.php');
                                exit;
                            }
                            else{
                                //Set message in case query is not correct
                                $error_message = 'Could not add entry';
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
        <div class="new-entry col-8">
            <?php if (isset($error_message)) {
                echo '<div class="alert alert-danger" role="alert">'. $error_message . '</div>';
            }?>
            <h2>New Entry</h2>
            <form method="post" action="new.php">
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
                <input type="hidden" name="form" value="new">
                <input type="submit" value="Publish Entry" class="button">
                <a href="index.php" class="button button-secondary">Cancel</a>
            </form>
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
