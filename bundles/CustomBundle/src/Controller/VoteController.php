<?php

namespace CustomBundle\Controller;

use CustomBundle\Model\Vote;
use CustomBundle\Model\Vote\Listing;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VoteController extends AbstractController
{
    /**
     * @Route("/vote")
     */
    public function voteAction(Request $request): Response
    {
        $vote = new Vote();
        $vote->setScore(3);
        $vote->setUsername('Adhi001'.mt_rand(1, 999));
        $vote->save();
        return $this->render('@CustomBundle/vote/vote.html.twig', [
            'vote'=>$vote,
        ]);
    }
    /**
     * @Route("/voteList")
     */
    public function listAction(Request $request): Response
    {
        $list = new Listing();
        $list->setCondition("score > ?", array(1));
        $votes = $list->load();
        return $this->render('@CustomBundle/vote/list.html.twig', [
            'votes'=>$votes,
        ]);
    }
}
