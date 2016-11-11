<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Entity\Firma;
use AppBundle\Form\FirmaType;
use AppBundle\Entity\Ansprechpartner;
use AppBundle\Form\AnsprechpartnerType;

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
	 * @Route("/create/user", name="registration")
	 */
	public function userAction(Request $request){
		$user = new User();
		$form = $this->createForm("AppBundle\Form\UserType", $user);
		$form->handleRequest($request);
        
        // Teilnehmer in die Datenbank aufnehmen
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
			
			$factory = $this->get('security.encoder_factory');
			
			$encoder = $factory->getEncoder($user);
			$password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
			$user->setPassword($password);
			/*$password = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());
            $user->setPassword($password);*/
			
			/*$plainPassword = $request->request->get('_password');
			$encoder = $this->container->get('security.password_encoder');
			$encoded = $encoder->encodePassword($plainPassword);
			$user->setPassword($encoded);*/
			
            $em->persist($user);
            $em->flush();
            
            return $this->redirectToRoute(
                'registration',
                array('id' => $user->getId()));
        }
		return $this->render('default/form/registration.html.twig', array(
            'form' => $form->createView()));
	}
	
	/**
     * @Route("/admin")
     */
    public function adminAction()
    {
        return new Response('<html><body>Admin page!</body></html>');
    }
    
    /**
    * @Route("/create/success/firma/{name}", name="form_success")
    */
    public function successfirmaAction($name)
    {

           return $this->render('default/form/firma_success.html.twig', array(
                   'name' => $name
           ));
    }

    /**
    * @Route("/create/firma", name="formfirma")
    */
    public function firmaAction(Request $request)
    {
           $firma = new Firma();
           $form = $this->createForm('AppBundle\Form\FirmaType', $firma);

           $form->handleRequest($request);

           if($form->isSubmitted() && $form->isValid()){
                   $firma = $form->getData();

                   $em = $this->getDoctrine()->getManager();
                   $em->persist($firma);
                   $em->flush();

                   return $this->redirectToRoute('form_success', array(
                           'name' => $firma->getName()
                   ));
           }

           return $this->render('default/form/firma.html.twig', array(
                   'form' => $form->createView()
           ));
    }
    
    /**
    * @Route("/create/success/contact/{name}", name="contact_success")
    */
    public function successContactAction($name)
    {

           return $this->render('default/form/contact_success.html.twig', array(
                   'name' => $name
           ));
    }

    /**
    * @Route("/create/contact", name="formcontact")
    */
    public function contactAction(Request $request)
    {
           $ansprechpartner = new Ansprechpartner();
           $form = $this->createForm('AppBundle\Form\AnsprechpartnerType', $ansprechpartner);

           $form->handleRequest($request);

           if($form->isSubmitted() && $form->isValid()){
                   $firma = $form->getData();

                   $em = $this->getDoctrine()->getManager();
                   $em->persist($firma);
                   $em->flush();

                   return $this->redirectToRoute('contact_success', array(
                           'name' => $ansprechpartner->getPrename(). " " .
                                $ansprechpartner->getSurname()
                   ));
           }

           return $this->render('default/form/contact.html.twig', array(
                   'form' => $form->createView()
           ));
    }
    
}
