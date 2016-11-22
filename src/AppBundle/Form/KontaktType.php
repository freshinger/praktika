<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class KontaktType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('ansprechpartner', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                            'class' => 'AppBundle:Ansprechpartner',
                            'choice_label' => 'surname'
                        ))
		;
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\Kontakt'
		));
	}
}