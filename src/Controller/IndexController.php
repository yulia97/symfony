<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {

	
	if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ){
	     return $this->redirectToRoute('chat');
	}	

	
        return $this->render('index/index.html.twig', [
	    'controller_name' => 'IndexController',
        ]);

    }
}
