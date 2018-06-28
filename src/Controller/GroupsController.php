<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Group;
use App\Entity\User;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class GroupsController extends Controller
{
    
    /**
     * @Route("/groups", name="groups")
     */
    public function index()
    {
	$user = $this->get('security.token_storage')->getToken()->getUser();
	$groups = $user->getGroups();


	$g = [];
	foreach($groups as $group){
	    $g[] = ["id"=>$group->getId(),
	    "alias"=>$group->getAlias()];
	}
	
	return $this->json($g);
    }

    
    /**
     * @Route("/groups/new", name="groups_new")
     * @Method({"GET", "POST"})
     */
    public function new_index(Request $request)
    {

	$encoders = array(new JsonEncoder());
	$normalizers = array(new ObjectNormalizer());
	$serializer = new Serializer($normalizers, $encoders);
	
	//$user = $this->get('security.token_storage')->getToken()->getUser();
	$user = $this->get('security.token_storage')->getToken()->getUser();
	$group = new Group;
	
	$group->addUser($user);
	$pusher = $this->container->get('gos_web_socket.wamp.pusher');
	//push(data, route_name, route_arguments)
	$pusher->push(['group' => 'new'], 'topic_xample', ['mode'=>'group','id'=>$user->getId()]);//'group/'.$group_id, ['username' => 'user1']);
	
	//$request->request->all();
	
	$alias = $request->request->get('alias');
	$usernames = $request->request->get('users');
	
	if ($alias == null) $alias = 'd';
	$group->setAlias($alias);
	
	$entityManager = $this->getDoctrine()->getManager();

	foreach($usernames as $uname){
	    $user2 = $entityManager->getRepository(User::class)->findBy(array('username' => $uname));
	    if (count($user2) >= 1){
		$group->addUser($user2[0]);
		$pusher->push(['group' => 'new'], 'topic_xample', ['mode'=>'group','id'=>$user2[0]->getId()]);//'group/'.$group_id, ['username' => 'user1']);
	    }
	}

	
	$entityManager->persist($group);
	$entityManager->flush();
	
	$alias = 'dd';
	return $this->json(["Result"=>"200 Ok",
			    "alias"=>$alias]);
    }


    
    
    
    
}
