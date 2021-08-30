<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Votre nom de famille'],
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2, 'max' => 255])
                ],
                'label' => 'Nom'
            ])
            ->add('firstname', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Votre prénom'],
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2, 'max' => 255])
                ],
                'label' => 'Prénom'
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'votre-email@gmail.com'],
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2, 'max' => 255])
                ],
                'label' => 'Email'
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe et la confirmation doivent être identiques.',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Votre mot de passe'],
                'label' => 'Mot de passe',
                'required' => true,
                'first_options' => [ 'label' => 'Mot de passe', 'attr' => ['class' => 'form-control', 'placeholder' => 'Votre mot de passe']],
                'second_options' => [ 'label' => 'Confirmer le mot de passe', 'attr' => ['class' => 'form-control mb-3', 'placeholder' => 'Merci de confirmer votre mot de passe']]
            ])
            ->add('tos', CheckboxType::class, [
                'attr' => ['class' => 'form-check-input me-2'],
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'I agree all statements in <a href="#!">Terms of service</a>'
                    ])
                    ],
                'label' => 'I agree all statements in Terms of service'
            ])
            ->add('submit', SubmitType::class,[
                'attr' => ['class' => 'w-100 btn btn-lg btn-primary'],
                'label' => 'S\'inscrire'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
