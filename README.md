Mail.TM
=======
[![CodeFactor](https://www.codefactor.io/repository/github/mateodioev/mail.tm/badge)](https://www.codefactor.io/repository/github/mateodioev/mail.tm)

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
$MailTm::GetToken('mail', 'password');

```


Get a specific message
--------

```php
$MailTm::GetMessageId('msgID', 'jwtToken');

```


Delete the account created
--------

```php
$MailTm::Delete('jwtToken', 'Account Id');

```



Installation
------------

### Install source from GitHub
To install the source code:

    $ git clone https://github.com/Mateodioev/mail.tm.git

And include it in your scripts:

    require_once "mailtm.php";
    $MailTm = new MailTm;


[the repository]: https://github.com/Mateodioev/mail.tm
