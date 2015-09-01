<?php
class Sms_model extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	function send_confirmation_SMS_tumasms($number,$amount,$sender_name)
		{

			$message = str_replace("amount",$amount,(str_replace("sender",$sender_name,"Thanks sender for making your contribution worth amount Ksh.FOCUS receives it with gratitude.")));
			# Load API class
			require APPPATH."/gateway/tumasms.php";

			# Setup API credentials
			$api_key = '3204ecce21629bc268c3216f43e38a38'; # Check under Settings->API Keys in Tumasms
			$api_signature = 'LVjZ/RDxhvsEC9L7Xas64whozPacLzKvRHYjfyfZkZIjVhBd9eoPz8Oy15iP8o8iuAim3QAFrT329597KSQGkeaudF6PPrI9w6gF4H9KPx4LwLyKnOo9MheQOAcRJzgvvE8nr/sW6BL6DZIlyiZ3ZzUA9EIoPWQrJYPbQTd95MI='; # Check under Manage Settings->API Keys in Tumasms
			
			# SEND SMS
			# Make API request
			$tumasms = new Tumasms($api_key, $api_signature); # Instantiate API library
			$tumasms->queue_sms($number, $message, "Vipepeo", ""); # Replace example with valid recipient, message, sender id and scheduled datetime if required in format ("YYYY-MM-DD HH:mm:ss")
			//$tumasms->queue_sms("+254733XXXXXX", "Message 2.", "Sender_ID", ""); # Replace example with valid recipient, message, sender id and scheduled datetime if required in format ("YYYY-MM-DD HH:mm:ss")
			$tumasms->send_sms(); # Initiate API call to send messages
			# Get API response
			echo $tumasms->status; # View status either (SUCCESS or FAIL)
			echo $tumasms->message; # Returns SMS available (Credits balance)
			echo $tumasms->description; # Returns a status message
			echo $tumasms->response_xml; # Returns full xml response
			echo $tumasms->response_json; # Returns full json response
			
			# GET BALANCE
			# Make API request
			$tumasms = new Tumasms($api_key, $api_signature); # Instantiate API library
			$tumasms->get_balance(); # Initiate API call to check available SMS credits
			# Get API response
			echo $tumasms->status; # View status either (SUCCESS or FAIL)
			echo $tumasms->message; # Returns SMS available (Credits balance)
			echo $tumasms->description; # Returns a status message
			echo $tumasms->response_xml; # Returns full xml response
			echo $tumasms->response_json; # Returns full json response	
			$status = $tumasms->status;
			$response = $tumasms->response_json;
			$this->save_msg($number,$message,$status,$response);
			
		}
		function send_confirmation_SMS_AIT($number,$amount,$sender_name)
		{
			
			$message = str_replace("amount",$amount,(str_replace("sender",$sender_name,"Thanks sender for making your contribution worth amount Ksh.FOCUS receives it with gratitude.")));

			// Be sure to include the file you've just downloaded
			require_once(APPPATH.'gateway/AfricasTalkingGateway.php');
			// Specify your login credentials
			$username   = "gracie";
			$apikey     = "9290020fc37dd90544f3de0d60d19b54bfa9cc2b8efad239a9442c54752afbcf";
			// Specify the numbers that you want to send to in a comma-separated list
			// Please ensure you include the country code (+254 for Kenya in this case)
			$recipients = $number;
			// And of course we want our recipients to know what we really do
			
			// Create a new instance of our awesome gateway class
			$gateway    = new AfricasTalkingGateway($username, $apikey);
			// Any gateway error will be captured by our custom Exception class below, 
			// so wrap the call in a try-catch block
			try 
			{ 
			  // Thats it, hit send and we'll take care of the rest. 
			  $results = $gateway->sendMessage($recipients, $message);
			            
			  foreach($results as $result) {
			    // status is either "Success" or "error message"
			    echo " Number: " .$result->number;
			    echo " Status: " .$result->status;
			    echo " MessageId: " .$result->messageId;
			    echo " Cost: "   .$result->cost."\n";

			    $this->save_msg($result->number,$message,$status);
			  }
			}
			catch ( AfricasTalkingGatewayException $e )
			{
			  echo "Encountered an error while sending: ".$e->getMessage();
			}
		}


	function save_msg($no,$msg,$status,$response){
		$Outbound_SMS = array(
			"number" => $no,
			"message" => $msg,
			"status" => $status,
			"response" => $response
			);

		$this->db->insert("outbound_sms",$Outbound_SMS);


	}

	function retrieve_msg($name=""){

	}

}
