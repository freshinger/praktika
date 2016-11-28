<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FirmaType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', 'Symfony\Component\Form\Extension\Core\Type\TextType')
			->add('street', 'Symfony\Component\Form\Extension\Core\Type\TextType')
			->add('city', 'Symfony\Component\Form\Extension\Core\Type\TextType')
			->add('postcode', 'Symfony\Component\Form\Extension\Core\Type\TextType')
			->add('website', "Symfony\Component\Form\Extension\Core\Type\TextType", array(
				'required' => false))
			->add('description', "Symfony\Component\Form\Extension\Core\Type\TextType", array(
				'required' => false))
		;
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\Firma'
		));
	}
}