<?php

class ZoomAPI {
    /* The API Key, Secret, & URL will be used in every function. */

    //private $api_key = 'olJojBRFTiqbeM-EvLLa3Q';
    //private $api_secret = 'XDIciDFYTHnFJUx9bAYP2kvB1NJ0ZiAigRW5';
    //private $api_url = 'https://api.zoom.us/v1/';

    private $api_key = 'RwvgMcWpRjajqOI4QutldQ';
    private $api_secret = 'Ew6CpwhWCPrfNOe9UAl4Eyd2iMwVaVmL3EGs';
    private $api_url = 'https://api.zoom.us/v1/';
    private $admin_id = '-D5eW-CMTJCocauHSguLjw';

    /* Function to send HTTP POST Requests */
    /* Used by every function below to make HTTP POST call */

    function sendRequest($calledFunction, $data) {
        /* Creates the endpoint URL */
        $request_url = $this->api_url . $calledFunction;

        /* Adds the Key, Secret, & Datatype to the passed array */
        $data['api_key'] = $this->api_key;
        $data['api_secret'] = $this->api_secret;
        $data['data_type'] = 'JSON';

        $postFields = http_build_query($data);
        /* Check to see queried fields */
        /* Used for troubleshooting/debugging */
        //echo $postFields;

        /* Preparing Query... */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        /* Check for any errors */
        $errorMessage = curl_exec($ch);
        //echo $errorMessage;

        curl_close($ch);

        /* Will print back the response from the call */
        /* Used for troubleshooting/debugging		 */
        //echo $request_url;
        //var_dump($data);
        //var_dump($response);

        if (!$response) {
            return false;
        }
        /* Return the data in JSON format */
        return $response;
    }

    /* Functions for management of users */

    function createAUser() {
        $createAUserArray = array();
        $createAUserArray['email'] = $_POST['userEmail'];
        $createAUserArray['type'] = $_POST['userType'];
        return $this->sendRequest('user/create', $createAUserArray);
    }

    function createAMeeting($host_id, $meeting_topic, $meeting_type) {
        $createAMeetingArray = array();
        $createAMeetingArray['host_id'] = $host_id;
        $createAMeetingArray['topic'] = $meeting_topic;
        $createAMeetingArray['type'] = $meeting_type;
        return $this->sendRequest('meeting/create', $createAMeetingArray);
    }

    function listUsers() {
        $listUsersArray = array();
        return $this->sendRequest('user/list', $listUsersArray);
    }

    function isMeetingExpired($meeting_id, $host_id) {
        $meeting_info = json_decode($this->getMeetingInfo($meeting_id, $host_id));
        
        if (is_object($meeting_info)) {
            if (property_exists($meeting_info, "error") && $meeting_info->error->code == "3001") {
                return true;
            }
        }

        return false;
    }

    function autoCreateAUser() {
        $autoCreateAUserArray = array();
        $autoCreateAUserArray['email'] = $_POST['userEmail'];
        $autoCreateAUserArray['type'] = $_POST['userType'];
        $autoCreateAUserArray['password'] = $_POST['userPassword'];
        return $this->sendRequest('user/autocreate', $autoCreateAUserArray);
    }

    function listMeetings($host_id) {
        $listMeetingsArray = array();
        $listMeetingsArray['host_id'] = $host_id;
        return $this->sendRequest('meeting/list', $listMeetingsArray);
    }

    function getMeetingInfo($meeting_id, $host_id) {
        $getMeetingInfoArray = array();
        $getMeetingInfoArray['id'] = $meeting_id;
        $getMeetingInfoArray['host_id'] = $host_id;
        return $this->sendRequest('meeting/get', $getMeetingInfoArray);
    }

    function custCreateAUser($user_email, $user_type = 1) {
        $custCreateAUserArray = array();
        $custCreateAUserArray['email'] = $user_email;
        $custCreateAUserArray['type'] = $user_type;
        return $this->sendRequest('user/custcreate', $custCreateAUserArray);
    }

}

?>
