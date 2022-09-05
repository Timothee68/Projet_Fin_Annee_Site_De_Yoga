<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use VictorPrdh\RecaptchaBundle\Form\ReCaptchaType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('name'  , TextType::class, [
                "attr" => ['class' => "form-control"],
            ])
            ->add('firstName' , TextType::class, [
                "attr" => ['class' => "form-control"],
            ])
            ->add('dateOfBirth', DateType::class, [
                'widget' => 'single_text',
                "attr" => ['class' => "form-control"],
            ])
            ->add('email', EmailType::class,[
                "attr" => ['class' => "form-control"],
            ])
            ->add('telephone', TextType::class, [
                "attr" => ['class' => "form-control"],
            ])
            ->add('img', FileType::class ,[
                'label' => 'Image profile',
                "attr" => [ 'class' => "form-control"],
                'mapped' => false,
                'required' => false,
                    'constraints' => [
                        new File([
                        'maxSize' => '10254k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'svp upload une image valide !!!',
                        ]),
                    ]
                ])
            ->add('agreeTerms', CheckboxType::class, [
                "attr" => ['class' => "form-check-input"],
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' =>false ,
                'type' => PasswordType::class,
                'constraints' => [
                    new Regex('^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$^' , 'Minimum 8 caractères, une majuscule, une minuscule, et un chiffre') 
                ],
                'invalid_message' => 'Les mots de passe ne corresponde pas.',
                'options' => ['attr' => ['class' => 'password-field form-control']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ])
            ->add("recaptcha", ReCaptchaType::class)
            ;
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
