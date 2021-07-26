Mail.TM
=======

![](https://i.imgur.com/ob0h4xk.png)

MailTm class allows you to use all HTTP methods. You can create [mail.tm](https://mail.tm) accounts, get messages, delete account, all this using the [mail.tm api](https://api.mail.tm)

Create a new account
--------

```php
$MailTm::CreateAccount();

```

Get JWT token
--------

```php
$MailTm::JwtToken('mail', 'password');

```

Get available messages
--------

```php
$MailTm::GetMessage();

// Imbox is empty
if ($messages['total'] == 0) {
	echo "Not found any message\n";
} else {
	echo "\nMessages: \nTotal: ".$messages['total']."\n";
	for ($i=0; $i < $messages['total']; $i++) {

		$dat = $messages['messages'][$i];
		// Get complet msg
		$complet = $MailTm::GetMessageId($dat['id'], $jwtToken);

		echo '[I:'.$i.'] Msg id: '.$dat['id']. ' | Title: '.$dat['subject']."\n";
		echo 'Text: '.$complet['data']['text']."\n";
		echo 'From: '.$dat['from']['address'].' | Name: '.$dat['from']['name']."\n\n";
	}
}

```


Get a specific message
--------

```php
$MailTm::GetMessageId('msgID', 'jwtToken');

```


Delete the account created
--------

```php
$MailTm::DeleteAccount('jwtToken', 'Account Id');

```



Installation
------------

### Install source from GitHub
To install the source code:

    $ git clone https://github.com/devblack/curlx.git

And include it in your scripts:

    require_once "mailtm.php";
    $MailTm = new MailTm;


[the repository]: 