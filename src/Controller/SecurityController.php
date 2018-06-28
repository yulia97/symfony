<?php

// src/Controller/SecurityController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



use Symfony\Component\Form\FormError;
use App\Form\UserType;

use App\Entity\User;
use App\Entity\UserInfo;


use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;



class SecurityController extends Controller
{
     /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
	// get the login error if there is one
	$error = $authenticationUtils->getLastAuthenticationError();
	
	// last username entered by the user
	$lastUsername = $authenticationUtils->getLastUsername();
	
	return $this->render('login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
	));
    }



    /**
     * @Route("/register", name="registration")
     * @Method({"GET", "POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // 1) build the form
        $user = new User();
	$userInfo = new UserInfo();
        $form = $this->createForm(UserType::class, $user);
	$err = '';
        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
	    $entityManager = $this->getDoctrine()->getManager();
	    $alreadyUser = $entityManager->getRepository(\App\Entity\User::class)->findBy(array('username' => $user->getUsername()));
            // 3) Encode the password (you could also do this via Doctrine listener)

	    if (!count($alreadyUser)){
		$password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
		$user->setPassword($password);
		
		$user->setUserInfo($userInfo);
		// 4) save the User!

		$entityManager->persist($user);
		$entityManager->persist($userInfo);
		$entityManager->flush();
		
		// ... do any other work - like sending them an email, etc
		// maybe set a "flash" success message for the user
		
		return $this->redirectToRoute('index');
	    } else {
		$form->get('username')->addError(new FormError('User exists'));
	    }
        }
	
        return $this->render(
            'registration/register.html.twig',
            array('form' => $form->createView())
        );
    }
}
