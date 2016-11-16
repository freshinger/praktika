<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Firma;
use AppBundle\Form\FirmaType;
use AppBundle\Entity\Ansprechpartner;
use AppBundle\Form\AnsprechpartnerType;

class SearchController extends Controller
{
    /**
	 * Firmensuchfunktion
	 */
	public function compsearchAction(Request $value){
		$repository = $this->getDoctrine()->getRepository('AppBundle:Firma');
        $firma = $repository->findByName('$value');
        echo $twig->render('index.html.twig', array('firma' => $firma));
	}
}