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

class UserController extends Controller
{														/* User Funktionen */
	
	/** Neuen Benutzer mit Username, Email und Password anlegen
	 * @Route("/create/user", name="registration")
	 */
	public function userAction(Request $request){
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
            
            return $this->redirectToRoute('reg_success',array(
					'id' => $user->getId(),
					'email' => $user->getEmail()));
        }
		
		return $this->render('default/form/registration.html.twig', array(
				'form' => $form->createView()
		));
	}
	
	/** Weiterleitung nach dem Anlegen eines neuen Benutzers
    * @Route("/create/success/user/{id}/{email}", name="reg_success")
    */
    public function regsuccessAction($id, $email){
           return $this->render('default/form/registration_success.html.twig', array(
					'id' => $id,
					'email' => $email));
    }
	
	/** Wenn eingeloggt, Seine eigenen Benutzerdaten mit Editierfunktion anzeigen
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
        
        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->request->has('delete'))
            {
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
    * @Route("/show/users", name="showusers")
    * @Security("has_role('ROLE_STAFF')")
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
	
	/** Zeigt einen Benutzer mit Editierfunktion für Mitarbeiter
     * @Route("/show/user/{id}", name="showuser")
     * @Security("has_role('ROLE_STAFF')")
     */
    public function showUserAction(Request $request, $id)
    {
        $user = $this->getDoctrine()
                    ->getRepository('AppBundle:User')
                    ->find($id);
        $form = $this->createForm("AppBundle\Form\ProfilType", $user);
		$form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
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
	
	/** Benutzer aus der Datenbank löschen
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
}