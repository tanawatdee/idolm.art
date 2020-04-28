<?php

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	$post = json_decode(file_get_contents("php://input"), true);

	$myfile = fopen(dirname(__FILE__)."/newfile.txt", "w");
	$txt = "Msg\n";
	fwrite($myfile, $txt);
	fwrite($myfile, file_get_contents("php://input"));
	fclose($myfile);


	if($post['object'] == 'page'){
		$webhook_event = $post['entry'][0]['messaging'][0];
		$sender_psid = $webhook_event['sender']['id'];
		if (isset($webhook_event['message'])) {
			handleMessage($sender_psid, $webhook_event['message']);        
		} else if (isset($webhook_event['postback'])) {
			handlePostback($sender_psid, $webhook_event['postback']);
		}
	}
	exit;
}
else if($_SERVER['REQUEST_METHOD'] === 'GET'){
	if($_GET['hub_mode'] == 'subscribe' && $_GET['hub_verify_token'] == ''){
		echo $_GET['hub_challenge'];
	}
	exit;
}
http_response_code(403);

function handleMessage($sender_psid, $received_message) {
	$response = [];
	if (isset($received_message['text'])) {
		$response = [
		  'text' => 'You sent the message: '.$received_message['text'].'. Now send me an image!'
		];
	} else if (isset($received_message['attachments'])) {
    $attachment_url = $received_message['attachments'][0]['payload']['url'];
    $response = [
      "attachment"=> [
        "type"=> "template",
        "payload"=> [
          "template_type"=> "generic",
          "elements"=> [[
            "title"=> "Is this the right picture?",
            "subtitle"=> "Tap a button to answer.",
            "image_url"=> $attachment_url,
            "buttons"=> [
              [
                "type"=> "postback",
                "title"=> "Yes!",
                "payload"=> "yes",
              ],
              [
                "type"=> "postback",
                "title"=> "No!",
                "payload"=> "no",
              ]
            ],
          ]]
        ]
      ]
    ];
  } 
	callSendAPI($sender_psid, $response); 
}

function handlePostback($sender_psid, $received_postback) {
	$response = [];
  
	$payload = $received_postback['payload'];

	if ($payload[0] == 'A') {
		$payload = explode('_', $payload);
		$db = new Database();
		$result = $db->call('setStatOrder', [$payload[1], 'PAID', null]);
		callSendAPI('1660884300669500', ['text'=>$payload[1].' อนุมัติแล้ว']);
		callSendAPI('1735962609857609', ['text'=>$payload[1].' อนุมัติแล้ว']);
	}
	else if ($payload[0] == 'E' && $payload[1] == 'A') {
		$payload = explode('_', $payload);
		$db = new Database();
		$result = $db->call('paidElection', [$payload[1]]);
		$code_result = $result['result'][0];
		
		$code_from = $code_result['from'];
		$code_to = $code_from + $code_result['amount'];
		$code_directory = dirname(__FILE__).'/../../private_html/vote/upload/code/';
		$code_list = [];
		for ($i_code = $code_from; $i_code < $code_to; $i_code++) {
			$code_list[] = $code_directory.'code_'.$i_code.'.jpg';
		}
		include_once dirname(__FILE__).'/mailer.php';
		Mail::sendCode($code_result['fbemail'], $code_result['fbname'], $code_list, date('d/m/y H:i', strtotime($code_result['order_time'])));

		callSendAPI('1660884300669500', ['text'=>$payload[1].' อนุมัติแล้ว']);
		callSendAPI('1735962609857609', ['text'=>$payload[1].' อนุมัติแล้ว']);
	}
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

  	$ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token=');
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
	curl_close($ch);
}

function sendApprove(){
	$sender_psid = '1735962609857609';//Tanawat Deepo
	$response = [
		"attachment" => [
		  	"type" =>  "template",
		  	"payload" =>  [
				"template_type" => "button",
			  	"text" => "<MESSAGE_TEXT>",
			  	"buttons" => [
				  	[
					  "type" => "postback",
					  "title" => "<BUTTON_TEXT>1",
					  "payload" => "<DEVELOPER_DEFINED_PAYLOAD>1"
					],[
					  "type" => "postback",
					  "title" => "<BUTTON_TEXT>2",
					  "payload" => "<DEVELOPER_DEFINED_PAYLOAD>2"
					]
				]
			]
		]
	];
	callSendAPI($sender_psid, $response);
}

?>