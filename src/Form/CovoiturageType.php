<?php

namespace App\Form;

use App\Entity\Covoiturage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class CovoiturageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $today = new \DateTime(); // Représente la date du jour

        $builder
            ->add('dateDepart', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'label' => 'Date de départ',
                'attr' => [
                    'min' => $today->format('Y-m-d'), // Format en attente pour juno
                    'placeholder' => 'Sélectionnez une date'
                ],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => $today, // Contrainte sur la date
                        'message' => 'La date doit être aujourd\'hui ou dans le futur.',
                    ]),
                ],
            ])
            ->add('heureDepart', TimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'label' => 'Heure de départ',
                'placeholder' =>['Sélectionnez une heure']
            ])
            ->add('pointDepart', TextType::class, [
                'label' => 'Point de départ',
                'attr' => ['placeholder' => 'Ex: Paris'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez indiquer un point de départ.']),
                ],
            ])
            ->add('dateArrivee', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'label' => 'Date d\'arrivée',
                'attr' => [
                    'placeholder' => 'Sélectionnez une date',
                    'min' => $today->format('Y-m-d'), // Format en attente de vue
                ],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => $today,
                        'message' => 'La date doit être aujourd\'hui ou dans le futur.',
                    ]),
                ],
            ])
            ->add('heureArrivee', TimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'label' => 'Heure d\'arrivée',
                'attr' => [
                    'placeholder' => 'Sélectionnez une heure',
                    'min' => $today->format('H:i') // Format pour 'HH:mm'
                ]
            ])
            ->add('pointArrivee', TextType::class, [
                'label' => 'Point d\'arrivée',
                'attr' => ['placeholder' => 'Ex: Lyon'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez indiquer un point d\'arrivée.']),
                ],
            ])
            ->add('nbPlace', IntegerType::class, [
                'label' => 'Nombre de places',
                'attr' => ['min' => 1, 'placeholder' => 'Ex: 4'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez indiquer le nombre de places disponibles.']),
                    new Positive(['message' => 'Le nombre de places doit être supérieur à zéro.']),
                ],
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix (en crédits)',
                'attr' => ['min' => 0, 'step' => '0.01', 'placeholder' => 'Ex: 15.50'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez indiquer le prix.']),
                    new Positive(['message' => 'Le prix doit être un nombre positif.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Covoiturage::class,
        ]);
    }
}
