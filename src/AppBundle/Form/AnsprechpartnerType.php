<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;


class AnsprechpartnerType extends AbstractType
{
    	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('prename', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
				'required' => false))
                        ->add('surname', 'Symfony\Component\Form\Extension\Core\Type\TextType')
			->add('position', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
				'required' => false))
			->add('phone', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
				'required' => false))
			->add('email', 'Symfony\Component\Form\Extension\Core\Type\EmailType', array(
				'required' => false))
                        ;
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\Ansprechpartner'
		));
	}
}