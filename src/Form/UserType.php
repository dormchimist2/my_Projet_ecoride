<?php
namespace App\Form;

use App\Entity\Userx;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Conducteur' => 'ROLE_CONDUCTEUR',
                    'Passager' => 'ROLE_PASSAGER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Rôles',
            ])

            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 8, 'max' => 4096]),
                    new Assert\Regex([
                        'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*[\d])(?=.*[\W_])[A-Za-z\d\W_]{8,}$/',
                        'message' => 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.',
                    ]),
                ],
            ])
            

            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])

           ->add('pseudo', TextType::class, [
    'label' => 'Pseudo',
    'required' => true,
    'constraints' => [
        new Assert\NotBlank(),
        new Assert\Length([
            'min' => 3,
            'max' => 50,
            'minMessage' => 'Le pseudo doit contenir au moins {{ limit }} caractères.',
            'maxMessage' => 'Le pseudo ne peut pas dépasser {{ limit }} caractères.',
        ]),
    ],
])


            ->add('photo', FileType::class, [
                'label' => 'Image (Fichiers JPG ou PNG)',
                'mapped' => false, // Ne pas mapper directement à l'entité
                'required' => false,
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG ou PNG).',
                    ]),
                ],
            ])
            



            ->add('phoneNumber', TextType::class, [
                'label' => 'Numéro de téléphone',
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^\+?\d{10,15}$/',
                        'message' => 'Veuillez entrer un numéro de téléphone valide.',
                    ]),
                ],
            ])
            ->add('street', TextType::class, [
                'label' => 'Rue',
                'required' => false,
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => false,
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Créer un compte',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Userx::class,
            'csrf_protection' => true,   //  Active la protection CSRF
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'user_form', // Identifiant unique pour éviter les conflits
        ]);
    }
    
}
