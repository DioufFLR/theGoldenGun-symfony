<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Supplier;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
                ],
                'label' => 'Image du produit',
                'mapped' => true,
                'required' => false,
                'data_class' => null,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide',
                    ])
                ],
            ])
            ->add('is_active', CheckboxType::class, [
                'label' => 'Actif ?',
                'mapped' => true,
                'required' => false,
                'attr' => [
                    'class' => 'w-1/2'
                ],
            ])
            ->add('productStock', IntegerType::class, [
                'label' => 'Stock',
                'mapped' => true,
                'required' => true,
                'attr' => [
                    'class' => 'mt-2 block mb-2 text-sm font-medium text-gray-900 dark:text-white bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500'
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
                'group_by' => 'parent.categoryName',
                # On crée un filtre pour n'avoir que les catégories avec un parent
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.parent IS NOT NULL')
                        ->orderBy('c.categoryName', 'ASC');
                },
                'choice_label' => 'categoryName',
                'attr' => [
                    'class' => 'mt-2 block mb-2 text-sm font-medium text-gray-900 dark:text-white bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                    'required' => true
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
