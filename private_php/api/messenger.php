<?php

class Messenger{
	const PAGE_TOKEN = '';

	function __construct(){
	}

	function __destruct(){
	}

	function sendApprove($order_code, $total_price, $name, $attachment_id){
		$response = [
			"attachment" => [
			  	"type" =>  "template",
			  	"payload" =>  [
					"template_type" => "media",
			         "elements" => [
			            [
			               "media_type" => "image",
			               "attachment_id" => $attachment_id,
			               "buttons" => [
			               		[
								  "type" => "postback",
								  "title" => "$order_code ฿$total_price",
								  "payload" => "O"
								],
							  	[
								  "type" => "postback",
								  "title" => "✓ $name",
								  "payload" => "A_$order_code"
								],
								[
								  "type" => "postback",
								  "title" => "X $name",
								  "payload" => "R_$order_code"
								]
							]
			            ]
			        ]
				]
			]
		];
		$this->callSendAPI('1660884300669500', $response);//Ratchapon Masphol
		$this->callSendAPI('1735962609857609', $response);//Tanawat Deepo
	}
	function sendElection($election_code, $amount, $attachment_id){
		$response = [
			"attachment" => [
			  	"type" =>  "template",
			  	"payload" =>  [
					"template_type" => "media",
			         "elements" => [
			            [
			               "media_type" => "image",
			               "attachment_id" => $attachment_id,
			               "buttons" => [
			               		[
								  "type" => "postback",
								  "title" => "$election_code $amount โค้ด",
								  "payload" => "O"
								],
							  	[
								  "type" => "postback",
								  "title" => "✓",
								  "payload" => "EA_$election_code"
								],
								[
								  "type" => "postback",
								  "title" => "X",
								  "payload" => "ER_$election_code"
								]
							]
			            ]
			        ]
				]
			]
		];
		$this->callSendAPI('1660884300669500', $response);//Ratchapon Masphol
		$this->callSendAPI('1735962609857609', $response);//Tanawat Deepo
	}

	function callSendAPI($sender_psid, $response) {
		$request_body = [
			'messaging_type' => 'RESPONSE',
			'recipient' => [
		    	'id' => $sender_psid
			],
			'message' => $response
		];

		$data_string = json_encode($request_body);

	  	$ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token='.self::PAGE_TOKEN);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    'Content-Type: application/json',
		    'Content-Length: ' . strlen($data_string))
		);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		$result = curl_exec($ch);

		$myfile = fopen(dirname(__FILE__)."/newfile.txt", "w");
	$txt = "Msg\n";
	fwrite($myfile, $txt);
	fwrite($myfile, $result);
	fclose($myfile);

		curl_close($ch);
	}

	function uploadImage($realpath, $mime){
		$ch = curl_init('https://graph.facebook.com/v2.6/me/message_attachments?access_token='.self::PAGE_TOKEN);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, ['message' => '{"attachment":{"type":"image", "payload":{"is_reusable":true}}}', 'filedata' => (new CURLFile($realpath, $mime))]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		$result = curl_exec($ch);

		curl_close($ch);

		return json_decode($result, true)['attachment_id'];
	}
}

?>