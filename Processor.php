<?php

require_once('Header.php');

if(isset($_POST['folderPath'])){
    
    //TO DO: Remove this line. This line is relevant only in development mode.
    $db->drop();
    
    //Directory path set by the user
    $dirPath = $_POST['folderPath'];
    
    if(!empty($dirPath)){//TO DO: Implement else to warn user the directory path is not set (may be control must be implemented in JS)
        if(!is_dir($dirPath)){//TO DO: Improve the message
            echo 'PROBLEM';
        }
        else{
            //Get all files from the directory specified
            $files = array_filter(scandir($dirPath), function($f){return !is_dir($f);});

            if(!empty($files)){//TO DO: Implement else to warn user the directory is empty
                
                //Creates/Retrieves a new collection of files which are going to be analysed.
                $collection = $db->files;
                
                //Creates/Retrieves a new collection for patterns.
                $patterns   = $db->patterns;

                //Loops through each file
                foreach($files as $file){
                    
                    //Builds the file path
                    $filePath = $dirPath . '/' . $file;
                    
                    //Get the file size in bytes
                    $fileSize = filesize($filePath);

                    //Splits the file retrieving each log block including its stacktrace.
                    $logs = preg_split('#(.* {4}.*\-\d+ {6}\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2},\d{3} [A-Z]+ \[.*\])#', file_get_contents($filePath), -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

                    //Stores the log content without the header
                    $logs_content = array();
                    //Stores the header of each log which contains the date, the process, the server...
                    $logs_header  = array();
                    
                    //Split each log into its header and its content
                    $both = array(&$logs_header, &$logs_content);
                    array_walk($logs, function($v, $k) use ($both) { $both[$k % 2][] = $v; });

                    //Keep only logs which header contains 'ERROR'
                    $logs_error_h = array_filter($logs_header, function($l){return preg_match('/ERROR/', $l);});

                    //Process the file only if it contains at least on error log
                    if(count($logs_error_h) != 0){
                        
                        //Number of error logs
                        $nbLogsError = count($logs_error_h);
                        //Number of warning logs
                        $nbLogsWarn  = count(array_filter($logs_header, function($l){return preg_match('/WARN/', $l);}));
                        //Number of info logs
                        $nbLogsInfo  = count(array_filter($logs_header, function($l){return preg_match('/INFO/', $l);}));

                        //Array of logs which is going to be persisted
                        $logs_error = array();
                        
                        //Loops through each error log headers
                        foreach($logs_error_h as $i => $l){
                            
                            //Encode the log content in UTF8
                            $logContent = utf8_encode($logs_content[$i]);

                            //Creates a pattern of each error log header or update an existing one which has the same content
                            //TODO: Improve using regexp or like clause (skipping for instance claim number)
                            $criteria = array('regex' => $logContent, 'regex_str' => $logContent);
                            $data = array('$inc' => array('count' => 1), '$set' => array('isChecked' => false));
                            $result = $db->command(array(
                                'findAndModify' => 'patterns',
                                'query' => $criteria,
                                'update' => $data,
                                'new' => true,     
                                'upsert' => true,
                                'fields' => array('_id' => 1)  
                            ));
                            $pat = $result['value'];

                            //Creates a log linked to the new/existing pattern
                            $log = array('index'     => $i, 
                                         'header'    => utf8_encode($l),
                                         'content'   => $logContent,
                                         'isChecked' => false,
                                         'patternID' => $pat['_id']);
                            array_push($logs_error, $log);
                        }

                        //Creates a new document files which gather all error logs
                        $document = array('name'    => $file,
                                          'size'    => $fileSize, 
                                          'nbTotal' => $nbLogsError + $nbLogsWarn + $nbLogsInfo,
                                          'nbError' => $nbLogsError,
                                          'nbWarn'  => $nbLogsWarn,
                                          'nbInfo'  => $nbLogsInfo,
                                          'logs'    => $logs_error);
                        $collection->insert($document);
                    }
                }
            }
        }
    }
}

?>