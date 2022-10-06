<?php

namespace App\Form;

use App\Entity\Benefit;
use App\Entity\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('benefit', EntityType::class, [
                "attr" => ['class' => "form-control"],
                // looks for choices from this entity
                'class' => Benefit::class,
                // uses the User.username property as the visible option string
                'choice_label' => 'title',           
                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])
            ->add('nbPlaceMax', IntegerType::class, [
                "attr" => ['class' => "form-control"]
                ])

            ->add('startTime', DateTimeType::class, [
                'widget' => 'single_text',
                "attr" => ['class' => "form-control" , 'min'=> '1'],
            ])
            ->add('endTime', DateTimeType::class, [
                'widget' => 'single_text',
                "attr" => ['class' => "form-control"],
            ])
            ->add('backgroundColor', ColorType::class, [
                "attr" => ['class' => "form-control"]
                ])
            ->add('submit',SubmitType::class, [
                "attr" => ['class' => "form-control bg-primary"]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
