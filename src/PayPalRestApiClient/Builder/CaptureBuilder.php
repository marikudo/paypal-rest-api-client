<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;
use PayPalRestApiClient\Model\Capture;
use PayPalRestApiClient\Model\Amount;

/**
 * The CaptureBuilder builds instances of PayPalRestApiClient\Model\Capture
 *
 * CaptureBuilder depends on two other builders: AmountBuilder and LinkBuilder.
 */
class CaptureBuilder extends AbstractBuilder
{
    protected $amountBuilder;
    protected $linkBuilder;

    public function __construct()
    {
        $this->amountBuilder = new AmountBuilder();
        $this->linkBuilder = new LinkBuilder();
    }

    public function setAmountBuilder($amountBuilder)
    {
        $this->amountBuilder = $amountBuilder;
    }

    public function setLinksBuilder($linkBuilder)
    {
        $this->linkBuilder = $linkBuilder;
    }

    /**
     * Build an instance of PayPalRestApiClient\Model\Capture given an array
     *
     * @param array $data The array should contains the following keys: 
     * id, create_time, update_time, amount, is_final_capture, state, parent_payment, links.
     * 
     * @return PayPalRestApiClient\Model\Capture
     * 
     * @throws PayPalRestApiClient\Exception\BuilderException If not all keys are set
     *
     * @see https://developer.paypal.com/docs/api/#capture-object
     */
    public function build(array $data)
    {
        $this->validateArrayKeys(
            array('id', 'create_time', 'update_time', 'amount', 'is_final_capture', 'state', 'parent_payment', 'links'),
            $data
        );

        $links = array();
        foreach ($data['links'] as $link) {
            $links[] = $this->linkBuilder->build($link);
        }

        $capture = new Capture(
            $data['id'],
            $data['create_time'],
            $data['update_time'],
            $this->amountBuilder->build($data['amount']),
            $data['is_final_capture'],
            $data['state'],
            $data['parent_payment'],
            $links
        );
        $capture->setPaypalData($data);

        return $capture;
    }
}
