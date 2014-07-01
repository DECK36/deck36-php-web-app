<?php

namespace Deck36\Bundle\Plan9Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class DefaultController
 *
 * @package Deck36\Bundle\Plan9Bundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="plan9_home")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
