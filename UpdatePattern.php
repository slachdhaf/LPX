<?php

require_once('Header.php');

if(isset($_POST["pattern_id"])){
	$pattern_id = $_POST["pattern_id"];
    $regex_str  = html_entity_decode(preg_quote($_POST["regex"]));
    $regex      = preg_replace("/(•)\\1*/", ".*", $regex_str);
    $regex      = preg_replace("/\n/", "\r\n", $regex);
    
    $db->patterns->update(array("_id"  => new MongoId($pattern_id)), 
						  array('$set' => array("isChecked" => true,
                                                "regex_str" => $regex_str,
												"regex"     => $regex)));
    
    $count = 1;
    
    while($count > 0){
        $cursor = $db->files->findAndModify(array("logs.content" => new MongoRegex('/' . $regex . '/i')),
                       array('$set' => array("logs.$.patternID" => new MongoId($pattern_id))),
                       array("multiple" => true));
        $count = count($cursor);
    }
    //TO DO: remove pattern
    
    echo $cursor->count() . $regex;
}

?>