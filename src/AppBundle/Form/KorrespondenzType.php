<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class KorrespondenzType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('type', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                            'choices' => array(
                                'Telefongespräch' => 'telefon',
                                'E-Mailaustausch' => 'email',
                                'Praktikumsbesuch' => 'praktikumsbesuch',
                                'persönlich' => 'persönlich',
                                'anderes' => 'sonst'
                            )))
			->add('datum', 'Symfony\Component\Form\Extension\Core\Type\DateType')
			->add('content', 'Symfony\Component\Form\Extension\Core\Type\TextareaType')
		;
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\Korrespondenz'
		));
	}
}