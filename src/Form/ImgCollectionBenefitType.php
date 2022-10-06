<?php

namespace App\Form;

use App\Entity\ImgCollectionBenefit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ImgCollectionBenefitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('img', FileType::class ,[
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
            ->add('alt',TextType::class ,[
                "attr" => [ 'class' => "form-control"],
                ])
            // ->add('benefit')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ImgCollectionBenefit::class,
        ]);
    }
}
