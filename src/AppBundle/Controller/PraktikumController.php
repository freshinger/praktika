<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\Praktikum;
use AppBundle\Form\PraktikumType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class PraktikumController extends Controller
{														/* Praktikum Funktionen */
	
	/** Einen neuen Praktikumseintrag zwischen Firma und Teilnehmer eintragen
    * @Route("/create/praktikum", name="formpraktikum")
    * @Security("has_role('ROLE_USER')")
    */
    public function praktikumAction(Request $request)
    {
        $praktikum = new Praktikum();
        $form = $this->createForm('AppBundle\Form\PraktikumType', $praktikum);
		
        $form->handleRequest($request);
		
        if($form->isSubmitted() && $form->isValid())
	{
            try {
                $this->save($praktikum);
            } catch(UniqueConstraintViolationException $e) {
                $msg = "Nur ein Praktikum pro User erlaubt!";
                return $this->render('default/confirm.html.twig', array(
                                    'message' => $msg
                ));
            }
            $msg = "Das Praktikum wurde erfolgreich gelistet!";
            return $this->render('default/confirm.html.twig', array(
                    'message' => $msg
            ));
        }
			
        return $this->render('default/form/praktikum.html.twig', array(
                'form' => $form->createView()
        ));
    }
	
	/** Eine Liste aller Praktikas anzeigen
    * @Route("/show/praktikum", name="showpraktikum")
    * @Security("has_role('ROLE_USER')")
    */
    public function showPraktikumAction(Request $request)
    {
        // User können nur ihr eigenes Praktikum angucken
        if ($this->get('security.context')->isGranted('ROLE_STAFF') === false) {
            $uid = $this->get('security.token_storage')->getToken()
                    ->getUser()->getId();
            $user = $this->getDoctrine()
                        ->getRepository('AppBundle:User')
                        ->find($uid);  
            $praktika = $this->getDoctrine()
                           ->getRepository('AppBundle:Praktikum')
                           ->findByUser($user);
        } else {
            $praktika = $this->getDoctrine()
                           ->getRepository('AppBundle:Praktikum')
                           ->findAll();
        }
           

           return $this->render('default/praktika.html.twig', array(
                   'praktika' => $praktika
           ));
    }
	
	/** Einen Praktikumseintrag mit Editierfunktion anzeigen
     * @Route("/edit/praktikum/{id}", name="editpraktikum")
     * @Security("has_role('ROLE_USER')")
     */
    public function editPraktikumAction(Request $request, $id)
    {
        $praktikum = $this->getDoctrine()
                           ->getRepository('AppBundle:Praktikum')
                           ->find($id);
        $form = $this->createForm("AppBundle\Form\PraktikumType", $praktikum);
	$form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->request->has('delete'))
            {
                return $this->redirectToRoute('deletepraktikum', array(
						'id' => $id
                ));
            }
            try {
            $this->save($praktikum);
            } catch(UniqueConstraintViolationException $e) {
                $msg = "Nur ein Praktikum pro User erlaubt!";
                return $this->render('default/confirm.html.twig', array(
                                    'message' => $msg
                ));
            }
            return $this->redirectToRoute('editpraktikum', array(
					'id' => $id,
					'message' => "Daten wurden erfolgreich gespeichert!",
			));
        }
	
        return $this->render('default/form/praktikum.html.twig', array(
				'praktikum' => $praktikum,
				'form' => $form->createView(),
				'message' => $request->query->get('message') 
        ));
    }
	
	/** Zeigt eine Liste aller zur Zeit aktiven Praktika an
     * @Route("/show/active", name="showactive")
     * @Security("has_role('ROLE_STAFF')")
     */
    public function showActiveAction(Request $request)
    {
		$em = $this->getDoctrine()->getManager();
		$query = $em->createQuery('SELECT p
								  FROM AppBundle:Praktikum p
								  WHERE p.startdatum <= :today
								  AND p.enddatum >= :today')
							->setParameter('today', new \DateTime());
		$praktika = $query->getResult();

        return $this->render('default/listactive.html.twig', array(
                'praktika' => $praktika
        ));
	}
	
	/** Löschen eines Praktikumeintrags aus der Datenbank
     * @Route("/delete/praktikum/{id}", name="deletepraktikum")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deletePraktikumAction(Request $request, $id)
    {
        $praktikum = $this->getDoctrine()
                           ->getRepository('AppBundle:Praktikum')
                           ->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($praktikum);
        $em->flush();
        $msg = "Das Praktikum wurde gelöscht!";
        return $this->render('default/confirm.html.twig', array(
				'message' => $msg
        ));
    }
    
    // Hilfsfunktion um daten in der datenbank abzuspeichern
    private function save($praktikum) {
        
        $em = $this->getDoctrine()->getManager();
        // User können nur ihr eigenes Praktikum ändern
        if ($this->get('security.context')->isGranted('ROLE_STAFF') === false) {
            $uid = $this->get('security.token_storage')->getToken()
                    ->getUser()->getId();
            $user = $this->getDoctrine()
                        ->getRepository('AppBundle:User')
                        ->find($uid);
            $praktikum->setUser($user);
        }
        $em->persist($praktikum);
        $em->flush();
        
    }
}