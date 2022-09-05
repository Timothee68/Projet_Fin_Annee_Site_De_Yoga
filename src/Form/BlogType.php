<?php

namespace App\Form;

use App\Entity\Blog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contents', TextareaType::class ,[
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
            ->add('altImage',TextType::class ,[
                "attr" => [ 'class' => "form-control"],
                ])
            ->add('urlVideo',TextType::class ,[
                "attr" => [ 'class' => "form-control"],
                'required' => false,
                ])
            ->add('title', TextType::class, [
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
            'data_class' => Blog::class,
        ]);
    }
}
