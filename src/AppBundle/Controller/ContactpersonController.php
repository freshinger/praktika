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

class ContactpersonController extends Controller
{														/* Ansprechpartner Funktionen */
	/** Legt einen neuen Ansprechpartner für ausgewählte Firma an
    * @Route("/create/contact/for/{id}", name="formcontact")
    * @Security("has_role('ROLE_USER')")
    */
    public function contactAction(Request $request, $id)
    {
        $ansprechpartner = new Ansprechpartner();
        $form = $this->createForm('AppBundle\Form\AnsprechpartnerType', $ansprechpartner);
		
        $form->handleRequest($request);
			
        if($form->isSubmitted() && $form->isValid())
		{
            $firma = $this->getDoctrine()
							->getRepository('AppBundle:Firma')
							->find($id);
            $ansprechpartner->setFirma($firma);
			
            $em = $this->getDoctrine()->getManager();
            $em->persist($ansprechpartner);
            $em->flush();
            $name = $ansprechpartner->getPrename(). " " .$ansprechpartner->getSurname();
            $msg = "Der Ansprechpartner: ".$name." wurde erfolgreich in die Datenbank übertragen!";
            return $this->render('default/confirm.html.twig', array(
                'message' => $msg
            ));
        }
		
        return $this->render('default/form/contact.html.twig', array(
                'form' => $form->createView()
        ));
    }
	
	/** Einen Ansprechpartner aus der Datenbank mit Editierfunktion anzeigen
     * @Route("/edit/contact/{id}", name="editcontact")
     * @Security("has_role('ROLE_USER')")
     */
    public function editContactAction(Request $request, $id)
    {
        $ansprechpartner = $this->getDoctrine()
								->getRepository('AppBundle:Ansprechpartner')
								->find($id);
        $form = $this->createForm("AppBundle\Form\AnsprechpartnerType", $ansprechpartner);
		$form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
		{
            $em = $this->getDoctrine()->getManager();
            $em->persist($ansprechpartner);
            $em->flush();
            
            if ($request->request->has('delete'))
            {
                return $this->redirectToRoute('deletecontact', array(
						'id' => $id
                ));
            }
            
            return $this->redirectToRoute('editcontact', array(
					'id' => $id,
					'message' => "Daten wurden erfolgreich gespeichert!",
            ));
        }
        
        return $this->render('default/form/contact.html.twig', array(
				'ansprechpartner' => $ansprechpartner,
				'form' => $form->createView(),
				'message' => $request->query->get('message') 
        ));
    }
	
	/** Ansprechpartner aus der Datenbank löschen
     * @Route("/delete/contact/{id}", name="deletecontact")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteContactAction(Request $request, $id)
    {
        $ansprechpartner = $this->getDoctrine()
								->getRepository('AppBundle:Ansprechpartner')
								->find($id);
        $name = $ansprechpartner->getSurname();
		
        $em = $this->getDoctrine()->getManager();
        $em->remove($ansprechpartner);
        $em->flush();
		
        $msg = "Der Ansprechpartner: " . $name . " wurde erfolgreich gelöscht!";
        return $this->render('default/confirm.html.twig', array(
				'message' => $msg
        ));
    }
}