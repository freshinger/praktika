<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Entity\Praktikum;
use AppBundle\Form\PraktikumType;

class AdminController extends Controller
{		
    /**
     * @Route("/show/active", name="showactive")
     * * @Security("has_role('ROLE_STAFF')")
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
		
		// Querybuildercode, funktioniert noch nicht
		/*$praktika = $this->getDoctrine()->getRepository('AppBundle:Praktikum');
        $query = $praktika->createQueryBuilder('p')
									//->where('p.startdatum < :today')
									//->andWhere('p.enddatum > :today')
									//->setParameter('today', 'CURRENT_DATE()')
									->orderBy('p.startdatum', 'ASC')
									->getQuery();*/
		
           return $this->render('default/listactive.html.twig', array(
                   'praktika' => $praktika
           ));
	}
	
	/**
    * @Route("/show/users", name="showusers")
    * * @Security("has_role('ROLE_STAFF')")
    */
    public function showUsersAction(Request $request)
    {
           $users = $this->getDoctrine()
                           ->getRepository('AppBundle:User')
                           ->findAll();
			
           return $this->render('default/listusers.html.twig', array(
                   'users' => $users
           ));
    }
	
	/**
     * @Route("/show/user/{id}", name="showuser")
     * * @Security("has_role('ROLE_STAFF')")
     */
    public function showUserAction(Request $request, $id)
    {
        
        $user = $this->getDoctrine()
                           ->getRepository('AppBundle:User')
                           ->find($id);
        $form = $this->createForm("AppBundle\Form\ProfilType", $user);
		$form->handleRequest($request);
        
        // Teilnehmer in die Datenbank aufnehmen
        if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			
			//$factory = $this->get('security.encoder_factory');
			
			//$encoder = $factory->getEncoder($user);
			//$password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
			//$user->setPassword($password);
			
            $em->persist($user);
            $em->flush();
            
            //$request = $this->get('request');
            if ($request->request->has('delete'))
            {
                return $this->redirectToRoute('showusers', array(
                    'id' => $id,
					'message' => "Nutzer wurde erfolgreich gelöscht!",
                ));
            }
            
            return $this->redirectToRoute('showuser', array(
                'id' => $id,
                'message' => "Daten wurden erfolgreich gespeichert!",
            ));
            
        }
        
        return $this->render('default/showuser.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
            'message' => $request->query->get('message') 
        ));
	}
}