<?php

/**
 * @see            http://developers.mobyt.fr/#sms-send-api
 * @author         Matthieu Bride <me@elfen.fr>
 * @license        http://www.opensource.org/licenses/mit-license.php MIT License
 */
abstract class AbstractMobytServices
{
    const URL = 'https://app.mobyt.fr/API/v1.0/REST';
    const LOGIN_PATH = '/login?username=%s&password=%s';

    /**
     * @var string
     */
    private $userKey;

    /**
     * @var string
     */
    private $sessionKey;

    /**
     *
     * @param string $login
     * @param string $password
     */
    public function __construct($login, $password)
    {
        $this->initializeSession($login, $password);
    }

    /**
     * @throws \Exception
     */
    private function initializeSession($login, $password)
    {
        $loginSessionUrl = sprintf(
            self::LOGIN_PATH,
            $login,
            $password
        );

        $response = $this->curlHttpGet($loginSessionUrl);
        if ($response['http_code'] != 200) {
            throw new \Exception(sprintf(
                'Error http code: %s, body message: ',
                $response['http_code'],
                $response['response']
            ));
        } else {
            $values = explode(";", $response['response']);
            $this->userKey = $values[0];
            $this->sessionKey = $values[1];
        }
    }

    /**
     * @param string $url
     * @param bool   $withAuth
     *
     * @return array|bool
     */
    protected function curlHttpGet($url, $withAuth = false)
    {
        $ch = @curl_init();
        if (!$ch) {
            return false;
        }

        curl_setopt($ch, CURLOPT_URL, AbstractMobytServices::URL . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($withAuth) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-type: application/json',
                'user_key: ' . $this->userKey,
                'Session_key: ' . $this->sessionKey,
            ]);
        }

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        return $this->buildResponse($info['http_code'], $response);
    }

    /**
     * @param string $url
     * @param string $payload
     *
     * @return array|bool
     */
    protected function curlHttpPost($url, $payload)
    {
        $ch = @curl_init();
        if (!$ch) {
            return false;
        }

        curl_setopt($ch, CURLOPT_URL, AbstractMobytServices::URL . $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-type: application/json',
            'user_key: ' . $this->userKey,
            'Session_key: ' . $this->sessionKey,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Elfen/v.1 (curl)');
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        return $this->buildResponse($info['http_code'], $response);
    }

    private function buildResponse($httpCode, $response)
    {
        return [
            'http_code' => $httpCode,
            'response'  => $response,
        ];
    }

}
