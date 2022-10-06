<?php

namespace App\Form;

use App\Entity\Benefit;
use App\Form\ImgCollectionBenefitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class BenefitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                "attr" => ['class' => "form-control"]
                ])
            ->add('description', TextareaType::class ,[
                "attr" => [ 'class' => "form-control"]
                ])
            ->add('price', IntegerType::class, [
                "attr" => ['class' => "form-control"],
                ])
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
                        'mimeTypesMessage' => 'S\'il vous plait upload une image valide',
                        ]),
                    ]
                ])
                
            //on ajoute le champ "images" dans le formulaire il n'est pas liée a la BDD MULTIPLE CHOIX IMAGES A LA SELECTION 
            ->add('imgCollectionBenefits', FileType::class, [
                'label' => 'Images réliées à la galerie d\'image pour la préstation Séléctionne autant de photos que tu le souhaite',
                "attr" => [ 'class' => "form-control"],
                'multiple' => true,
                'mapped' => false,
                'required' => false,
            ])                

            // pour faire un COLLECTION TYPE 
            // ->add('imgCollectionBenefits',CollectionType::class, [
            //     // la collection attend l'élément qu'elle entrera dans le form ce n'est pas obligatoire que se soit un autre form
            //     'entry_type' => ImgCollectionBenefitType::class,
            //     'prototype' => true,
            //     //  on va autoriser l'ajout  d'un nouvelle élément dans l'entité session qui seront persister grace au cascade persiste sur l'élément programme
            //     // ca va activer un data prototype qui sera un attribu html qu'on pourra manipuler en js
            //     'allow_add' => true, 
            //     'allow_delete' => true,
            //     // il obligatoire car Session  n'a pas de setProgramm() mais c'est Programme qui contient setSession() 
            //     // Programme est propriaitaire de la relation, pour éviter un mapped a false on ajoute le by reference a false.
            //     'by_reference' => false,
            //     'entry_options' => [
            //         'attr' => ['class' => 'form-control'],
            //         'label' => 'Choisis les photos pour afficher dans la gallery',
            //         ],
            //     ])
            ->add('submit',SubmitType::class, [
                "attr" => ['class' => "form-control bg-primary"]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Benefit::class,
        ]);
    }
}
