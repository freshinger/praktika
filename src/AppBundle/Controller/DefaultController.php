<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ));
    }
    
    /**
	 * @Route("/create/register", name="register")
	 */
	public function userAction(Request $request){
		$user = new User();
		$form = $this->createForm("AppBundle\Form\UserType", $user);
		$form->handleRequest($request);
        
        // Teilnehmer in die Datenbank aufnehmen
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
			
			/*$plainPassword = $request->request->get('_password');
			$encoder = $this->container->get('security.password_encoder');
			$encoded = $encoder->encodePassword($user, $plainPassword);
			$user->setPassword($encoded);*/
			
            $em->persist($user);
            $em->flush();
            
            return $this->redirectToRoute(
                'register',
                array('id' => $user->getId()));
        }
		return $this->render('default/form/register.html.twig', array(
            'form' => $form->createView()));
	}
}
