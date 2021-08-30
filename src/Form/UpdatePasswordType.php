<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UpdatePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'disabled' => true,
                'label' => 'Mon prénom'
            ])
            ->add('lastname', TextType::class, [
                'disabled' => true,
                'label' => 'Mon nom'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Mon adresse email'
            ])
            ->add('old_password', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre mot de passe actuel'
                ]
            ])
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Le mot de passe et la confirmation doivent être identiques.',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Votre mot de passe'],
                'label' => 'Mot de passe',
                'required' => false,
                'first_options' => [ 'label' => 'Mon nouveau mot de passe', 'attr' => ['class' => 'form-control', 'placeholder' => 'Votre nouveau mot de passe']],
                'second_options' => [ 'label' => 'Confirmer mon nouveau mot de passe', 'attr' => ['class' => 'form-control mb-3', 'placeholder' => 'Merci de confirmer votre nouveau mot de passe']]
            ])
            ->add('submit', SubmitType::class,[
                'attr' => ['class' => 'w-100 btn btn-lg btn-primary'],
                'label' => 'Mettre à jour'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
