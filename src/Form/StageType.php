<?php

namespace App\Form;

use App\Entity\Stage;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class StageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', CKEditorType::class, [
                'config' => array('toolbar' => 'full'),
                "attr" => ['class' => "form-control"]
                ])
            ->add('title', TextType::class ,[
                "attr" => [ 'class' => "form-control"]
                ])
            ->add('image', FileType::class ,[
                'label' => 'Image réliée à la préstation',
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
                        'mimeTypesMessage' => 'Please upload une image valide',
                        ]),
                    ]
                ])
            ->add('backgroundColor', ColorType::class, [
                "attr" => ['class' => "form-control"]
                ])
            ->add('nbPlaceMax', IntegerType::class, [
                "attr" => ['class' => "form-control"]
                ])
            ->add('price', IntegerType::class, [
                "attr" => ['class' => "form-control"]
                ])
            ->add('endTime', DateTimeType::class, [
                'widget' => 'single_text',
                "attr" => ['class' => "form-control" , 'min'=> '1'],
            ])
            ->add('startTime', DateTimeType::class, [
                'widget' => 'single_text',
                "attr" => ['class' => "form-control" , 'min'=> '1'],
            ])
            ->add('submit',SubmitType::class, [
                "attr" => ['class' => "form-control bg-primary"]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stage::class,
        ]);
    }
}
