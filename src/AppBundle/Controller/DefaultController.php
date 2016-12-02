<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/*
 * Der DefaultController bildet den Einstiegspunkt in die Webapplikation der
 * Praktikumsfirmenverwaltung. 
 * Hier wird bestimmt was in der index.html passiert.
 * Im Wesentlichen werden Daten die der Benutzer im Frontend, dem View, eingibt
 * an die Datenbank weitergeleitet. In diesem Fall für die Suchfuntkion nach
 * Firmen. 
 */

class DefaultController extends Controller {

    /**
     * Hauptfunktion die sich um das anzeigen der Startseite kümmert und eine 
     * Suchfuntion implementiert.
     * 
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request) {
        $defaultData = array('message' => 'Type your message here');
        $form = $this->createFormBuilder($defaultData)
            ->add('searchbar',
                "Symfony\Component\Form\Extension\Core\Type\TextType")
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $value = $form->getData();
            $em = $this->getDoctrine()->getManager();
            //wegen dem JOIN darf die Ansprechpartner Tabelle nicht leer sein
            $query = $em->createQuery(
                "SELECT f
                FROM AppBundle:Firma f
                JOIN AppBundle:Ansprechpartner a
                WHERE f.name LIKE :value
                OR f.website LIKE :value
                OR (a.phone LIKE :value AND IDENTITY(a.firma, 'id') = f.id)
                ORDER BY f.name ASC"
                )->setParameter('value', '%' . $value['searchbar'] . '%');
            $firma = $query->getResult();

            if (!empty($firma)) {
                return $this->render('default/index.html.twig', 
                    array('firmen' => $firma, 'form' => $form->createView()));
            }
        }
        return $this->render('default/index.html.twig', array(
                'base_dir' => realpath(
                    $this->container->getParameter('kernel.root_dir') . '/..') 
                    . DIRECTORY_SEPARATOR,
                'form' => $form->createView()
        ));
    }

}
