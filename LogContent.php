<?php

require_once('Header.php');

if(isset($_POST['pattern_id'])){
    $pattern = $db->patterns->findOne(array('_id' => new MongoId($_POST['pattern_id'])));
}
if(isset($_POST['log_file_id'])){
    $log_file = $db->files->findOne(array('_id' => new MongoId($_POST['log_file_id'])));
    $logs = array_values(array_filter($log_file['logs'], function($l) use($pattern) {return $l['patternID'] == $pattern['_id'];}));
    if(isset($_POST['log_index'])){
        $log = array_values(array_filter($log_file['logs'], function($l) use($pattern) {return $l['index'] == $_POST['log_index'];}))[0];
    }
    else{
        $log = $logs[0];
    }
}


echo '<img src="img/file.png" class="title_icon"/><h1>Contenu de la log</h1><h1 class="prev"><<</h1>';
echo '<hr/>';

echo '<div class="toolbar"><button type="submit" class="save" data-mongo-id="' . $log_file['_id'] . '" data-pattern-id="' . $pattern['_id'] . '">Enregistrer</button> <button type="submit" class="cancel">Annuler</button></div>';

echo '<div class="occurences">';
echo '<div class="contentTitle">Occurences</div>';
echo '<div class="logContent">';
echo '<ul>';
foreach($logs as $l){
    $result  = array();
    preg_match('#(.*) {4}(.*)\-\d+ {6}(\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2},\d{3}) ([A-Z]+) \[(.*)\]#', $l['header'], $result);
    if($l['index'] == $log['index']){
        echo '<li class="selected">' . $result[3] . '</li>';
    }
    else{
        echo '<li><a href="#" class="logSelection" data-log-index="' . $l['index'] . '" data-mongo-id="' . $log_file['_id'] . '" data-pattern-id="' . $pattern['_id'] . '">' . $result[3] . '</a></li>';
    }
}
echo '</ul>';
echo '</div>';
echo '</div>';


$result  = array();
preg_match('#(.*) {4}(.*)\-\d+ {6}(\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2},\d{3}) ([A-Z]+) \[(.*)\]#', $log['header'], $result);

echo '<div class="log_information">';
echo '<div class="contentTitle">Informations sur la log</div>';
echo '<div class="logContent">';

echo '<table>';
echo '<tr><td><button class="add">+</button></td><td class="input keys"></td></tr>';
echo '</table>';

echo '<table class="key_value">';
echo '<tr>';
echo '<td><button class="remove">-</button></td>';
echo '<td class="key" data-key="instance">Instance</td>';
echo '<td class="input">' . $result[1] . '</td>';
echo '</tr>';
echo '<tr>';
echo '<td><button class="remove">-</button></td>';
echo '<td class="key" data-key="process">Processus</td>';
echo '<td class="input">' . $result[2] . '</td>';
echo '</tr>';
echo '<tr>';
echo '<td><button class="remove">-</button></td>';
echo '<td class="key" data-key="date">Date</td>';
echo '<td class="input">' . $result[3] . '</td>';
echo '</tr>';
echo '<tr>';
echo '<td><button class="remove">-</button></td>';
echo '<td class="key" data-key="criticity">Criticité</td>';
echo '<td class="input">' . $result[4] . '</td>';
echo '</tr>';
echo '<tr>';
echo '<td><button class="remove">-</button></td>';
echo '<td class="key" data-key="claim_number">N° de sinistre</td>';
echo '<td class="input"></td>';
echo '</tr>';
echo '</table>';
echo '</div>';

echo '<div class="contentTitle">Contenu</div>';
echo '<pre class="logContent">' . htmlspecialchars($log['content']) . '</pre>';

//$pattern = $db->patterns->findOne(array('_id' => new MongoId($log['patternID'])));

echo '<div class="contentTitle">Règle n°1 - Motif d\'identification</div>';
echo '<pre class="rule">' . htmlspecialchars(stripslashes($pattern['regex_str'])) . '</pre>';

echo '</div>';

?>