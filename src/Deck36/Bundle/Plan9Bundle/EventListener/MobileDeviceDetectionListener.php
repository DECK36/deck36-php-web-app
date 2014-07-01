<?php
/**
 * Declares the MobileDeviceDetectionListener class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 */

namespace Deck36\Bundle\Plan9Bundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class MobileDeviceDetectionListener
 *
 * Used to detect if the requesting user-agent is on a mobile device.
 *
 * @package Deck36\Bundle\Plan9Bundle\EventListener
 */
class MobileDeviceDetectionListener
{

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (preg_match(
            '/(android|bb\d+|meego)\.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip
            (hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile\.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p
            (ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',
            $request->headers->get('user-agent')
        )) {
            $request->attributes->set('isMobile', true);
        } else {
            $request->attributes->set('isMobile', false);
        }
    }
}