<?php

App::uses('AbstractMobytServices', 'Elfen.Lib/Mobyt');

/**
 * @author        Matthieu Bride <me@elfen.fr>
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
abstract class AbstractSendingSms extends AbstractMobytServices
{
    const DEFAULT_ENCODING = 'gsm';
    const DEFAULT_ALLOWED_INVALID_RECIPIENTS = false;
    const DEFAULT_RETURN_CREDITS = 'false';
    const DEFAULT_SCHEDULED_DELIVERY_TIME = null;
    const DEFAULT_SENDER = '';
    const DEFAULT_MESSAGE_TYPE = 'L';
    const DEFAULT_MESSAGE = '';
    const DEFAULT_RECIPIENTS = [];

    /**
     * The type of SMS.
     * (“N” for High quality with reception notification, “L” for Medium Quality)
     *
     * @var string
     */
    protected $messageType;

    /**
     * The body of the message. *Message max length could be 160 chars when using low-quality SMSs
     * (max 1000 chars with “gsm” encoding, 450 with “ucs2” encoding)
     *
     * @var string
     */
    protected $message;

    /**
     * A list of recipents phone numbers
     *
     * @var array
     */
    protected $recipients;

    /**
     * The Sender name. If the message type allows a custom TPOA and the field is left empty,
     * the user’s preferred TPOA is used. Must be empty if the message type does not allow a custom TPOA
     *
     * @var string
     */
    protected $sender;

    /**
     * The messages will be sent at the given scheduled time
     * [ddMMyy, yyyyMMdd, ddMMyyHHmm, yyyyMMddHHmmss, yyyy-MM-dd HH:mm, yyyy-MM-dd HH:mm.0]
     *
     * @var string
     */
    protected $scheduledDeliveryTime;

    /**
     * Specifies a custom order ID
     * (accepts only any letters, numbers, underscore, dot and dash)
     *
     * @var string
     */
    protected $orderId;

    /**
     * Returns the number of credits used instead of the number of messages.
     * i.e. when message is more than 160 chars long more than one credit is used
     *
     * @var bool
     */
    protected $returnCredits;

    /**
     * Sending to an invalid recipient does not block the operation
     *
     * @var bool
     */
    protected $allowInvalidRecipients;

    /**
     * The SMS encoding. Use UCS2 for non standard character sets
     * (“gsm” or “ucs2”)
     *
     * @var string
     */
    protected $encoding;

    /**
     * The id of the published page. Also add the %PAGESLINK____________% placeholder in the message body
     *
     * @var int
     */
    protected $idLanding;

    /**
     * The campaign name
     *
     * @var string
     */
    protected $campaignName;

    /**
     * The url where the short link redirects. Also add the %SHORT_LINK% placeholder in the message body
     *
     * @var string
     */
    protected $shortLinkUrl;

    /**
     *
     * @param string $login
     * @param string $password
     */
    public function __construct($login, $password)
    {
        $this->encoding = self::DEFAULT_ENCODING;
        $this->allowInvalidRecipients = self::DEFAULT_ALLOWED_INVALID_RECIPIENTS;
        $this->returnCredits = self::DEFAULT_RETURN_CREDITS;
        $this->scheduledDeliveryTime = self::DEFAULT_SCHEDULED_DELIVERY_TIME;
        $this->sender = self::DEFAULT_SENDER;
        $this->messageType = self::DEFAULT_MESSAGE_TYPE;
        $this->message = self::DEFAULT_MESSAGE;
        $this->recipients = self::DEFAULT_RECIPIENTS;

        parent::__construct($login, $password);
    }

    /**
     * @return array
     */
    public function send()
    {
        // Send request
        $response = $this->curlHttpPost($this->getUrlParts(), $this->buildPayload());

        // Interpret response
        $result = [];
        if (!$response || (!empty($response) && $response['http_code'] != 201)) {
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
            $result['data'] = json_decode($response['response'], true);
        }

        return $result;
    }

    /**
     * @return string
     */
    abstract protected function getUrlParts();

    /**
     * @return string
     */
    abstract protected function buildPayload();

    /**
     * @return string
     */
    public function getMessageType()
    {
        return $this->messageType;
    }

    /**
     * @param string $messageType
     *
     * @return $this
     */
    public function setMessageType($messageType)
    {
        $this->messageType = $messageType;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    abstract protected function setMessage($message);

    /**
     * @return array
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * @param array $recipients
     *
     * @return $this
     */
    abstract protected function setRecipients(array $recipients);

    /**
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param string $sender
     *
     * @return $this
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @return string
     */
    public function getScheduledDeliveryTime()
    {
        return $this->scheduledDeliveryTime;
    }

    /**
     * @param string $scheduledDeliveryTime
     *
     * @return $this
     */
    public function setScheduledDeliveryTime($scheduledDeliveryTime)
    {
        $this->scheduledDeliveryTime = $scheduledDeliveryTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param string $encoding
     *
     * @return $this
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     *
     * @return $this
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * @return int
     */
    public function getIdLanding()
    {
        return $this->idLanding;
    }

    /**
     * @param int $idLanding
     *
     * @return $this
     */
    public function setIdLanding($idLanding)
    {
        $this->idLanding = $idLanding;

        return $this;
    }

    /**
     * @return string
     */
    public function getCampaignName()
    {
        return $this->campaignName;
    }

    /**
     * @param string $campaignName
     *
     * @return $this
     */
    public function setCampaignName($campaignName)
    {
        $this->campaignName = $campaignName;

        return $this;
    }

    /**
     * @return string
     */
    public function getShortLinkUrl()
    {
        return $this->shortLinkUrl;
    }

    /**
     * @param string $shortLinkUrl
     *
     * @return $this
     */
    public function setShortLinkUrl($shortLinkUrl)
    {
        $this->shortLinkUrl = $shortLinkUrl;

        return $this;
    }

    /**
     * @param string $recipient
     *
     * @return $this
     */
    public function setRecipient($recipient)
    {
        $this->setRecipients([$recipient]);

        return $this;
    }

    /**
     * @return bool
     */
    public function isReturnCredits()
    {
        return $this->returnCredits;
    }

    /**
     * @return bool
     */
    public function getReturnCredits()
    {
        return $this->returnCredits;
    }

    /**
     * @param bool $returnCredits
     *
     * @return $this
     */
    public function setReturnCredits($returnCredits)
    {
        $this->returnCredits = $returnCredits;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowInvalidRecipients()
    {
        return $this->allowInvalidRecipients;
    }

    /**
     * @return bool
     */
    public function getAllowInvalidRecipients()
    {
        return $this->allowInvalidRecipients;
    }

    /**
     * @param bool $allowInvalidRecipients
     *
     * @return $this
     */
    public function setAllowInvalidRecipients($allowInvalidRecipients)
    {
        $this->allowInvalidRecipients = $allowInvalidRecipients;

        return $this;
    }

}
