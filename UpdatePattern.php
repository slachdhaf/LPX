<?php

require_once('Header.php');

if(isset($_POST['pattern_id'])){
    $pattern_id = $_POST['pattern_id'];
    $regex_str  = html_entity_decode(preg_quote($_POST['regex']));
    $regex      = preg_replace('/(•)\\1*/', '.*', $regex_str);
    $regex      = preg_replace('/\n/', '\r\n', $regex);

    $db->patterns->update(array('_id'  => new MongoId($pattern_id)), 
                          array('$set' => array('isChecked' => true,
                                                'regex_str' => $regex_str,
                                                'regex'     => $regex)));

    $pipeline = array(
        array('$unwind' => '$logs'),
        array('$match'  => array('logs.content'   => new MongoRegex('/' . $regex . '/i'),
                                 'logs.patternID' => array('$ne' => new MongoId($pattern_id)))),
        array('$group'  => array('_id'            => '$_id',
                                 'index'          => array('$addToSet' => '$logs.index'),
                                 'header'         => array('$addToSet' => '$logs.header'),//TO DO: Store in order to use for figures
                                 'oldPattern'     => array('$addToSet' => '$logs.patternID'))));

    $result = $db->files->aggregate($pipeline)['result'];

    foreach ($result as $file) {
        foreach($file['index'] as $index){

            $db->files->update(array('_id'        => new MongoId($file['_id']),
                                     'logs.index' => $index), 
                               array('$set'       => array('logs.$.patternID' => new MongoId($pattern_id))));

            $db->patterns->update(array('_id'     => new MongoId($pattern_id)), 
                                  array('$inc'    => array('count' => 1)));
        }
        $db->patterns->remove(array('_id'  => array('$in' => $file['oldPattern'])));
    }
}

?>