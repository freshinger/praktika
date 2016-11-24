<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Entity\Firma;
use AppBundle\Form\FirmaType;
use AppBundle\Entity\Praktikum;
use AppBundle\Form\PraktikumType;
use AppBundle\Entity\Ansprechpartner;
use AppBundle\Form\AnsprechpartnerType;
use AppBundle\Entity\Kontakt;
use AppBundle\Form\KontaktType;
use AppBundle\Entity\Korrespondenz;
use AppBundle\Form\KonrrespondenzType;

//@TODO: Klasse aufspalten in unterklassen, da zu groß

class DefaultController extends Controller
{	
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
		$defaultData = array('message' => 'Type your message here');
		$form = $this->createFormBuilder($defaultData)
				->add('searchbar', "Symfony\Component\Form\Extension\Core\Type\TextType")
				->getForm();
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			$value = $form->getData();
			$em = $this->getDoctrine()->getManager();
			$query = $em->createQuery(
				"SELECT f
				FROM AppBundle:Firma f
                                JOIN AppBundle:Ansprechpartner a
				WHERE f.name LIKE :value
				OR f.website LIKE :value
				OR (a.phone LIKE :value AND IDENTITY(a.firma, 'id') = f.id)
				ORDER BY f.name ASC"
			)->setParameter('value', '%'.$value['searchbar'].'%');
			$firma = $query->getResult();
			
			// Querybuildercode, funktioniert nicht
				/*$repository = $this->getDoctrine()->getRepository('AppBundle:Firma');
				$query = $repository->createQueryBuilder('f')
									->where('f.name LIKE :value')
									->orWhere('f.website LIKE :value')
									//->orWhere('a.phone LIKE :value')
									//->andWhere('a.company_id = f.id')
									->setParameter('value', '%'.$value['searchbar'].'%')
									->orderBy('f.name', 'ASC')
									->getQuery();*/
				$firma = $query->getResult();
				if (!empty($firma)){
					return $this->render('default/index.html.twig', array('firmen' => $firma, 'form' => $form->createView()));
				}
		}
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
			'form' => $form->createView()
        ));
    }
    
    /**
     * @Route("/edit/user", name="edituser")
     * @Security("has_role('ROLE_USER')")
     */
    public function editUserAction(Request $request)
    {
        $uid = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $user = $this->getDoctrine()
                           ->getRepository('AppBundle:User')
                           ->find($uid);
        $form = $this->createForm("AppBundle\Form\ProfilType", $user);
		$form->handleRequest($request);
        
        // Teilnehmer in die Datenbank aufnehmen
        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->request->has('delete'))
            {
                return $this->redirectToRoute('deleteuser');
            }
			$em = $this->getDoctrine()->getManager();
			
			//$factory = $this->get('security.encoder_factory');
			
			//$encoder = $factory->getEncoder($user);
			//$password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
			//$user->setPassword($password);
			
            $em->persist($user);
            $em->flush();
        }
        
        return $this->render('edit/user.html.twig', array(
            'user' => $user,
            'form' => $form->createView()
        ));
    }
    
    /**
	 * @Route("/create/user", name="registration")
	 */
	public function userAction(Request $request){
		$user = new User();
		$form = $this->createForm("AppBundle\Form\UserType", $user);
		$form->handleRequest($request);
        
        // Teilnehmer in die Datenbank aufnehmen
        if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			
			$factory = $this->get('security.encoder_factory');
			
			$encoder = $factory->getEncoder($user);
			$password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
			$user->setPassword($password);
			
            $em->persist($user);
            $em->flush();
            
            return $this->redirectToRoute('reg_success',array(
				'id' => $user->getId(),
				'email' => $user->getEmail()));
        }
		return $this->render('default/form/registration.html.twig', array(
			'form' => $form->createView()));
	}
	
	/**
    * @Route("/create/success/user/{id}/{email}", name="reg_success")
    */
    public function regsuccessAction($id, $email){
           return $this->render('default/form/registration_success.html.twig', array(
					'id' => $id,
					'email' => $email));
    }
	
	/**
     * @Route("/admin")
     */
    public function adminAction()
    {
        return new Response('<html><body>Admin page!</body></html>');
    }
    
    /**
    * @Route("/show/correspondence/{kontakt_id}", name="showcorrespondence")
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
    * @Route("/show/relationships", name="showrelationships")
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
     * @Route("/delete/user", name="deleteuser")
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteUserAction(Request $request)
    {
        $uid = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $user = $this->getDoctrine()
                           ->getRepository('AppBundle:User')
                           ->find($uid);
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        
        return $this->redirectToRoute('logout');
    }
    
    /**
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
    
    /**
     * @Route("/delete/praktikum/{id}", name="deletepraktikum")
     * @Security("has_role('ROLE_USER')")
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
    
    /**
    * @Route("/create/success/contact/{name}", name="contact_success")
    */
    public function successContactAction($name)
    {
           return $this->render('default/form/contact_success.html.twig', array(
                   'name' => $name
           ));
	}
    
    /**
    * @Route("/show/praktikum", name="showpraktikum")
    */
    public function showPraktikumAction(Request $request)
    {
           $praktika = $this->getDoctrine()
                           ->getRepository('AppBundle:Praktikum')
                           ->findAll();

           return $this->render('default/praktika.html.twig', array(
                   'praktika' => $praktika
           ));
    }
    
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

    /**
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
    
    /**
    * @Route("/create/praktikum", name="formpraktikum")
    */
    public function praktikumAction(Request $request)
    {
           $praktikum = new Praktikum();
           $form = $this->createForm('AppBundle\Form\PraktikumType', $praktikum);

           $form->handleRequest($request);

           if($form->isSubmitted() && $form->isValid()){
                $praktikum = $form->getData();

                $em = $this->getDoctrine()->getManager();
                $em->persist($praktikum);
                $em->flush();

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
            $em = $this->getDoctrine()->getManager();
		
            $em->persist($praktikum);
            $em->flush();
            
            if ($request->request->has('delete'))
            {
                return $this->redirectToRoute('deletepraktikum', array(
                    'id' => $id
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
}
