<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use App\Entity\Document;

class FileController extends Controller
{
    
    /**
     * @Route("/file", name="file")
     * @Method({"POST"})
     */
    public function index()
    {
	
	$g = [];
    	foreach($_FILES as $file){
	    $md5 = md5_file($file['tmp_name']);


	    $entityManager = $this->getDoctrine()->getManager();
	    $alreadyFile = $entityManager->getRepository(\App\Entity\Document::class)->findBy(array('md5' => $md5));


	    if (count($alreadyFile)){
		$g[] = ['md5' => $md5,
			'fname' => $alreadyFile[0]->getFilename()];
	    } else {
		
		$uploaddir = 'uploaded/images/';
		$uploadfile = $uploaddir.$md5.'.'.pathinfo($file['name'], PATHINFO_EXTENSION);;
		if (move_uploaded_file($file['tmp_name'], $uploadfile)) {
		    $doc = new Document();

		    $doc->setMd5($md5);
		    $doc->setFilename($uploadfile);
		    $doc->setOriginalFilename($file['name']);
		    $entityManager->persist($doc);
		    $entityManager->flush();
		    
		    $g[] = ['md5'=> $md5,
			    'fname' => $uploadfile];
		}
	    }
	    

	}
	
	return $this->json($g);
    }
}
