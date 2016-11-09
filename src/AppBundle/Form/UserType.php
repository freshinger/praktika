<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserType extends AbstractType{
	public function buildForm(FormBuilderInterface $builder, array $options){
		$builder
			->add('email', "Symfony\Component\Form\Extension\Core\Type\EmailType")
			->add('password', "Symfony\Component\Form\Extension\Core\Type\RepeatedType", array(
				'type' => "Symfony\Component\Form\Extension\Core\Type\PasswordType",
				'invalid_message' => 'Die Passwörter müssen übereinstimmen!',
				'options' => array('attr' => array('class' => 'password-field')),
				'required' => true,
				'first_options'  => array('label' => 'Passwort'),
				'second_options' => array('label' => 'Passwort wiederholen')));
	}
	
	public function configureOptions(OptionsResolver $resolver){
		$resolver->setDefaults(array(
        'data_class' => 'AppBundle\Entity\User',));
	}
}