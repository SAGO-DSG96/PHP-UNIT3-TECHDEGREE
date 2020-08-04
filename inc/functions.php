<?php

    //Function read DB project List
    function get_entries_list(){
        include('connection.php');
        $sql = 'SELECT id, title, date FROM entries ORDER BY date DESC';

        try {
            //Query
            $results = $db -> query($sql);
        } catch (Exception $e) {
            echo "Error: " . $e -> getMessage() . "</br>";
            //Return null foreach loop in project_list.php
            return array();
        }
        return $results -> fetchAll(PDO::FETCH_ASSOC);
    }


    //Function to get specifc entry of DB
    function get_specific_entry($ID){
        include('connection.php');
        //Select all ITEMS where ID 
        $sql = 'SELECT * FROM entries WHERE id = ?';

        try {
            $results = $db -> prepare($sql);
            $results -> bindValue(1, $ID, PDO::PARAM_INT);
            $results -> execute();
        } catch (Exception $e) {
            echo "Error!: " . $e -> getMessage() . "<br />";
            return false;
        }
        return $results -> fetch();
    }

    //Function to get specifc entry of DB
    function get_new_entry($title){
        include('connection.php');
        //Select all ITEMS where ID 
        $sql = 'SELECT id FROM entries WHERE title = ?';

        try {
            $results = $db -> prepare($sql);
            $results -> bindValue(1, $title, PDO::PARAM_STR);
            $results -> execute();
        } catch (Exception $e) {
            echo "Error!: " . $e -> getMessage() . "<br />";
            return false;
        }
        return $results -> fetch();
    }

    //Function to get specifc entry of DB
    function update_specific_entry($ID, $title, $date, $time, $learned, $resources){
        include('connection.php');

        $sql = 'UPDATE entries SET title=?, date=?, time_spent=?, learned=?, resources=? WHERE id=?';
    
        try {
            $results = $db->prepare($sql);
            $results -> bindValue(1, $title, PDO::PARAM_STR);
            $results -> bindValue(2, $date, PDO::PARAM_STR);
            $results -> bindValue(3, $time, PDO::PARAM_INT);
            $results -> bindValue(4, $learned, PDO::PARAM_STR);
            $results -> bindValue(5, $resources, PDO::PARAM_STR);
            $results -> bindValue(6, $ID, PDO::PARAM_INT);
            $results -> execute();

        } catch (Exception $e) {
            echo "Error: "  . $e -> getMessage() . '<br/>';
            return false;
        }
        return true;
    }
    
    //Function to use formated date in all site
    // Resource to transform strtotime from https://stackoverflow.com/questions/6136430/a-non-well-formed-numeric-value-encountered
    function formated_date($date){
        return date("F d, Y ", strtotime($date));
    }

    //Function to add new entry
    function add_new_entry($title, $date, $time, $learned, $resources){
        include('connection.php');
        //Add new ITEM from 
        $sql = 'INSERT INTO entries(title, date, time_spent, learned, resources) VALUES (?, ?, ?, ?, ?)';
        try {
            $results = $db -> prepare($sql);
            $results -> bindValue(1, $title, PDO::PARAM_STR);
            $results -> bindValue(2, $date, PDO::PARAM_STR);
            $results -> bindValue(3, $time, PDO::PARAM_INT);
            $results -> bindValue(4, $learned, PDO::PARAM_LOB);
            $results -> bindValue(5, $resources, PDO::PARAM_LOB);
            $results -> execute();
        } catch (Exception $e) {
            echo "Error: "  . $e -> getMessage() . '<br/>';
            return false;
        }
        return true;
    }

    // Function to delete entry
    function delete_entry($ID){
        include('connection.php');
        $sql = 'DELETE FROM entries WHERE id = ?';
        try {
            $results = $db -> prepare($sql);
            $results -> bindValue(1, $ID, PDO::PARAM_INT);
            $results -> execute();
        } catch (Exception $e) {
            echo "Error: "  . $e -> getMessage() . '<br/>';
            return false;
        }
        return true;
    }

    // Function to get tags from entry 
    function get_tags_entry($ID){
        include('connection.php');
        $sql = 'SELECT tags.id, tags.tag FROM entries_tags_relation '
                . 'INNER JOIN entries ON entries_tags_relation.entry_id = entries.id '
                . 'INNER JOIN tags ON entries_tags_relation.tag_id = tags.id '
		        . 'WHERE entries.id = ?';
        try {
            $results = $db -> prepare($sql);
            $results -> bindValue(1, $ID, PDO::PARAM_INT);
            $results -> execute();
        } catch (Exception $e) {
            echo "Error: "  . $e -> getMessage() . '<br/>';
            return array();
        }
        return $results -> fetchAll(PDO::FETCH_ASSOC);
    }

    // Function to get entries from tag 
    function get_entries_tag($ID){
        include('connection.php');
        $sql = "SELECT entries.id, entries.title, entries.date FROM entries_tags_relation "
                . 'INNER JOIN entries ON entries_tags_relation.entry_id = entries.id '
                . 'INNER JOIN tags ON entries_tags_relation.tag_id = tags.id '
                . 'WHERE tags.id = ? '
                . 'ORDER BY entries.date DESC';
        try {
            $results = $db -> prepare($sql);
            $results -> bindValue(1, $ID, PDO::PARAM_INT);
            $results -> execute();
        } catch (Exception $e) {
            echo "Error: "  . $e -> getMessage() . '<br/>';
            return array();
        }
        return $results -> fetchAll(PDO::FETCH_ASSOC);
    }

    //Function to get all Tags
    function get_all_tags(){
        include('connection.php');
        $sql = 'SELECT id, tag FROM tags';
        try {
            $results = $db -> query($sql);
        } catch (Exception $e) {
            echo "Error: "  . $e -> getMessage() . '<br/>';
            return array();
        }
        return $results -> fetchAll(PDO::FETCH_ASSOC);
    }
    
    //Function to create new tag
    function create_new_tag($tag){
        include('connection.php');
        $sql = 'INSERT INTO tags (tag) VALUES (lower(?))';
        try {
            $results = $db -> prepare($sql);
            $results -> bindValue(1, $tag, PDO::PARAM_STR);
            $results -> execute();
        } catch (Exception $e) {
            echo "Error: "  . $e -> getMessage() . '<br/>';
            return false;
        }
        return true;
    }

    //Function associate tags
    function associate_tags($entryID, $tagID){
        include('connection.php');
        $sql = 'INSERT INTO entries_tags_relation (entry_id, tag_id) VALUES (?, ?)';
        try {
            $results = $db -> prepare($sql);
            $results -> bindValue(1, $entryID, PDO::PARAM_INT);
            $results -> bindValue(2, $tagID, PDO::PARAM_INT);
            $results -> execute();
        } catch (Exception $e) {
            echo "Error: "  . $e -> getMessage() . '<br/>';
            return false;
        }
        return true;
    }

    //Function associate tags
    function count_tags(){
        include('connection.php');
        $sql = 'SELECT count(tag) AS total FROM tags';
        try {
            $results = $db -> query($sql);
        } catch (Exception $e) {
            echo "Error: "  . $e -> getMessage() . '<br/>';
            return 0;
        }
        return $results -> fetchAll(PDO::FETCH_ASSOC);
    }



    //SQL STATEMENTS

    /*
    GET ALL INFORMATION TAGS & ENTRIES

        SELECT entries.title, tags.tag FROM entries_tags_relation
        INNER JOIN entries ON entries_tags_relation.entries_ID = entries.id
        INNER JOIN tags ON entries_tags_relation.tags_ID = tags.id
    */

    /*
    ADD TAGS
        
        INSERT INTO tags (tag) VALUES (lower('PERSONAL'))
    */

    /*
    RELATIONS ENTRIES AND TAGS

        INSERT INTO entries_tags_relation (entries_ID, tags_ID) VALUES (3, 2)
    */

?>


