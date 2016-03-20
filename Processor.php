<?php

require_once('Header.php');

if(isset($_POST['folderPath'])){
    $db->drop();
    $dirPath = $_POST['folderPath'];
    if(!empty($dirPath)){
        if(!is_dir($dirPath)){
            echo "PROBLEM";
        }
        else{
            $files = array_filter(scandir($dirPath), function($f){return !is_dir($f);});

            if(!empty($files)){
                $collection = $db->files;
				$patterns   = $db->patterns;

                foreach($files as $file){
                    $filePath = $dirPath . '/' . $file;
                    $fileSize = filesize($filePath);
                    
                    $logs = preg_split("#(ccbatch {4}.*\-\d+ {6}\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2},\d{3} [A-Z]+ \[.*\])#", file_get_contents($filePath), -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                    
                    $logs_content = array();
                    $logs_header  = array();
                    $both = array(&$logs_header, &$logs_content);
                    array_walk($logs, function($v, $k) use ($both) { $both[$k % 2][] = $v; });
                        
                    $logs_error_h = array_filter($logs_header, function($l){return preg_match("/ERROR/", $l);});

                    if(count($logs_error_h) != 0){
                        $nbLogsError = count($logs_error_h);
                        $nbLogsWarn  = count(array_filter($logs_header, function($l){return preg_match("/WARN/", $l);}));
                        $nbLogsInfo  = count(array_filter($logs_header, function($l){return preg_match("/INFO/", $l);}));
                        
                        $logs_error = array();
                        foreach($logs_error_h as $i => $l){
							$logContent = utf8_encode($logs_content[$i]);
							
							//TODO: Improve using regexp or like clause
							$criteria = array('regex' => $logContent, 'regex_str' => $logContent);
							$data = array('$inc' => array("count" => 1), '$set' => array("isChecked" => false));
							$result = $db->command(array(
								'findAndModify' => 'patterns',
								'query' => $criteria,
								'update' => $data,
								'new' => true,     
								'upsert' => true,
								'fields' => array('_id' => 1)  
							));
							$pat = $result['value'];
							
                            $log = array("index"     => $i, 
                                         "header"    => utf8_encode($l),
                                         "content"   => $logContent,
                                         "isChecked" => false,
                                         "patternID" => $pat["_id"]);
                            array_push($logs_error, $log);
                        }
                        
                        $document = array("name"    => $file,
                                          "size"    => $fileSize, 
                                          "nbTotal" => $nbLogsError + $nbLogsWarn + $nbLogsInfo,
                                          "nbError" => $nbLogsError,
                                          "nbWarn"  => $nbLogsWarn,
                                          "nbInfo"  => $nbLogsInfo,
                                          "logs"    => $logs_error);
                        $collection->insert($document);
                    }
                }
            }
        }
    }
}

?>