<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ReservationType extends AbstractType
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
            // ->add('dateOfBirth', DateType::class, [
            //     'widget' => 'single_text',
            //     "attr" => ['class' => "form-control"],
            // ])
            ->add('telephone', TextType::class, [
                "attr" => ['class' => "form-control"],
            ])
            ->add('nb_places', IntegerType::class, [
                "attr" => ['class' => "form-control"],
            ])
            ->add('submit',SubmitType::class, [
                "attr" => ['class' => "form-control bg-primary"]
                ])
            // ->add('user')
            // ->add('session')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
