<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class FirmaType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', 'Symfony\Component\Form\Extension\Core\Type\TextType')
			->add('street', 'Symfony\Component\Form\Extension\Core\Type\TextType')
			->add('city', 'Symfony\Component\Form\Extension\Core\Type\TextType')
			->add('postcode', 'Symfony\Component\Form\Extension\Core\Type\TextType')
			->add('website', 'Symfony\Component\Form\Extension\Core\Type\TextType')
			->add('description', 'Symfony\Component\Form\Extension\Core\Type\TextType')
                        ->add('ansprechpartner', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                        // query choices from this entity
                        'class' => 'AppBundle:Ansprechpartner',

                        // use the User.username property as the visible option string
                        'choice_label' => 'surname',

                        // used to render a select box, check boxes or radios
                        // 'multiple' => true,
                        // 'expanded' => true,
                        'required' => false
                        ))
		;
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\Firma'
		));
	}
}