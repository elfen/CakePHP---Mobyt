<?php

App::uses('AbstractSendingSms', 'Elfen.Lib/Mobyt/Sms');

/**
 * Create and send SMS Simple
 *
 * @author        Matthieu Bride <me@elfen.fr>
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
class SmsSimple extends AbstractSendingSms
{
    const URL_PART = '/sms';

    /**
     * Expected format: ["", "", ...]
     *
     * {@inheritdoc}
     */
    public function setRecipients(array $recipients)
    {
        $this->recipients = $recipients;

        return $this;
    }

    /**
     * Expected format: "..."
     *
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getUrlParts()
    {
        return self::URL_PART;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildPayload()
    {
        $payload = [];

        $payload['message_type'] = $this->getMessageType();
        $payload['message'] = $this->getMessage();
        $payload['recipient'] = $this->getRecipients();
        $payload['sender'] = $this->getSender();
        $payload['scheduled_delivery_time'] = $this->getScheduledDeliveryTime();
        $payload['returnCredits'] = $this->getReturnCredits();
        $payload['allowInvalidRecipients'] = $this->getAllowInvalidRecipients();
        $payload['encoding'] = $this->getEncoding();

        if ($this->getOrderId() !== null) {
            $payload['order_id'] = $this->getOrderId();
        }

        if ($this->getIdLanding() !== null) {
            $payload['id_landing'] = $this->getIdLanding();
        }

        if ($this->getCampaignName() !== null) {
            $payload['campaign_name'] = $this->getCampaignName();
        }

        if ($this->getShortLinkUrl() !== null) {
            $payload['short_link_url'] = $this->getShortLinkUrl();
        }

        return json_encode($payload);
    }

}