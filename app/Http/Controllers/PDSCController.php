<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PDSCCOntroller extends Controller
{
    public function readFile(){
        $lines = [];
        // Keys: <ID>|<UserID>|<BytesTX>|<BytesRX>|<DateTime>
        $fp = fopen("sample-log.txt", "r");
        if ($fp) {
            while (!feof($fp)) {
                $line = fgets($fp);
                if ($line !== false) {
                    $line = str_replace('  ', ',', $line);
                    $line = str_replace("\r\n", ',', $line);
                    $line = explode(",",$line);
                    $line = array_filter($line);        
                    $cleanedArray = [];
                    $count = 0;
                    foreach($line as $l){
                        if($count == 0){
                            $cleanedArray['ID'] = trim($l);
                        }
                        if($count == 1){
                            $cleanedArray['UserID'] = trim($l);
                        }
                        if($count == 2){
                            $cleanedArray['BytesTX'] = trim($l);
                        }
                        if($count == 3){
                            $cleanedArray['BytesRX'] = trim($l);
                        }
                        if($count == 4){
                            $cleanedArray['DateTime'] = trim($l);
                        }
                        $count++;
                    }
                    array_push($lines, $cleanedArray);
                }
            }
            fclose($fp);
        }
        $the_answer = $this->formatArray($lines);
        return $the_answer;
    }

    public function sortArray($cleanedArray){
    }

    public function formatArray($cleanedArray){
        $newArray = [];
        // <UserID>|<BytesTX>|<BytesRX>|<DateTime>|<ID>
        foreach($cleanedArray as $record){
            $userID = $record['UserID'];
            $BytesTX = $record['BytesTX'];
            $BytesRX = $record['BytesRX'];
            $DateTime = $record['DateTime'];
            $ID = $record['ID'];
            $formattedRecord = $userID."|".$BytesTX."|".$BytesRX."|".$DateTime."|".$ID;
            array_push($newArray, $formattedRecord);
        }
        return $newArray;
    }

    public function getDistinctArray($cleanedArray){

    }
}