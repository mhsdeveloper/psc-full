<?php
// note: seprator for csv file is "$"
// using data from this spreadsheet https://docs.google.com/spreadsheets/d/1f2upu_uLeAWeZsRcOpNe2VODaVNGRMlQaTpHarosHIg/edit?usp=sharing 
// that has modified header columns so that they can be accessed by array methods; 
namespace SubjectsManager\Helpers;


class CSVtoDB {
    private $csvFilePath;
    private $serverURL;
    private $seperatorValue;
    private $data;
    private $seeArray = [];
    // private $appendArray = [];


    public function __construct($csvFilePath, $seperatorValue){
        $this->csvFilePath = $csvFilePath;
        $this->seperatorValue = $seperatorValue;
        $this->serverURL = "http://" . $_SERVER['SERVER_ADDR'];
		if(false === strpos($_SERVER['SERVER_ADDR'], "192.168")) $this->serverURL = "https://www.primarysourcecoop.org";
        
        echo $this->serverURL;

		$this->headers = [];
		$this->headers[0] = "Topic";
		$this->headers[1] = "UT1";
		$this->headers[2] = "UT2";
		$this->headers[3] = "UT3";
		$this->headers[4] = "Projects";
		$this->headers[5] = "Definition";
		$this->headers[6] = "JQA_internal_note";
		$this->headers[7] = "RBT_internal_note";
		$this->headers[8] = "CMS_internal_note";
		$this->headers[9] = "ESR_internal_note";
		$this->headers[10] = "JQA_public_note";
		$this->headers[11] = "RBT_public_note";
		$this->headers[12] = "CMS_public_note";
		$this->headers[13] = "ESR_public_note";
    }

    public function process(){
        
        $this->data = $this->csvToArray();
        $this->createTopics($this->data);
        // $this->appendErrors();
        $this->updateTopicsWithSees();
        $this->createTopicRelationships($this->data);
        $this->createProjectTopicRelationships($this->data);
        $this->updateUmbrellaTopics();
    }

    private function postAPI($url){
        $url = str_replace(" ", '%20', $url);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //!! IGNORE SSL ERRORS!!
        //!! THESE ARE OKAY SINCE WE"RE JUST HARVESTING
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $resp = curl_exec($curl);
        
        if($resp === false){
			echo curl_error($curl);
        }

        curl_close($curl);

        return $resp;
    }


    private function getAPI($url){

        $url = str_replace(" ", '%20', $url);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_HTTPGET, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //!! IGNORE SSL ERRORS!!
        //!! THESE ARE OKAY SINCE WE"RE JUST HARVESTING
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $resp = curl_exec($curl);
        if($resp === false){
			echo curl_error($curl);
        }
        curl_close($curl);

        

        $resp = json_decode($resp, true);
        return $resp;
    }

    private function csvToArray(){
        $rows = [];
		$headers = null;
        if (file_exists(__DIR__ . $this->csvFilePath) && is_readable(__DIR__ . $this->csvFilePath)) {

			$count = 1;
            $handle = fopen(__DIR__ . $this->csvFilePath, 'r');
            while (!feof($handle)) {

                $row = fgetcsv($handle, 0, $this->seperatorValue , '"');
                if (empty($headers)){
                    $headers = $row;

				}
                else if (is_array($row)) {
                    array_splice($row, count($headers));
                    $rows[] = array_combine($headers, $row);
                }
            }
            fclose($handle);
            // print_r($rows);
		}
        else {
            $response = array(
                "error" => $this->csvFilePath . ' doesn`t exist or is not readable.',
            );
           
            // throw new Exception($this->csvFilePath . ' doesn`t exist or is not readable.');
        }

       
        return $rows;
    }

    private function createTopics($data){
        foreach ($data as $row) {
            // var_dump($row);
            // echo($row["Topic"]);
            $topicName = $row["Topic"];
                
            if(stripos($topicName, "SEE") !== false){
                $parts = explode(" SEE ", $topicName);

                $seeCombo = array(
                    "topic" => trim($parts[0]),
                    "see" => trim($parts[1]),
                    "definition" => trim($row["Definition"])
                );

                array_push($this->seeArray, $seeCombo);

            } else{

                $definition = trim($row["Definition"]);

                $url = $this->serverURL . "/subjectsmanager/createtopic?topic={$topicName}&see_id=&consensusDefinition={$definition}";

                // echo($url . "\n");

                $result = $this->postAPI($url);

            }
        }

        // print_r($this->seeArray);

    }

    private function updateTopicsWithSees(){
        if(!empty($this->seeArray)){
            foreach ($this->seeArray as &$seeCombo) {
                $topicName =  trim($seeCombo["topic"]);
                $see = trim($seeCombo["see"]);
                $definition = trim($seeCombo["definition"]);

                $response = $this->getAPI($this->serverURL . "/subjectsmanager/gettopicid?topic={$see}");

                
                if(isset($response[0])){
                    $seeId = $response[0]["id"];

                    $url = $this->serverURL . "/subjectsmanager/createtopic?topic={$topicName}&see_id={$seeId}&consensusDefinition={$definition}";
                    // echo($url);
                    $this->postAPI($url);
                } 
                
                else {
                    // print_r($response);
                    echo("ERROR: http://192.168.56.57/subjectsmanager/gettopicid?topic={$see} for seeid for {$see} for topic {$topicName}");

                }
            }
        }
    }

    private function createTopicRelationships($data){
        foreach ($data as $row) {
            $topicName = trim($row["Topic"]);
            

            for ($x = 1; $x < 4; $x++) {
                $relatedTopicName = trim($row['UT' . $x]);
                $relationship = 'broadensTo';

                $topicId = "";
                $relatedTopicId = "";
                if(!empty($relatedTopicName)){
                    $topicIdResp = $this->getAPI($this->serverURL . "/subjectsmanager/gettopicid?topic={$topicName}");
                    // print "\n";
                    // print_r($topicIdResp);
                    // echo("topic name" . $topicName);
                    
                    if(!empty($topicIdResp)){
                        $topicId = $topicIdResp[0]["id"];
                    }
                    
                    $relatedTopicIdResp = $this->getAPI($this->serverURL . "/subjectsmanager/gettopicid?topic={$relatedTopicName}");
                    // print_r($relatedTopicIdResp);
                    // print "\n";
                    // echo("related name" . $relatedTopicName);


                    if(!empty($relatedTopicIdResp)){
                        $relatedTopicId = $relatedTopicIdResp[0]["id"];
                    }

                    // print "\n";

                    if(strlen($topicId) > 0 && strlen($relatedTopicId) > 0){
                        $url = $this->serverURL . "/subjectsmanager/createtopicrelationship?topic_id={$topicId}&relationship={$relationship}&related_topic_id={$relatedTopicId}";
                        
                        // echo("FINAL URL" . $url);
                        
                        $this->postAPI($url);
                    }  
                }
            }
        }
    }

    private function createProjectTopicRelationships($data){
        foreach ($data as $row) {
            $topicName = trim($row["Topic"]);
            $projects = trim($row["Projects"]);


            $projectArr = explode(",", $projects);
            if(!empty($projectArr)){
                // print_r($projectArr);
                // echo("\n");
                foreach ($projectArr as &$project) {
                    if(strlen($project) != 0){ 
                        $project = trim($project);                
                                       
                        // if(str($row[$project . "_internal_note"])){
                        //     $internalNote = "";
                        // } else{
                            $internalNote = $row[$project . "_internal_note"];
                        // }

                        // if(empty($row[$project . "_public_note"])){
                        //     $publicNote = "";
                        // } else{
                            $publicNote = $row[$project . "_public_note"];
                        // }
                        

                        $topicIdResp = $this->getAPI($this->serverURL . "/subjectsmanager/gettopicid?topic={$topicName}");
                        // echo($this->serverURL . "/subjectsmanager/gettopicid?topic={$topicName} \n");
                        // print_r($topicIdResp);

                        if($topicIdResp && $topicIdResp[0]){
                            $topicId = $topicIdResp[0]["id"];
                            $url = $this->serverURL . "/subjectsmanager/createprojecttopicrelationship?topic_id={$topicId}&project={$project}&internalNote={$internalNote}&publicNote={$publicNote}";
                            
                            echo($url . "\n");
                            
                            $this->postAPI($url);
                        }
                    
                    }
                    
                }
            }

        }
    }

    private function updateUmbrellaTopics(){
        $umbrellaArr = $this->getAPI($this->serverURL . "/subjectsmanager/getumbrellaterms");

        if(!empty($umbrellaArr)){
            foreach ($umbrellaArr as $umbrella) {
                $topicName = $umbrella["topic_name"];


                if(!empty($topicName)){
                    $url = $this->serverURL . "/subjectsmanager/updateumbrella?topic_name={$topicName}&is_umbrella=1";
                    
                    // echo($url);
                    // echo "---UMBRELLA: " . $topicName . "    <br/>\n";
                    
                    $this->postAPI($url);
                }
            }

        }
    }
}