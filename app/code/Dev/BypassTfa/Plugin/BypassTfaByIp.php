<?php
declare(strict_types=1);
namespace Dev\BypassTfa\Plugin;

use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\TwoFactorAuth\Model\TfaSession;

class BypassTfaByIp
{
    /**
     * List of trusted IP addresses allowed to bypass TFA
     */
    private const ALLOWED_IPS = [
        '192.168.x.xxx', // Replace with your developer/office static IP
    ];

    /**
     * @var RemoteAddress
     */
    private RemoteAddress $remoteAddress;

    /**
     * @param RemoteAddress $remoteAddress
     */
    public function __construct(RemoteAddress $remoteAddress)
    {
        $this->remoteAddress = $remoteAddress;
    }

    /**
     * Bypass TFA if the client IP matches an allowed address.
     *
     * @param TfaSession $subject
     * @param bool $result
     * @return bool
     */
    public function afterIsGranted(TfaSession $subject, bool $result): bool
    {
        // If it's already granted, keep it true
        if ($result) {
            return true;
        }

        $clientIp = $this->remoteAddress->getRemoteAddress();


        // Check if current visitor IP is in our allowed white-list
        if ($clientIp && in_array($clientIp, self::ALLOWED_IPS, true)) {
            return true;
        }

        return $result;
        //return true; //allow all ip addresses to bypass tfa for testing purposes, remove this line in production
    }
}
