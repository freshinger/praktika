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
}
