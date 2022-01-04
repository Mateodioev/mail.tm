<?php


/**
 * PHP class for use mail.tm api
 * @api-link: https://api.mail.tm/
 * @link https://github.com/Mateodioev/mail.tm
 * @author Mateodioev
 */
class MailTm {

    private static string $baseUrl = 'https://api.mail.tm/';
    private static string $id      = "";
    private static string $token   = "";

    public static string $mail     = "";
    public static string $password = "";


    /**
     * Interactuar con la API
     */
    private static function send(string $path, string $method = 'GET', $body = null) : array {
        
        $headers = [
            'accept: application/json',
            'authorization: Bearer ' . self::$token
        ];

        if (in_array($method, ['POST', 'PATCH'])) {
            $contentType = $method == 'PATCH' ? 'merge-patch+json' : 'json';
            $headers[] = 'content-type: application/'.$contentType;
            $body = json_encode($body);
        }
        
        $ch = curl_init(self::$baseUrl . $path);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0
        ]);

        if ($body) 
            curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $body]);

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);

        if ($info['http_code'] >= 400) {
            $error = curl_error($ch);
            curl_close($ch);
            return [
                'ok' => false,
                'code' => $info['http_code'],
                'info' => $info,
                'error' => $error,
                'response' => $response
            ];
        } else {
            curl_close($ch);
            return [
                'ok' => true,
                'code' => $info['http_code'],
                'body' => json_decode($response, true)
            ];
        }
    }

    /**
     * Crea datos random para el email y password
     */
    private static function CreateData(): void {
        self::$mail = uniqid();
        self::$password = str_shuffle(substr(uniqid('12$@3456'), 0, 15));
    }

    /**
     * Retrieves a Domain resource.
     */
    public static function GetDomains() : array 
    {
        $datas = self::send('domains?page=1');
        
       return $datas['code'] == 200 ? ['ok' => true, 'domains' => $datas['body'][0]['domain']] : ['ok' => false, 'error' => $datas['error']];
        
    }

    /**
     * Creates a Account resource.
     */
    public static function CreateAccount(?string $prefix = null, ?string $pass = null) : array 
    {
        self::CreateData();
       
        self::$mail = $prefix ?? self::$mail;
        self::$password = $pass ?? self::$password;

        $domains = self::GetDomains();

        if (!$domains['ok']) return $domains;

        self::$mail = self::$mail . '@' . $domains['domains'];

        $data = self::send('accounts', 'POST', ['address' => self::$mail, 'password' => self::$password]);
        
        if (!$data['ok']) return $data;

        $datas = $data['body'];
        self::$id = $datas['id'];
        return ['ok' => true, 'mail' => self::$mail, 'pass' => self::$password, 'accid' => self::$id];
    }

    /**
     * Get jwt token to login
     */
    public static function GetToken(?string $mail = null, ?string $pass = null) : array 
    {
        $mail = $mail ?? self::$mail;
        $pass = $pass ?? self::$password;
        $post = ['address' => $mail, 'password' => $pass];

        $data = self::send('token', 'POST', $post);
        if (!$data['ok']) return $data;

        self::$token = $data['body']['token'];
        return ['ok' => true, 'token' => $data['body']['token'], 'id' => $data['body']['id']];
    }

    /**
     * Removes the Account resource.
     */
    public static function Delete(?string $token = null, ?string $accid = null) : array 
    {
        $accid = self::$id ?? $accid;
        self::$token = $token ?? self::$token;
        return self::send('accounts/'.$accid, 'DELETE');
    }
    
    /**
     * Retrieves the collection of Message resources.
     */
    public static function GetMessage(int $page = 1, ?string $token = null): array
    {
        self::$token = $token ?? self::$token;

        return self::send('messages?page='.$page);
    }

    /**
     * Retrieves a Message resource.
     */
    public static function GetMessageId(string $msgId, ?string $token = null) : array
    {
        self::$token = $token ?? self::$token;
        return self::send('messages/'.$msgId);
    }

    /**
     * Removes the Message resource.
     */
    public static function DeleteMessageId(string $msgId, ?string $token = null): array
    {
        self::$token = $token ?? self::$token;

        return self::send('messages/'.$msgId, 'DELETE');
    }

    /**
     * Retrieves a Account resource.
     */
    public static function Me(?string $token): array
    {
        self::$token = $token ?? self::$token;
        return self::send('me');
    }
}
