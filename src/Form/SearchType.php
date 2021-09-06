<?php

namespace App\Form;

use App\Entity\Category;
use App\Services\Search;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    /**
     * Construction du formulaire
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('string', TextType::class, [
            'label' => false,
            'required' => false,
            'attr' => [
                'placeholder' => 'Votre recherche',
                'class' => 'form-control'
            ] 
        ])
        ->add('categories', EntityType::class, [
            'label' => false,
            'required' => false,
            'class' => Category::class,
            'multiple' => true,
            'expanded' => true,
            'attr' => [
                'class' => 'my-2'
            ] 
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Filtrer',  
            'attr' => [
                'class' => 'btn btn-primary'
            ] 
        ]); 
    }

    /**
     * Configure les options du formulaire
     *
     * @param OptionsResolver $resolver
     * @return void
     */    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    /**
     * Retourne un tableau avec le prefix du nom de la class Search
     *
     * @return void
     */
    public function getBlockPrefix()
    {
        return '';
    }
}