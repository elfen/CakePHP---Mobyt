<?php

App::uses('AbstractMobytServices', 'Elfen.Lib/Mobyt');

/**
 * Class SmsState
 *
 * @package Mobyt\Sms
 */
class SmsState extends AbstractMobytServices
{
    const URL = '/sms/%s';
    const DEFAULT_ORDER_ID = null;

    private $orderId;

    /**
     *
     * @param string $login
     * @param string $password
     */
    public function __construct($login, $password)
    {
        $this->orderId = self::DEFAULT_ORDER_ID;

        parent::__construct($login, $password);
    }

    /**
     * Send request to Mobyt
     *
     * @return array
     */
    public function send()
    {
        // Build URL
        $url = sprintf(self::URL, $this->getOrderId());

        // Send requeest with use of authentication
        $response = $this->curlHttpGet($url, true);

        // Interpret response
        $result = [];
        if (!$response || (!empty($response) && $response['http_code'] != 200)) {
            $result["success"] = false;
            if ($response) {
                $result["message"] = sprintf(
                    'Error! http code: %s, body message: %s',
                    $response['http_code'],
                    $response['response']
                );
            } else {
                $result['data'] = 'Internal error';
            }
        } else {
            $result['success'] = true;
            $result['message'] = json_decode($response['response'], true);
        }

        return $result;
    }


    /**
     * @return null
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param null $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

}