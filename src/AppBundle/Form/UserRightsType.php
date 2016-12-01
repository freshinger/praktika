<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class UserRightsType extends AbstractType
{
    	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('role', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                            'choices' => array(
                                'ROLE_USER' => 'Benutzer',
                                'ROLE_STAFF' => 'Mitarbeiter',
                                'ROLE_ADMIN' => 'Administrator'
                            )));
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\User'
		));
	}
}