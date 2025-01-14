<?php

namespace App\Form;

use App\Entity\Character;
use App\Entity\Movie;
use App\Repository\CharacterRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'maxlength' => '100',
                ],
                'label' => 'Title',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 50])
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Description',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
            ])
            ->add('releaseDate', DateType::class, [
                'widget' => 'single_text',
                'input'  => 'datetime_immutable',
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Release date',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
            ])
            ->add('characters', EntityType::class, [
                'class' => Character::class,
                'query_builder' => function (CharacterRepository $r) use ($builder) {
                    return $r->createQueryBuilder('c')
                        ->where('c.movie = :movie')
                        ->orWhere('c.movie is null')
                        ->orderBy('c.id', 'ASC')
                        ->setParameter('movie', $builder->getData()->getId());
                },
                'attr' => [
                    'class' => 'form-check',
                ],
                'label' => 'Characters',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'choice_label' => 'name',
                'choice_attr' => function () {
                    return ['class' => 'ms-3 me-1'];
                },
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
                'label' => 'Save',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
