<?php

require_once('Header.php');

$page_size = 10;
$page_number = 1;

// sélectionne une collection (analogue à une table de base de données relationnelle)
$collection = $db->files;
$max_count = $collection->find()->count();

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

// récupère tout de la collection
if(isset($last_id_next)){
    $cursor = $collection->find(array('_id' => array('$gt' => new MongoId($last_id_next))))->sort(array('_id' => 1))->limit($page_size);
}
elseif(isset($last_id_prev)){
    $cursor = $collection->find(array('_id' => array('$lt' => new MongoId($last_id_prev))))->sort(array('_id' => -1))->limit($page_size);
}
else{
    $cursor = $collection->find()->sort(array('_id' => 1))->limit($page_size);
}

$i = 0;

$pagination = "";
$fileTable = "";
$fileLine = "";
$fileTableContent = "";

$fileTable .= '<table class="file">';
$fileTable .= '<tr><td colspan="2">Fichier</td><td>Taille</td><td>Nombre de logs</td><td>A vérifier</td><td>Total</td></tr>';

// traverse les résultats
foreach ($cursor as $logFile) {
    $i++;

    $nbTotal     = $logFile["nbTotal"];

    $nbLogsError  = round($logFile["nbError"]/$nbTotal*100);
    $nbLogsWarn   = round($logFile["nbWarn"]/$nbTotal*100);
    $nbLogsInfo   = round($logFile["nbInfo"]/$nbTotal*100);
    $nbNotChecked = count(array_filter($logFile["logs"], function($l){return !$l["isChecked"];}));
	
	$patternIDs = array_values(array_unique(array_map(function($l){return $l["patternID"];}, $logFile["logs"])));

	$isChecked = $db->patterns->findOne(array('_id' => array('$in' => $patternIDs), "isChecked" => false)) === null;

    $fileLine .= '<tr>';
    $fileLine .= '<td class="checkbox">';
    $fileLine .= '<span class="' . ($isChecked ? 'green' : 'red') . '">•</span>';
    $fileLine .= '</td>';
    $fileLine .= '<td class="logFileName"><a href="#file_content" class="logFileLink" data-container="#folder_results" data-mongo-id=' . $logFile["_id"] . '>' . $logFile["name"] . '</a></td>';
    $fileLine .= '<td>' . $logFile["size"] . '</td>';
    $fileLine .= '<td>';
    $fileLine .= '<table class="figures">';
    $fileLine .= '<tr>';
    $fileLine .= '<td class="red"><a href="#">' . $nbLogsError . '%</a></td>';
    $fileLine .= '<td class="orange"><a href="#">' . $nbLogsWarn . '%</a></td>';
    $fileLine .= '<td class="green"><a href="#">' . $nbLogsInfo . '%</a></td>';
    $fileLine .= '</tr>';
    $fileLine .= '</table>';
    $fileLine .= '</td><td>' . $nbNotChecked . '</td><td>' . $logFile["nbTotal"] . '</td>';
    $fileLine .= '</tr>';

    if(isset($_POST["last_id_prev"])){
        $fileTableContent = $fileLine . $fileTableContent;
        $last_id_prev = $logFile["_id"];
        if($i == 1){
            $last_id_next = $logFile["_id"];
        }
    }
    else{
        $fileTableContent .= $fileLine;
        $last_id_next = $logFile["_id"];
        if($i == 1){
            $last_id_prev = $logFile["_id"];
        }
    }
    $fileLine = "";
}
$fileTable .= $fileTableContent;

$isCheckedPip = array(
    array('$unwind' => '$logs'),
    array('$match'  => array('logs.isChecked' => false)),
    array(
        '$group' => array(
            '_id' => null,
            'check' => array('$sum' =>  1)
        )));

$result = $collection->aggregate($isCheckedPip)["result"];
if(count($result) > 0){
    $totalNotChecked = $result[0]['check'];
}
$pipeline = array(array(
    '$group' => array(
        '_id' => null,
        'error' => array('$sum' => '$nbError'),
        'warn'  => array('$sum' =>  '$nbWarn'),
        'info'  => array('$sum' =>  '$nbInfo')
    )));

$result = $collection->aggregate($pipeline)["result"];
if(count($result) > 0){
    $totals = $result[0];
}

if(isset($totals)){
    $total      = $totals["error"] + $totals["warn"] + $totals["info"];
    $totalError = round($totals["error"]/$total*100);
    $totalWarn  = round($totals["warn"] /$total*100);
    $totalInfo  = round($totals["info"] /$total*100);
}

if($i > 0){
    $fileTable .= '<tr><td colspan="2">Total</td><td></td><td><table class="figures"><tr>';
    $fileTable .= '<td class="red"><a href="#">' . $totalError . '%</a></td>';
    $fileTable .= '<td class="orange"><a href="#">' . $totalWarn . '%</a></a></td>';
    $fileTable .= '<td class="green"><a href="#">' . $totalInfo . '%</a></a></td></tr></table></td>';
    $fileTable .= '<td>' . $totalNotChecked . '</td><td>' . $total . '</td></tr>';
}

$fileTable .= '</table>';

echo '<img src="img/file.png" class="title_icon"/><h1>Résultats</h1> - <span class="result-count">' . $max_count . '</span> fichiers analysés - ';
echo '<hr/>';
echo '<div class="options">';
if(isset($last_id_next)){
    echo '<div class="pagination" data-last-id-next="' . $last_id_next 
        . '" data-last-id-prev="' . $last_id_prev 
        . '" data-page-number="' . $page_number . '"><a href="#"   class="prev_page"><<</a>' . (($page_number - 1) * $page_size + 1) . '-' . (($page_number - 1) * $page_size + $cursor->count(true)) . '<a href="#" class="next_page">>></a></div>';
}
echo '<a href="#" class="checkbox">•</a>';
echo '<table class="colors figures">';
echo '<tr><td class="red"><a href="#"></a></td><td class="orange"><a href="#"></a></td><td class="green"><a href="#"></a></td></tr>';
echo '</table>';
echo '</div>';

echo $fileTable;

?>