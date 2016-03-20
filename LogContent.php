<?php

require_once('Header.php');

if(isset($_POST["log_file_id"])){
    $log_file = $db->files->findOne(array('_id' => new MongoId($_POST["log_file_id"])));
    //$log = $log_file["logs"][$_POST["log_index"]];
}

if(isset($_POST["pattern_id"])){
    $pattern = $db->patterns->findOne(array('_id' => new MongoId($_POST["pattern_id"])));
}

$log = array_values(array_filter($log_file["logs"], function($l) use($pattern) {return $l["patternID"] == $pattern["_id"];}))[0];

$result  = array();
preg_match('#(.*) {4}(.*)\-\d+ {6}(\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2},\d{3}) ([A-Z]+) \[(.*)\]#', $log["header"], $result);

echo '<img src="img/file.png" class="title_icon"/><h1>Contenu de la log</h1>';
echo '<hr/>';

echo '<button type="submit" class="save" data-mongo-id="' . $log_file["_id"] . '" data-pattern-id="' . $pattern["_id"] . '">Enregistrer</button> <button type="submit">Annuler</button>';

echo '<div class="log_information">';
echo '<div class="contentTitle">Informations sur la log</div>';
echo '<div class="logContent">';

echo '<table>';
echo '<tr><td><button class="add">+</button></td><td class="input keys"></td></tr>';
echo '</table>';

echo '<table class="key_value">';
echo '<tr>';
echo '<td><button class="remove">-</button></td>';
echo '<td class="key">Instance</td>';
echo '<td class="input">' . $result[1] . '</td>';
echo '</tr>';
echo '<tr>';
echo '<td><button class="remove">-</button></td>';
echo '<td class="key">Processus</td>';
echo '<td class="input">' . $result[2] . '</td>';
echo '</tr>';
echo '<tr>';
echo '<td><button class="remove">-</button></td>';
echo '<td class="key">Date</td>';
echo '<td class="input">' . $result[3] . '</td>';
echo '</tr>';
echo '<tr>';
echo '<td><button class="remove">-</button></td>';
echo '<td class="key">Criticité</td>';
echo '<td class="input">' . $result[4] . '</td>';
echo '</tr>';
echo '<tr>';
echo '<td><button class="remove">-</button></td>';
echo '<td class="key">N° de sinistre</td>';
echo '<td class="input"></td>';
echo '</tr>';
echo '</table>';
echo '</div>';

echo '<div class="contentTitle">Contenu</div>';
echo '<pre class="logContent">' . htmlspecialchars($log["content"]) . '</pre>';

//$pattern = $db->patterns->findOne(array('_id' => new MongoId($log["patternID"])));

echo '<div class="contentTitle">Règle n°1 - Motif d\'identification</div>';
echo '<pre class="rule">' . htmlspecialchars(stripslashes($pattern["regex_str"])) . '</pre>';

echo '</div>';

?>