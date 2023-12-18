<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Supplier;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('productLabel', TextType::class, [
                'attr' => [
                    'class' => 'mt-2 block mb-2 text-sm font-medium text-gray-900 dark:text-white bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                    'placeholder' => 'HK',
                    'required' => true
                ]
            ])
            ->add('productDescription', TextType::class, [
                'attr' => [
                    'class' => 'mt-2 block mb-2 text-sm font-medium text-gray-900 dark:text-white bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                    'placeholder' => 'Description de l\'arme',
                    'required' => true
                ]
            ])
            ->add('productPrice', TextType::class, [
                'attr' => [
                    'class' => 'mt-2 block mb-2 text-sm font-medium text-gray-900 dark:text-white bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                    'placeholder' => '...',
                    'required' => true
                ]
            ])
            ->add('productImage', FileType::class, [
                'attr' => [
                    'class' => 'mt-2 block mb-2 text-sm font-medium text-gray-900 dark:text-white bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                    'required' => true
                ]
            ])
            ->add('is_active', CheckboxType::class, [
                'label' => 'Upload Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(['maxSize' => '1024k', 'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                    ]]),
                ],
                'attr' => [
                    'class' => 'mt-2 block mb-2 text-sm font-medium text-gray-900 dark:text-white bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                    'placeholder' => '...',
                    'required' => true
                ],
            ])
            ->add('supplier', EntityType::class, [
                'class' => Supplier::class,
                'choice_label' => 'supplierName',
                'attr' => [
                    'class' => 'mt-2 block mb-2 text-sm font-medium text-gray-900 dark:text-white bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                    'required' => true
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.id NOT IN (:ids)')
                        ->setParameter('ids', [1, 2, 3]);
                },
                'choice_label' => 'categoryName',
                'attr' => [
                    'class' => 'mt-2 block mb-2 text-sm font-medium text-gray-900 dark:text-white bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                    'required' => true
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
