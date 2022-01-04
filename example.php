<?php

/**
 * Repository: https://github.com/Mateodioev/mail.tm
 */
require 'mailTm.php';


# Example how to use this class

$MailTm = new MailTm;

// Create the account in mail.tm
$acc = $MailTm::CreateAccount();

// Get JWT token
$jwtToken = $MailTm::JwtToken()['token'];
# $jwtToken = $MailTm::JwtToken($acc['mail'], $acc['pass']); | Another axample

// Get messages
$messages = $MailTm::GetMessage();
# $messages = $MailTm::GetMessage($jwtToken); | Another axample


// Print datas
echo 'Mail: ' . $acc['mail']."\n";
echo 'Pass: ' . $acc['pass']."\n";
echo 'Account Id: ' . $acc['accid']."\n";

echo 'JwtToken: ' . $jwtToken."\n";

// Delete the account created
$MailTm::DeleteAccount();
# $MailTm::DeleteAccount($jwtToken, $acc['accid']); | Another axample

?>
