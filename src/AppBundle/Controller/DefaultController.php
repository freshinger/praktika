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
    * @Route("/create/success/firma/{name}/{id}", name="form_success")
    */

    public function successfirmaAction($name, $id)
    {
           return $this->render('default/form/firma_success.html.twig', array(
                   'name' => $name,
                   'id' => $id
			));
    }

    /**
    * @Route("/create/firma", name="formfirma")
    */
    public function firmaAction(Request $request)
    {
           $firma = new Firma();
           $form = $this->createForm('AppBundle\Form\FirmaType', $firma);

           $form->handleRequest($request);

           if($form->isSubmitted() && $form->isValid()){
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
    
    /**
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
    
    /**
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
        
        // Teilnehmer in die Datenbank aufnehmen
        if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			
			//$factory = $this->get('security.encoder_factory');
			
			//$encoder = $factory->getEncoder($user);
			//$password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
			//$user->setPassword($password);
			
            $em->persist($firma);
            $em->flush();
            
            //$request = $this->get('request');
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
    
    /**
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
    
    /**
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
    * @Route("/create/success/contact/{name}", name="contact_success")
    */
    public function successContactAction($name)
    {

           return $this->render('default/form/contact_success.html.twig', array(
                   'name' => $name
           ));
    }

    /**
    * @Route("/create/contact/for/{id}", name="formcontact")
    */
    public function contactAction(Request $request, $id)
    {
           $ansprechpartner = new Ansprechpartner();
           $form = $this->createForm('AppBundle\Form\AnsprechpartnerType', $ansprechpartner);

           $form->handleRequest($request);

           if($form->isSubmitted() && $form->isValid()){
                   $firma = $this->getDoctrine()
                           ->getRepository('AppBundle:Firma')
                           ->find($id);
                   $ansprechpartner->setFirma($firma);
                   $em = $this->getDoctrine()->getManager();
                   $em->persist($ansprechpartner);
                   $em->flush();

                   return $this->redirectToRoute('contact_success', array(
                           'name' => $ansprechpartner->getPrename(). " " .
                                $ansprechpartner->getSurname()
                   ));
           }

           return $this->render('default/form/contact.html.twig', array(
                   'form' => $form->createView()
           ));
    }
    
    /**
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
        
        if ($form->isSubmitted() && $form->isValid()) {
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
