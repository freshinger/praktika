<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Form\ProfilType;

class UserController extends Controller { /* User Funktionen */

    /** Neuen Benutzer mit Username, Email und Password anlegen
     * @Route("/create/user", name="registration")
     */
    public function userAction(Request $request) {
        $user = new User();
        $form = $this->createForm("AppBundle\Form\UserType", $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $factory = $this->get('security.encoder_factory');

            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);

            $em->persist($user);
            $em->flush();
            $msg = "Account für " . $user->getEmail() . " wurde erfolgreich registriert!";
            return $this->render('default/confirm.html.twig', array(
                        'message' => $msg
            ));
        }

        return $this->render('default/form/registration.html.twig', array(
                    'form' => $form->createView()
        ));
    }

    /** Wenn eingeloggt, Seine eigenen Benutzerdaten mit Editierfunktion anzeigen
     * @Route("/edit/user", name="edituser")
     * @Security("has_role('ROLE_USER')")
     */
    public function editUserAction(Request $request) {
        $uid = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $user = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->find($uid);
        $form = $this->createForm("AppBundle\Form\ProfilType", $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->request->has('delete')) {
                return $this->redirectToRoute('deleteuser');
            }
            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();
        }

        return $this->render('edit/user.html.twig', array(
                    'user' => $user,
                    'form' => $form->createView()
        ));
    }

    /** Zeigt eine Liste aller registrierten Benutzer für die Mitarbeiter
     * @Route("/show/user", name="showusers")
     * @Security("has_role('ROLE_STAFF')")
     */
    public function showUsersAction(Request $request) {
        $users = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->findBy(array(), array('username'=>'asc'));

        return $this->render('default/listusers.html.twig', array(
                    'users' => $users
        ));
    }

    /** Zeigt einen Benutzer mit Editierfunktion für Mitarbeiter
     * @Route("/show/user/{id}", name="showuser")
     * @Security("has_role('ROLE_STAFF')")
     */
    public function showUserAction(Request $request, $id) {
        $user = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->find($id);
        $form = $this->createForm("AppBundle\Form\ProfilType", $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->request->has('delete')) {
                return $this->redirectToRoute('removeuser', array(
                            'id' => $id,
                            'message' => "Nutzer wurde erfolgreich gelöscht!",
                ));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            return $this->redirectToRoute('showuser', array(
                        'id' => $id,
                        'message' => "Daten wurden erfolgreich gespeichert!",
            ));
        }
        
        return $this->render('edit/user.html.twig', array(
                    'user' => $user,
                    'form' => $form->createView(),
                    'message' => $request->query->get('message')
        ));
    }
    
    /** Benutzer in der Datenbank suchen anhand von Username, Email oder Nachname
     * @Route("/search/user", name="searchuser")
     * @Security("has_role('ROLE_STAFF')")
     */
    public function searchAction(Request $request)
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
				"SELECT u
				FROM AppBundle:User u
				WHERE u.username LIKE :value
				OR u.surname LIKE :value
				OR u.email LIKE :value
				ORDER BY u.username ASC"
			)->setParameter('value', '%'.$value['searchbar'].'%');
			$user = $query->getResult();
			
			if (!empty($user)){
				return $this->render('default/searchuser.html.twig', array('users' => $user, 'form' => $form->createView()));
			}
		}
        return $this->render('default/searchuser.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
			'form' => $form->createView()
        ));
    }

    /** Seinen eigenen Benutzeraccount löschen
     * @Route("/delete/user", name="deleteuser")
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteUserAction(Request $request) {
        $uid = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $user = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->find($uid);
        $informationdata = $this->getDoctrine()
                ->getRepository('AppBundle:Information')
                ->findByUser($user);
        $praktika = $this->getDoctrine()
                ->getRepository('AppBundle:Praktikum')
                ->findByUser($user);
        $em = $this->getDoctrine()->getManager();
        foreach($informationdata as $information){
            $em->remove($information);
        }
        foreach ($praktika AS $praktikum){
            $em->remove($praktikum);
        }
        $em->remove($user);
        $em->flush();
        
        //clear out session before redirecting deleted user
        $this->get('security.context')->setToken(null);
        $this->get('request')->getSession()->invalidate();
        
        return $this->redirectToRoute('logout');
    }

    /** Benutzer aus der Datenbank löschen
     * @Route("/delete/user/{id}", name="removeuser")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function removeUserAction(Request $request, $id) {
        $user = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->find($id);
        $praktika = $this->getDoctrine()
                ->getRepository('AppBundle:Praktikum')
                ->findByUser($user);
        $informationdata = $this->getDoctrine()
                ->getRepository('AppBundle:Information')
                ->findByUser($user);
        $em = $this->getDoctrine()->getManager();
        foreach ($praktika AS $praktikum) {
            $em->remove($praktikum);
        }
        foreach ($informationdata AS $information) {
            $em->remove($information);
        }
        $em->remove($user);
        $em->flush();
        
        //clear out session before redirecting deleted user
        $this->get('security.context')->setToken(null);
        $this->get('request')->getSession()->invalidate();
        
        return $this->redirectToRoute('showusers');
    }

/** Eigenes Passwort ändern
     * @Route("/edit/password", name="changepassword")
     * @Security("has_role('ROLE_USER')")
     */
    public function editUserPasswordAction(Request $request) {
        $uid = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $user = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->find($uid);
        $form = $this->createForm("AppBundle\Form\UserPasswordType", $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            $msg = "Passwort wurde erfolgreich geändert!";
            return $this->render('default/confirm.html.twig', array(
                        'message' => $msg
            ));
        }

        return $this->render('edit/password.html.twig', array(
                    'user' => $user,
                    'form' => $form->createView()
        ));
    }    
    /** Passwort anderer User ändern
     * @Route("/edit/password/{id}", name="changepasswordof")
     * @Security("has_role('ROLE_STAFF')")
     */
    public function editUserPasswordOfAction(Request $request, $id) {
        
        $user = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->find($id);
        $form = $this->createForm("AppBundle\Form\UserPasswordType", $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            $msg = "Passwort wurde erfolgreich geändert!";
            return $this->render('default/confirm.html.twig', array(
                        'message' => $msg
            ));
        }

        return $this->render('edit/password.html.twig', array(
                    'user' => $user,
                    'form' => $form->createView()
        ));
    } 
    
    /** User Rechte ändern
     * @Route("/edit/rights/{id}", name="changerights")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editUserRightsAction(Request $request, $id) {
        
        $user = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->find($id);
        $form = $this->createForm("AppBundle\Form\UserRightsType", $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            $msg = "Rechte wurde erfolgreich geändert!";
            return $this->render('default/confirm.html.twig', array(
                        'message' => $msg
            ));
        }

        return $this->render('edit/rights.html.twig', array(
                    'user' => $user,
                    'form' => $form->createView()
        ));
    }
}
