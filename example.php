<?php

/**
 * Repository: 
 */
require 'mailTm.php';


# Example how to use this class

$MailTm = new MailTm;

// Create the account in mail.tm
$acc = $MailTm::CreateAccount();

// Get JWT token
$jwtToken = $MailTm::JwtToken();
# $jwtToken = $MailTm::JwtToken($acc['mail'], $acc['pass']); | Another axample

// Get messages
$messages = $MailTm::GetMessage();
# $messages = $MailTm::GetMessage($jwtToken); | Another axample


// Print datas
echo 'Mail: ' . $acc['mail']."\n";
echo 'Pass: ' . $acc['pass']."\n";
echo 'Account Id: ' . $acc['accid']."\n";

echo 'JwtToken: ' . $jwtToken."\n";

if ($messages['total'] == 0) {
	echo "Not found any message\n";
} else {

	echo "\nMensajes: \nTotal: ".$messages['total']."\n";
	for ($i=0; $i < $messages['total']; $i++) {

		$dat = $messages['messages'][$i];
		// Get complet msgs
		$complet = $MailTm::GetMessageId($dat['id'], $jwtToken);

		echo '[I:'.$i.'] Msg id: '.$dat['id']. ' | Title: '.$dat['subject']."\n";
		echo 'Text: '.$complet['data']['text']."\n";
		echo 'From: '.$dat['from']['address'].' | Name: '.$dat['from']['name']."\n\n";
	}
}

// Delete the account created
$MailTm::DeleteAccount();
# $MailTm::DeleteAccount($jwtToken, $acc['accid']); | Another axample

?>