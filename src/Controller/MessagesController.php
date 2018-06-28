<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Group;
use App\Entity\User;
use App\Entity\Message;
use App\Entity\Document;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
class MessagesController extends Controller
{

    /**
     * @Route("/messages/{id}", name="messages", requirements={"id"="\d+"})
     */
    public function index($id)
    {
	
	$user = $this->get('security.token_storage')->getToken()->getUser();
	$groups = $user->getGroups();

	foreach($groups as $group){
	    if ($group->getId() == $id){
		$messages = $group->getMessages();
		$g = [];
		foreach($messages as $message){
		    $files = $message->getDocuments();
		    $f = [];
		    foreach($files as $file){
			$f[] = ['path' => $file->getFilename(),
				'origname' => $file->getOriginalFilename()];
		    }

		    $g[] = ["id"=>$message->getId(),
			    "text"=>$message->getText(),
			    "author"=>$message->getAuthor()->getUsername(),
			    "user_id" => $message->getAuthor()->getId(),
			    'files' => $f];
		    
		    
		}

		return $this->json($g);
	    }
	}

	return $this->json([]);
	
    }

    
    /**
     * @Route("/messages/new", name="messages_new")
     * @Method({"POST"})
     */
    public function new_index(Request $request)
    {

	$encoders = array(new JsonEncoder());
	$normalizers = array(new ObjectNormalizer());
	$serializer = new Serializer($normalizers, $encoders);
	$entityManager = $this->getDoctrine()->getManager();
	//$user = $this->get('security.token_storage')->getToken()->getUser();
	$user = $this->get('security.token_storage')->getToken()->getUser();
	$group_id = $request->request->get('group_id');
	$text = $request->request->get('text');
	$files = $request->request->get('files');

	//return $this->json(['a'=>var_export($files)]);
	
	$group = $entityManager->getRepository(Group::class)->findBy(array('id' => $group_id));
	if (count($group) >= 1)
	    $group = $group[0];
	else
	    return $this->json(["result"=>"500 Error",
				"err"=>"no such group"]);


	//return $this->json(['f'=>$files]);
	foreach ($group->getUsers() as $u){
	    if ($user == $u){
		$message = new Message();


		if (!is_null($files))
		    foreach($files as $file){
			$alreadyFile = $entityManager->getRepository(\App\Entity\Document::class)->findBy(array('md5' => $file['md5']));
			if (count($alreadyFile)){
			    $alreadyFile[0]->addMessage($message);
			    //$message->addDocument();
			}
		    }
		
		
		$message->setAuthor($user);
		$message->setGroupId($group);
		$message->setText($text);
		
		$entityManager->persist($message);
		$entityManager->flush();
		
		$pusher = $this->container->get('gos_web_socket.wamp.pusher');
		//push(data, route_name, route_arguments)
		$pusher->push(['message' => 'new'], 'topic_xample', ['mode'=>'message','id'=>$group->getId()]);//'group/'.$group_id, ['username' => 'user1']);
		

		return $this->json(["result"=>"200 Ok",
				    "group"=>$group->getId(),
				    "user"=>$user->getUsername()]);

	    }
	}
	
	return $this->json(["result"=>"500 Error",
			    "err"=>"user is not in group"]);
	
	/*
	$group = new Group;
	
	$group->addUser($user);
	
	//$request->request->all();
	
	   $alias = $request->request->get('alias');
	$usernames = $request->request->get('users');

	if ($alias == null) $alias = 'd';
	$group->setAlias($alias);
	
	$entityManager = $this->getDoctrine()->getManager();
	
	
	
	foreach($usernames as $uname){
	   $user2 = $entityManager->getRepository(User::class)->findBy(array('username' => $uname));
	   if (count($user2) >= 1)
		$group->addUser($user2[0]);
	}

	
	$entityManager->persist($group);
	$entityManager->flush();
	
	return $this->json(["Result"=>"200 Ok",
			    "alias"=>$alias]);*/
    }

}
