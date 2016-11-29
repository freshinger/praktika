<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PraktikumType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('startdatum', 'Symfony\Component\Form\Extension\Core\Type\DateType')
			->add('enddatum', 'Symfony\Component\Form\Extension\Core\Type\DateType')
			->add('beruf', 'Symfony\Component\Form\Extension\Core\Type\TextType')
			->add('user','Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                            'class' => 'AppBundle:User',
                            'choice_label' => 'username'))
			->add('firma', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                            'class' => 'AppBundle:Firma',
                            'choice_label' => 'name'));
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\Praktikum'
		));
	}
}