<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;

use App\Entity\User;
use App\Entity\UserInfo;
use App\Entity\Like;

class UserController extends Controller
{
    /**
     * @Route("/user", name="user-specific")
     */
    public function index()
    {
	$user = $this->get('security.token_storage')->getToken()->getUser();
	return $this->json(["result"=>"200 Ok",
			    "user"=>$user->getId()]);	
    }

    /**
     * @Route("/user/{id}", name="user")
     */
    public function indexInfo($id)
    {
	$entityManager = $this->getDoctrine()->getManager();
	$user = $entityManager->getRepository(User::class)->findBy(array('id' => $id));
	
	
	if (count($user) == 0){
	    return $this->redirectToRoute('chat');
	}
	
	
	$me = $this->get('security.token_storage')->getToken()->getUser();
	$likedByMe = false;	
	
	$lbm = $entityManager->getRepository(Like::class)->findBy(array('liker' => $me, 'liked'=>$user[0]));
	
	if (count($lbm)) $likedByMe = true;	
	
	
	    
	//var_dump($user[0A]);
	
	/*if (count($user) == 0){
	   return $this->redirectToRoute('chat');
	   }
	 */  
	
	return $this->render('user/index.html.twig', [
	    'username' => $user[0]->getUsername(),
	    'user' => $user[0]->getId(),
	    'likedbyme' => $likedByMe,
	    'likes' => count($user[0]->getLikedBy()),
	    'status' => $user[0]->getUserInfo()->getStatus()
        ]);    
	
	
    }
    
    /**
     * @Route("/shout", name="user-shout")
     * @Method({"POST"})
     */
    public function shout(Request $request)
    {
	$entityManager = $this->getDoctrine()->getManager();
	//$user = $entityManager->getRepository(User::class)->findBy(array('id' => $id));
	$user = $this->get('security.token_storage')->getToken()->getUser();
	$text = $request->request->get('status');
	$uinf = $user->getUserInfo();
	$uinf->setStatus($text);

	$entityManager->persist($uinf);
	$entityManager->flush();

	return $this->json(["res"=>'200 ok', 'status' => $text]);
    }


    /**
     * @Route("/like/{id}", name="like-user")
     */
    public function likeUser($id)
    {
	$entityManager = $this->getDoctrine()->getManager();
	$user = $entityManager->getRepository(User::class)->findBy(array('id' => $id));

	$me = $this->get('security.token_storage')->getToken()->getUser();
	
	
	
	$like = new Like();
	$like->setLiker($me);
	$like->setLiked($user[0]);

	$entityManager->persist($like);
	$entityManager->flush();

	return $this->json(['res' => '200 Ok']);
    }

    /**
     * @Route("/dislike/{id}", name="dislike-user")
     */
    public function dislikeUser($id)
    {
	$entityManager = $this->getDoctrine()->getManager();
	$user = $entityManager->getRepository(User::class)->findBy(array('id' => $id));

	$me = $this->get('security.token_storage')->getToken()->getUser();


	$lbm = $entityManager->getRepository(Like::class)->findBy(array('liker' => $me, 'liked'=>$user[0]));


	
	if (count($lbm)){
	    	$like = $lbm[0];
	    $entityManager->remove($like);
	    $entityManager->flush();
	}
	return $this->json(['res' => '200 Ok']);
    }

}

