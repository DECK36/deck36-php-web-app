<?php

namespace Deck36\Bundle\Plan9Bundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class PlayController
 *
 * @package Deck36\Bundle\Plan9Bundle\Controller
 */
class PlayController extends Controller
{
    /**
     * @Route("/overview", name="plan9_play_overview")
     * @Template()
     */
    public function overviewAction()
    {

        $solvedPix = $this->getSolvedPix();

        /*$grid = array();
        for ($i = 0; $i < $rows; $i++) {
            for ($j = 0; $j < $cols; $j++) {
                $currentPix = array();
                $currentPix[0] = $i;
                $currentPix[1] = $j;

                if (in_array('[' . $i . ',' . $j . ']', $solvedPix)) {
                    $currentPix[2] = true;
                } else {
                    $currentPix[2] = false;
                }

                array_push($grid, $currentPix);
            }
        }*/


        $overview = array(
            'solution_image' => $this->container->getParameter('deck36_plan9.parameter.overview.solutionimage'),
            'rows' => $this->container->getParameter('deck36_plan9.parameter.overview.size.rows'),
            'cols' => $this->container->getParameter('deck36_plan9.parameter.overview.size.cols'),
            'solved_pix' => $this->getSolvedPix(),
            'highscores' => $this->getHighScores(),
        );

        // die(var_dump($overview));

        return array('overview' => $overview);
    }

    /**
     * @Route("/playground", name="plan9_play_playground")
     * @Security("has_role('ROLE_USER')")
     * @Template()
     */
    public function playgroundAction(Request $request)
    {
        if ($request->attributes->get('isMobile', false)) {
            $rows = $this->container->getParameter('deck36_plan9.parameter.playground.mobilesize');
        } else {
            $rows = $this->container->getParameter('deck36_plan9.parameter.playground.desktopsize');
        }

        $badgesToShow = array();
        $this->get('security.context')->getToken()->getUser()->getBadges()->forAll(
            function ($key, $element) use ($badgesToShow) {
                $badgesToShow[] = $element;
            }
        );

        $cols = $rows;
        $playground = array(
            'rows' => $rows,
            'cols' => $cols,
            'col_image' => '../../../images/logo.png',
            'badges' => $badgesToShow,
            'highscores' => $this->getHighScores()
        );

        return array('playground' => $playground);
    }

    /**
     * @return array
     */
    private function getHighScores()
    {
        $redisClient = $this->get('snc_redis.default');

        return $redisClient->zrevrange('plan9_highscores', '0', '9', 'withscores');
    }

    /**
     * @return array
     */
    private function getSolvedPix()
    {
        $redisClient = $this->get('snc_redis.default');

        return $redisClient->smembers('plan9_pixel_solved');

        /*$encodedSolvedPix = array();
        $jsonEncodeValues = function($solvedPixel) use(&$encodedSolvedPix) {
            array_push($encodedSolvedPix, json_decode($solvedPixel));
        };
        array_map($jsonEncodeValues, $solvedPix);

        return $encodedSolvedPix;*/
    }
}
