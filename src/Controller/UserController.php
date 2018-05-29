<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\User;

class UserController extends Controller
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
	$user = $this->get('security.token_storage')->getToken()->getUser();
	return $this->json(["result"=>"200 Ok",
			    "user"=>$user->getId()]);
	
	
    }
}
