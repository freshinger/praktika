<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Praktikum;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

/*
 * Praktikums Controller
 * Hier werden die typischen CRUD (Create, Read, Update, Delete) Funktionen
 * definiert. Zusätzlich gibt es varianten bei Read und Update:
 * Read all liefert alle Praktikas in der Datenbank
 * Read active liefert nur zur Zeit stattfindende Praktikas
 * Update und Update only oneself unterscheiden sich dadurch, dass man bei 
 * letzterem nur sein eigenes Praktikum updaten kann. Das ist dadurch notwendig,
 * da der Benutzer sonst durch manipulation der URL auch Praktikas anderer 
 * Nutzer ändern konnte.
 */

class PraktikumController extends Controller {

    /**
     * Create
     * Einen neuen Praktikumseintrag zwischen Firma und Teilnehmer eintragen
     * 
     * @Route("/create/praktikum", name="formpraktikum")
     * @Security("has_role('ROLE_USER')")
     */
    public function praktikumAction(Request $request) {
        $praktikum = new Praktikum();
        $form = $this->createForm('AppBundle\Form\PraktikumType', $praktikum);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->save($praktikum);
            } catch (UniqueConstraintViolationException $e) {
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

    /**
     * Read all
     * Eine Liste aller Praktikas anzeigen
     * 
     * @Route("/show/praktikum", name="showpraktikum")
     * @Security("has_role('ROLE_USER')")
     */
    public function showPraktikumAction(Request $request) {
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
            /* $praktika = $this->getDoctrine()
              ->getRepository('AppBundle:Praktikum')
              ->findAll(); */
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                "SELECT p
                FROM AppBundle:Praktikum p, AppBundle:User u
                WHERE IDENTITY(p.user, 'id') = (u.id)
                ORDER BY u.username ASC"
            );
            $praktika = $query->getResult();
        }
        return $this->render('default/praktika.html.twig', array(
            'praktika' => $praktika
        ));
    }

    /**
     * Read active
     * Zeigt eine Liste aller zur Zeit aktiven Praktika an
     * 
     * @Route("/show/active", name="showactive")
     * @Security("has_role('ROLE_STAFF')")
     */
    public function showActiveAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            "SELECT p
            FROM AppBundle:Praktikum p, AppBundle:User u
            WHERE p.startdatum <= :today
            AND p.enddatum >= :today
            AND IDENTITY(p.user, 'id') = (u.id)
            ORDER BY u.username ASC")
            ->setParameter('today', new \DateTime());
        $praktika = $query->getResult();
        return $this->render('default/listactive.html.twig', array(
            'praktika' => $praktika
        ));
    }

    /**
     * Update
     * Einen Praktikumseintrag mit Editierfunktion anzeigen
     * 
     * @Route("/edit/praktikum/{id}", name="editpraktikum")
     * @Security("has_role('ROLE_STAFF')")
     */
    public function editPraktikumAction(Request $request, $id) {
        $praktikum = $this->getDoctrine()
            ->getRepository('AppBundle:Praktikum')
            ->find($id);
        $form = $this->createForm("AppBundle\Form\PraktikumType", $praktikum);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->request->has('delete')) {
                return $this->redirectToRoute('deletepraktikum', array(
                    'id' => $id
                ));
            }
            try {
                $this->save($praktikum);
            } catch (UniqueConstraintViolationException $e) {
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

    /**
     * Update only oneself
     * Eigenen Praktikumseintrag mit Editierfunktion anzeigen
     * 
     * @Route("/edit/praktikum", name="editmypraktikum")
     * @Security("has_role('ROLE_USER')")
     */
    public function editMyPraktikumAction(Request $request) {
        $uid = $this->get('security.token_storage')->getToken()
            ->getUser()->getId();
        $praktikum = $this->getDoctrine()
            ->getRepository('AppBundle:Praktikum')
            ->findOneByUser($uid);
        $form = $this->createForm("AppBundle\Form\PraktikumType", $praktikum);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->request->has('delete')) {
                return $this->redirectToRoute('deletepraktikum', array(
                    'id' => $id
                ));
            }
            try {
                $this->save($praktikum);
            } catch (UniqueConstraintViolationException $e) {
                $msg = "Nur ein Praktikum pro User erlaubt!";
                return $this->render('default/confirm.html.twig', array(
                    'message' => $msg
                ));
            }
            return $this->redirectToRoute('editmypraktikum', array(
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
    
    /**
     * Delete
     * Löschen eines Praktikumeintrags aus der Datenbank
     * 
     * @Route("/delete/praktikum/{id}", name="deletepraktikum")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deletePraktikumAction(Request $request, $id) {
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

    // Hilfsfunktion um Daten in der Datenbank abzuspeichern
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
