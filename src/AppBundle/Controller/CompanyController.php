<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Firma;

/* 
 * Der Firmencontroller kümmert sich um die CRUD (Create, Read, Update, Delete)
 * Funktionalität der Firmenobjekte 
 */

class CompanyController extends Controller { 

    /** 
     * Create Company
     * Neue Firma in die Datenbank aufnehmen
     * 
     * @Route("/create/firma", name="formfirma")
     * @Security("has_role('ROLE_USER')")
     */
    public function firmaAction(Request $request) {
        $firma = new Firma();
        $form = $this->createForm('AppBundle\Form\FirmaType', $firma);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $firma = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($firma);
            $em->flush();

            return $this->redirectToRoute('formcontact', array(
                'id' => $firma->getId()
            ));
        }

        return $this->render('default/form/firma.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /** 
     * Read all Companies
     * Liste aller eingetragenen Firmen anzeigen
     * 
     * @Route("/show/firma", name="showfirma")
     * @Security("has_role('ROLE_USER')")
     */
    public function showFirmaAction(Request $request) {
        $firmen = $this->getDoctrine()
            ->getRepository('AppBundle:Firma')
            ->findAll();

        return $this->render('default/firmen.html.twig', array(
            'firmen' => $firmen
        ));
    }

    /** 
     * Update Company
     * Eine Firma aus der Datenbank mit Editierfunktion anzeigen
     * 
     * @Route("/show/firma/{id}", name="editfirma")
     * @Security("has_role('ROLE_STAFF')")
     */
    public function editFirmaAction(Request $request, $id) {
        $firma = $this->getDoctrine()
            ->getRepository('AppBundle:Firma')
            ->find($id);
        $form = $this->createForm("AppBundle\Form\FirmaType", $firma);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($firma);
            $em->flush();

            if ($request->request->has('delete')) {
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
     * Delete Company
     * Firmeneintrag aus der Datenbank löschen.
     * Dabei werden Ansprechpartner, Informationen und Praktikas in Verbindung 
     * zu dieser Firma ebenfalls gelöscht.
     * 
     * @Route("/delete/firma/{id}", name="deletefirma")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteFirmaAction(Request $request, $id) {
        $firma = $this->getDoctrine()
            ->getRepository('AppBundle:Firma')
            ->find($id);
        $name = $firma->getName();
        $ansprechpartner = $this->getDoctrine()
            ->getRepository('AppBundle:Ansprechpartner')
            ->findByFirma($firma);

        $praktika = $this->getDoctrine()
            ->getRepository('AppBundle:Praktikum')
            ->findByFirma($firma);

        $informationdata = $this->getDoctrine()
            ->getRepository('AppBundle:Information')
            ->findByFirma($firma);

        $em = $this->getDoctrine()->getManager();
        foreach ($praktika AS $praktikum) {
            $em->remove($praktikum);
        }
        foreach ($informationdata AS $information) {
            $em->remove($information);
        }
        $em->remove($firma);
        $em->flush();

        $msg = "Die Firma: " . $name . " wurde erfolgreich gelöscht!";
        return $this->render('default/confirm.html.twig', array(
            'message' => $msg
        ));
    }
}
