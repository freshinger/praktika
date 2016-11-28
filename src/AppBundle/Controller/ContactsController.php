<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\Firma;
use AppBundle\Form\FirmaType;
use AppBundle\Entity\Praktikum;

class ContactsController extends Controller
{														/* Firma Funktionen */
	/** 
    * @Route("/create/relationship", name="createrelationship")
    */
    public function relationshipAction(Request $request)
    {
        $uid = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $user = $this->getDoctrine()
                    ->getRepository('AppBundle:User')
                    ->find($uid);
        $relationship = new Kontakt();
        $form = $this->createForm('AppBundle\Form\KontaktType', $relationship);
		
        $form->handleRequest($request);
		
        if($form->isSubmitted() && $form->isValid()){
            $relationship = $form->getData();
            $relationship->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($relationship);
            $em->flush();
			
            $msg = "Der Kontakt wurde erfolgreich gelistet!";
            return $this->render('default/confirm.html.twig', array(
                    'message' => $msg
            ));
        }
		
        return $this->render('default/form/relationship.html.twig', array(
                'form' => $form->createView()
        ));
	}
	
	/** Legt eine neue Korrespondenz an um Informationen einer Kontaktaufnahme festzuhalten
    * @Route("/create/correspondence/{kontakt_id}", name="createcorrespondence")
    */
    public function correspondenceAction(Request $request, $kontakt_id)
    {
        $kontakt = $this->getDoctrine()
                        ->getRepository('AppBundle:Kontakt')
                        ->find($kontakt_id);
        $correspondence = new Korrespondenz();
        $form = $this->createForm('AppBundle\Form\KorrespondenzType', $correspondence);
		
        $form->handleRequest($request);
		
        if($form->isSubmitted() && $form->isValid()){
            $correspondence = $form->getData();
            $kontakt->addKorrespondenz($correspondence);
            $correspondence->setKontakt($kontakt);
            $em = $this->getDoctrine()->getManager();
            $em->persist($correspondence);
            $em->persist($kontakt);
            $em->flush();
			
            $msg = "Die Korrespondenz wurde erfolgreich erstellt!";
            return $this->render('default/confirm.html.twig', array(
                    'message' => $msg
            ));
        }
		
        return $this->render('default/form/correspondence.html.twig', array(
                'form' => $form->createView()
        ));
    }
}