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

class CompanyController extends Controller
{														/* Firma Funktionen */
	
	/** Neue Firma in die Datenbank aufnehmen
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
	
	/** Weiterleitung nach dem Anlegen einer neuen Firma
    * @Route("/create/success/firma/{name}/{id}", name="form_success")
    */
    public function successfirmaAction($name, $id)
    {
        return $this->render('default/form/firma_success.html.twig', array(
                'name' => $name,
                'id' => $id
		));
    }
	
	/** Liste aller eingetragenen Firmen anzeigen
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
	
	/** Einen Datensatz aus der Firmentabelle mit Editierfunktion anzeigen
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
        
        if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
            $em->persist($firma);
            $em->flush();
			
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
	
	/** Firmeneintrag aus der Datenbank löschen, Ansprechpartner zu dieser Firma werden ebenfalls gelöscht (cascade)
     * @Route("/delete/firma/{id}", name="deletefirma")
     * @Security("has_role('ROLE_USER')")
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
	
														/* Ansprechpartner Funktionen */
	
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
	
	/**  Legt einen neuen Ansprechpartner für ausgewählte Firma an
    * @Route("/create/contact/for/{id}", name="formcontact")
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
			
            return $this->redirectToRoute('contact_success', array(
					'name' => $ansprechpartner->getPrename(). " " .$ansprechpartner->getSurname()
            ));
        }
		
        return $this->render('default/form/contact.html.twig', array(
                'form' => $form->createView()
        ));
    }
	
	/** Ansprechpartner aus der Datenbank löschen
     * @Route("/delete/contact/{id}", name="deletecontact")
     * @Security("has_role('ROLE_USER')")
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