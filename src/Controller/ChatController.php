<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ChatController extends Controller
{
    /**
     * @Route("/chat", name="chat")
     */
    public function index()
    {
	$user = $this->get('security.token_storage')->getToken()->getUser();
	$groups = $user->getGroups();
        return $this->render('chat/index.html.twig', [
            'controller_name' => 'ChatController',
	    'groups' => $groups
        ]);
    }
}
