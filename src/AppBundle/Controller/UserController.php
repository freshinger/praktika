<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\Firma;
use AppBundle\Form\FirmaType;
use AppBundle\Entity\Ansprechpartner;
use AppBundle\Form\AnsprechpartnerType;

class UserController extends Controller
{		
    /**
    * @Route("/show/firma", name="showfirma")
    */
    public function showFirmaAction(Request $request)
    {
           $firmen = $this->getDoctrine()
                           ->getRepository('AppBundle:Firma')
                           ->findAll();
		
           return $this->render('default/firmen.html.twig', array(
                   'firmen' => $firmen
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
		
        if($form->isSubmitted() && $form->isValid())
		{
            $firma = $form->getData();
			
            $em = $this->getDoctrine()->getManager();
            $em->persist($firma);
            $em->flush();
			
            return $this->redirectToRoute('form_success', array(
										'name' => $firma->getName(),
										'id' => $firma->getId()
				));
        }
		
			return $this->render('default/form/firma.html.twig', array(
							'form' => $form->createView()
			));
    }
	
	/**
     * @Route("/edit/firma/{id}", name="editfirma")
     * @Security("has_role('ROLE_USER')")
     */
    public function editFirmaAction(Request $request, $id)
    {
        
        $firma = $this->getDoctrine()
                           ->getRepository('AppBundle:Firma')
                           ->find($id);
        $form = $this->createForm("AppBundle\Form\FirmaType", $firma);
	$form->handleRequest($request);
        
        // Teilnehmer in die Datenbank aufnehmen
        if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			
			//$factory = $this->get('security.encoder_factory');
			
			//$encoder = $factory->getEncoder($user);
			//$password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
			//$user->setPassword($password);
			
            $em->persist($firma);
            $em->flush();
            
            //$request = $this->get('request');
            if ($request->request->has('delete'))
            {
                return $this->redirectToRoute('deletefirma', array(
                    'id' => $id
                ));
            }
            
            return $this->redirectToRoute('editfirma', array(
                'id' => $id,
                'message' => "Daten wurden erfolgreich gespeichert!",
            ));
            
        }
        
        return $this->render('edit/firma.html.twig', array(
            'firma' => $firma,
            'form' => $form->createView(),
            'message' => $request->query->get('message') 
        ));
    }
	
	/**
     * @Route("/delete/firma/{id}", name="deletefirma")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteFirmaAction(Request $request, $id)
    {
        $firma = $this->getDoctrine()
                           ->getRepository('AppBundle:Firma')
                           ->find($id);
        $name = $firma->getName();
        $em = $this->getDoctrine()->getManager();
        $em->remove($firma);
        $em->flush();
        $msg = "Die Firma: " . $name . " wurde erfolgreich gelöscht!";
        return $this->render('default/confirm.html.twig', array(
            'message' => $msg
        ));
    }
}