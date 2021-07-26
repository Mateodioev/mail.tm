<?php
/**
 * PHP class for use mail.tm api
 * Documentation: https://api.mail.tm/
 * Github repository: https://github.com/Mateodioev/mail.tm
 * @author: @Mateodioev
 * Telegram Channel: https://t.me/caspercardergil
 */

class MailTm {
	const VERSION = '0.1';

	private static array $cURL = [
		CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLINFO_HEADER_OUT    => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_AUTOREFERER    => true,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0
	];

	private static string $jwtToken  = '';
	private static string $accountId = '';
	private static string $msgId     = '';
	private static string $mail      = '';
	private static string $password  = '';
	private static string $domain    = '';

	private static array $messages;

	private static $ch;
	private static $response;
	private static $info;
	private static $error_string;

	/**
	 * Function to send resquest to api
	 */
	private static function req($url, $headers = NULL, $method = 'GET', $post = NULL) {
		self::$ch = curl_init($url);
		curl_setopt_array(self::$ch, self::$cURL);
		curl_setopt_array(self::$ch, [CURLOPT_CUSTOMREQUEST => $method]);

		// Set headers, when $headers var is array
		if (!empty($headers) && is_array($headers)) {
			curl_setopt_array(self::$ch, [CURLOPT_HTTPHEADER => $headers]);
		}
		// Put data in postfields
		if ($post && $post != NULL) {
			curl_setopt_array(self::$ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post]);
		}

		// RUN resquest 
		self::$response = curl_exec(self::$ch);
		self::$info     = curl_getinfo(self::$ch);

		if (self::$response === FALSE) {

			self::$error_string = curl_error(self::$ch);

			// Close the resquest
			curl_close(self::$ch);

			return [
				'success' => false,
				'code'    => self::$info['http_code'],
				'error'   => self::$error_string,
			];
		} else {
			// Close the resquest
			curl_close(self::$ch);

			return [
				'success' => true,
				'code'    => self::$info['http_code'],
				'headers' => self::$info,
				'body'    => self::$response,
			];
		}
	}

	// Set an password and mail for the resquest
	private static function Datas() {
		self::$mail = uniqid();
		self::$password = str_shuffle(uniqid('1$@'));
	}

	// Get domains avaliable 
	private static function GetDomains() {
		// send resquest to the api
		$data = self::req('https://api.mail.tm/domains?page=1')['body'];
		$datas = json_decode($data, true);
		// Decode info
		self::$domain = $datas['hydra:member'][0]['domain'];
	}

	// Create the account | establece el accountId
	public static function CreateAccount($mailI = NULL, $passI = NULL) {
		if (empty($mail) || empty($pass)) {
			// Create the acount with random datas
			self::Datas();
		} else {
			self::$mail = $mailI;
			self::$password = $passI;
		}
		self::GetDomains();
		$mailn = self::$mail.'@'.self::$domain;
		self::$mail = $mailn;

		$header = ['Accept: application/ld+json', 'Content-Type: application/ld+json'];
		$post = json_encode([
			'address' => self::$mail,
			'password' => self::$password,
		]);
		$data = self::req('https://api.mail.tm/accounts', $header, 'POST', $post)['body'];
		$datas = json_decode($data, true);
		self::$accountId = $datas['id'];
		return [
			'mail' => self::$mail,
			'pass' => self::$password,
			'accid' => self::$accountId
		];
	}

	// GET JWT token
	public static function JwtToken($mails = NULL, $pass = NULL) {
		if (empty(self::$mail)) {
			// Function CreateAccount was no called
			$post = json_encode(['address' => $mails, 'password' => $pass]);
		} else {
			$post = json_encode(['address' => self::$mail, 'password' => self::$password]);
		}

		$header = ['Accept: application/json', 'Content-Type: application/json'];
		$data = self::req('https://api.mail.tm/token', $header, 'POST', $post)['body'];
		$datas = json_decode($data, true);

		self::$jwtToken = $datas['token'];
		return self::$jwtToken;
	}

	// DETELE ACCOUNT
	public static function DeleteAccount($jwtTokens = NULL, $accId = NULL) {
		if (empty(self::$jwtToken)) {
			// Function JwtToken was no called
			$headers = ['Accept: */*', 'Authorization: Bearer ' . $jwtTokens];
		} else {
			$headers = ['Accept: */*', 'Authorization: Bearer ' . self::$jwtToken];
		}

		if (!empty(self::$accountId)) {
			$accId = self::$accountId;
		}
		$a = self::req('https://api.mail.tm/accounts/'.$accId, $headers, 'DELETE');
		return json_decode($a['body'], true);
	}

	// GET MSGS
	public static function GetMessage($token = NULL) {
		if (!empty(self::$jwtToken)) {
			// Function JwtToken was called
			$token = self::$jwtToken;
		}
		$headers = ['Accept: application/ld+json', 'Authorization: Bearer '.$token];
		// Send resquest
		$data = self::req('https://api.mail.tm/messages?page=1', $headers, 'GET')['body'];
		$datas = json_decode($data, true);
		if (isset($datas['code'])) {
			// Invalid JWT token
			return ['success' => false, 'msg' => '['.$datas['code'].'] '.$datas['message']];
		} else {
			self::$messages = $datas['hydra:member'];
			return [
				'success' => true,
				'messages' => self::$messages,
				'total' => (int) $datas['hydra:totalItems']
			];
		}
	}

	// Get msg id
	public static function GetMessageId($msgID, $token = NULL) {
		if (!empty(self::$jwtToken)) {
			// Function JwtToken was called
			$token = self::$jwtToken;
		}
		$headers = ['Authorization: Bearer ' . $token];
		// Send resquest
		$data = self::req('https://api.mail.tm/messages/'.$msgID, $headers, 'GET')['body'];
		$datas = json_decode($data, true);
		if (isset($datas['hydra:description'])) {
			// Msg id no found
			return ['success' => false, 'msg' => $datas['hydra:title']."\n".$datas['hydra:description'] ];
		} elseif (isset($datas['code'])) {
			// Invalid JWT TOKEN
			return ['success' => false, 'msg' => '['.$datas['code'].'] '.$datas['message']];
		} else {
			// Done
			return ['success' => true, 'data' => $datas];
		}
	}

}

?>
