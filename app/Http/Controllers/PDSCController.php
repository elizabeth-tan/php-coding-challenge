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
                    $line = str_replace(' ', ',', $line);
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
                        if($count == 5){
                            $cleanedArray['DateTime'] .= " ".trim($l);
                        }
                        $count++;
                    }
                    array_push($lines, $cleanedArray);
                }
            }
            fclose($fp);
        }
        $part1 = $this->formatArray($lines);
        $part2 = $this->sortArray($lines);
        $part3 = $this->getDistinctArray($part2);
        $the_output = array_merge($part1, $part2, $part3);
        $this->printOutput($the_output);
        return $the_output;
    }

    public function printOutput($input_value){
        $myfile = fopen("sample_output.txt", "w");
        foreach($input_value as $line){
            fwrite($myfile, $line."\n");
        }
        fclose($myfile);
    }

    public function formatArray($cleanedArray){
        $newArray = [];
        // <UserID>|<BytesTX>|<BytesRX>|<DateTime>|<ID>
       
        foreach($cleanedArray as $record){
            if(isset($record['UserID'])){
                $userID = $record['UserID'];
            }
            if(isset($record['BytesTX'])){
                $BytesTX = number_format((int)$record['BytesTX']);
            }
            if(isset($record['BytesRX'])){
                $BytesRX = number_format((int)$record['BytesRX']);
            }
            if(isset($record['DateTime'])){
                $DateTime = $record['DateTime'];
                $date = date_create($DateTime);
                $DateTime = date_format($date,"D, F d Y, H:i:s");
            }
            if(isset($record['ID'])){
                $ID = $record['ID'];
            }
            $formattedRecord = $userID."|".$BytesTX."|".$BytesRX."|".$DateTime."|".$ID;
            array_push($newArray, $formattedRecord);
        }
        return $newArray;
    }

    public function sortArray($cleanedArray){
        $ids_only = array_column($cleanedArray, 'ID');
        $sorted_array = [];
        foreach($ids_only as $ids){
            $pieces = explode("-", $ids);
            $the_object = ["id"=>(int)$pieces[0], "value"=>$ids];
            array_push($sorted_array, $the_object);
        }
        sort($sorted_array);
        $sorted_array = array_column($sorted_array, 'value');
        return $sorted_array;
    }

    public function getDistinctArray($cleanedArray){
        $result = array_unique($cleanedArray);
        $converted_array = [];
        for($x=0; $x<count($result); $x++){
            array_push($converted_array, '['.$x.']'." $result[$x]");
        }
        return $converted_array;
    }
}