<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Entity\Information;
use AppBundle\Form\InformationType;

class InformationController extends Controller
{														/* Firmeninformations Funktionen */
	
	/** Legt eine neue Information für ausgewählte Firma an
    * @Route("/create/information/for/{id}", name="forminformation")
    * @Security("has_role('ROLE_USER')")
    */
    public function informationAction(Request $request, $id)
    {
        $information = new Information();
        $form = $this->createForm('AppBundle\Form\InformationType', $information);
		
        $form->handleRequest($request);
			
        if($form->isSubmitted() && $form->isValid())
		{
			$uid = $this->get('security.token_storage')->getToken()
                    ->getUser()->getId();
            $user = $this->getDoctrine()
                        ->getRepository('AppBundle:User')
                        ->find($uid);
            $information->setUser($user);
            $firma = $this->getDoctrine()
							->getRepository('AppBundle:Firma')
							->find($id);
            $information->setFirma($firma);
			
            $em = $this->getDoctrine()->getManager();
            $em->persist($information);
            $em->flush();
            $name = $firma->getName();
            $msg = "Die Information für die Firma ".$name." wurde erfolgreich eingetragen!";
            return $this->render('default/confirm.html.twig', array(
                'message' => $msg
            ));
        }
		
        return $this->render('default/form/information.html.twig', array(
                'form' => $form->createView()
        ));
    }
	
	/** Eine Firmeninformation aus der Datenbank mit Editierfunktion anzeigen
     * @Route("/edit/information/{id}", name="editinformation")
     * @Security("has_role('ROLE_STAFF')")
     */
    public function editInformationAction(Request $request, $id)
    {
        $information = $this->getDoctrine()
								->getRepository('AppBundle:Information')
								->find($id);
        $form = $this->createForm("AppBundle\Form\InformationType", $information);
		$form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
		{
            $em = $this->getDoctrine()->getManager();
            $em->persist($information);
            $em->flush();
            
            if ($request->request->has('delete'))
            {
                return $this->redirectToRoute('deleteinformation', array(
						'id' => $id
                ));
            }
            
            return $this->redirectToRoute('editinformation', array(
					'id' => $id,
					'message' => "Daten wurden erfolgreich gespeichert!",
            ));
        }
        
        return $this->render('default/form/information.html.twig', array(
				'information' => $information,
				'form' => $form->createView(),
				'message' => $request->query->get('message')
        ));
    }
	
	/** Firmeninformation aus der Datenbank löschen
     * @Route("/delete/information/{id}", name="deleteinformation")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteInformationAction(Request $request, $id)
    {
        $information = $this->getDoctrine()
								->getRepository('AppBundle:Information')
								->find($id);
        $title = $information->getTitle();
		
        $em = $this->getDoctrine()->getManager();
        $em->remove($information);
        $em->flush();
		
        $msg = "Die Imfornation: " . $title . " wurde erfolgreich gelöscht!";
        return $this->render('default/confirm.html.twig', array(
				'message' => $msg
        ));
    }
}