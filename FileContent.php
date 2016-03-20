<?php

require_once('Header.php');

$page_size = 10;
$page_number = 1;

if(isset($_POST["log_file_id"])){
    $log_file_id = $_POST["log_file_id"];
}
if(isset($_POST["last_id_next"])){
    $last_id_next = $_POST["last_id_next"];
}

if(isset($_POST["last_id_prev"])){
    $last_id_prev = $_POST["last_id_prev"];
}

if(isset($_POST["page_number"])){
    $page_number = $_POST["page_number"];
}
if(isset($_POST["page_size"])){
    $page_size = $_POST["page_size"];
}

$log_file = $db->files->findOne(array('_id' => new MongoId($log_file_id)));
$patternIDs = array_values(array_unique(array_map(function($l){return $l["patternID"];}, $log_file["logs"])));
$max_count = $db->patterns->find(array('_id' => array('$in' => $patternIDs)))->sort(array('_id' => 1))->count();

if(isset($last_id_next)){
    $cursor = $db->patterns->find(array('_id' => array('$gt' => new MongoId($last_id_next), '$in' => $patternIDs)))->sort(array('_id' => 1))->limit($page_size);
}
elseif(isset($last_id_prev)){
    $cursor = $db->patterns->find(array('_id' => array('$lt' => new MongoId($last_id_prev), '$in' => $patternIDs)))->sort(array('_id' => -1))->limit($page_size);
}
else{
    $cursor = $db->patterns->find(array('_id' => array('$in' => $patternIDs)))->sort(array('_id' => 1))->limit($page_size);
}

$countPip = array(
    array('$unwind' => '$logs'),
    array('$match'  => array('_id' => new MongoId($log_file_id))),
    array('$group'  => array(
			'_id'   => '$logs.patternID',
			'fileCount' => array('$sum' => 1)
		)));

$result = array_reduce($db->files->aggregate($countPip)["result"], function($carry, $item){$carry[strval($item["_id"])] = $item["fileCount"]; return $carry;}, array());
		
$logTable = "";
$logTableContent = "";

$logTable .= '<table class="file">';
$logTable .= '<tr><td colspan="2">Log</td><td>Quantité</td><td>Type</td><td>Tags</td></tr>';

$i = 0;

foreach ($cursor as $pat) {
    $logLine = "";
    $i++;
    $logLine .= '<tr>';
    $logLine .= '<td class="checkbox"><span class="' . ($pat["isChecked"] ? 'green' : 'red') . '">•</span></td>';
    $logLine .= '<td class="sample">';
    $logLine .= '<a href="#log_content" class="logContentLink"  data-container="#file_content" data-mongo-id="' . $log_file["_id"] . '" data-pattern-id="' . $pat["_id"] . '">' . htmlspecialchars(stripslashes($pat["regex_str"])) . '</a>';
    $logLine .= '</td>';
    $logLine .= '<td>' . $result[strval($pat["_id"])] . '</td>';
    $logLine .= '<td>BAGADADDDC</td>';
    $logLine .= '<td><span class="tag">#ERROR</span> <span class="tag">#DOCUMENT</span> <span class="tag">#GED</span></td>';
    $logLine .= '</tr>';
    
    if(isset($_POST["last_id_prev"])){
        $logTableContent = $logLine . $logTableContent;
        $last_id_prev = $pat["_id"];
        if($i == 1){
			$last_id_next = $pat["_id"];
        }
    }
    else{
        $logTableContent .= $logLine;
        $last_id_next = $pat["_id"];
        if($i == 1){
            $last_id_prev = $pat["_id"];
        }
    }
}
$logTable .= $logTableContent;

$logTable .= '</table>';
$logTable .= '</div>';


echo '<img src="img/check_list.png" class="title_icon"/><h1>Contenu du fichier</h1> - <span class="result-count">' . count($log_file["logs"]) . '</span> logs';
echo '<hr/>';

echo '<div class="pagination" data-last-id-next="' . $last_id_next 
    . '" data-last-id-prev="' . $last_id_prev 
    . '" data-page-number="' . $page_number
    . '" data-mongo-id="' . $log_file_id
    . '"><a href="#"   class="prev_page"><<</a>' . (($page_number - 1) * $page_size + 1) . '-' . (($page_number - 1) * $page_size + $cursor->count(true)) . '<a href="#" class="next_page">>></a></div>';

echo $logTable;
?>