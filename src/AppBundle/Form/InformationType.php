<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class InformationType extends AbstractType
{
    	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('type', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                            'choices' => array(
                                'telefon' => 'Telefongespräch',
                                'email' => 'E-Mailaustausch',
                                'praktikumsbesuch' => 'Praktikumsbesuch',
                                'persönlich' => 'persönlich',
                                'sonst' => 'anderes'
                            )))
			->add('datum', 'Symfony\Component\Form\Extension\Core\Type\DateType', array(
				'required' => false))
			->add('title', 'Symfony\Component\Form\Extension\Core\Type\TextType')
			->add('content', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', array(
				'attr' => array('cols' => '50', 'rows' => '6')
			));
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\Information'
		));
	}
}