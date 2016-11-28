<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Entity\Kontakt;
use AppBundle\Form\KontaktType;
use AppBundle\Entity\Korrespondenz;
use AppBundle\Form\KorrespondenzType;

class ContactsController extends Controller
{														/* Kontakt Funktionen */
	
	/** Kontakt zu seinen eigenen persönlichen Kontakten hinzufügen
    * @Route("/create/relationship", name="createrelationship")
    * @Security("has_role('ROLE_USER')")
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
	
	/** Zeigt seine eigenen angelegten Kontakte an
    * @Route("/show/relationships", name="showrelationships")
    * @Security("has_role('ROLE_USER')")
    */
    public function showRelationshipsAction(Request $request)
    {
		$uid = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $user = $this->getDoctrine()
                        ->getRepository('AppBundle:User')
                        ->find($uid);
        $relationships = $this->getDoctrine()
                        ->getRepository('AppBundle:Kontakt')
                        ->findByUser($user);
		
        return $this->render('default/relationships.html.twig', array(
                'kontakte' => $relationships
        ));
    }
	
	/**
    * @Route("/show/relationships/{id}", name="showrelationshipsbyid")
    * @Security("has_role('ROLE_STAFF')")
    */
    public function showRelationshipsByIdAction(Request $request, $id)
    {
           
        $user = $this->getDoctrine()
						->getRepository('AppBundle:User')
                        ->find($id);
        $relationships = $this->getDoctrine()
                        ->getRepository('AppBundle:Kontakt')
                        ->findByUser($user);
		
        return $this->render('default/relationships.html.twig', array(
                'kontakte' => $relationships
        ));
    }
	
	/** Löscht einen angelegten Ansprechpartner aus der Liste der Kontakte
     * @Route("/delete/relationship/{id}", name="deleterelationship")
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteRelationshipAction(Request $request, $id)
    {
        $relationship = $this->getDoctrine()
                           ->getRepository('AppBundle:Kontakt')
                           ->find($id);
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($relationship);
        $em->flush();
        $msg = "Der Kontakt wurde erfolgreich gelöscht!";
        return $this->render('default/confirm.html.twig', array(
            'message' => $msg
        ));
    }
	
	/** Legt eine neue Korrespondenz an um Informationen einer Kontaktaufnahme festzuhalten
    * @Route("/create/correspondence/{kontakt_id}", name="createcorrespondence")
    * @Security("has_role('ROLE_USER')")
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
	
	/**
    * @Route("/show/correspondence/{kontakt_id}", name="showcorrespondence")
    * @Security("has_role('ROLE_USER')")
    */
    public function showKorrespondenzAction(Request $request, $kontakt_id)
    {
        $kontakt = $this->getDoctrine()
                        ->getRepository('AppBundle:Kontakt')
                        ->findOneById($kontakt_id);
        $korrespondenzen = $this->getDoctrine()
                        ->getRepository('AppBundle:Korrespondenz')
                        ->findByKontakt($kontakt);
		
        return $this->render('default/correspondence.html.twig', array(
                'kontakt' => $kontakt,
                'korrespondenzen' => $korrespondenzen
        ));
    }
    
    /**
     * @Route("/delete/correspondence/{id}", name="deletecorrespondence")
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteKorrespondenzAction(Request $request, $id)
    {
        $correspondence = $this->getDoctrine()
                        ->getRepository('AppBundle:Korrespondenz')
                        ->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($correspondence);
        $em->flush();
        $msg = "Die Unterhaltung wurde gelöscht!";
        return $this->render('default/confirm.html.twig', array(
				'message' => $msg
        ));
    }
}