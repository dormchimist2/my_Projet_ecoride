<?php

namespace App\Form;

use App\Entity\Covoiturage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CovoiturageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    
    {$builder
        ->add('dateDepart', DateType::class, [
            'widget' => 'single_text',
            'label' => 'Date de départ',
        ])
        ->add('heureDepart', TimeType::class, [
            'widget' => 'single_text',
            'label' => 'Heure de départ',
        ])
        ->add('pointDepart', TextType::class, [
            'label' => 'Point de départ',
        ])
        ->add('dateArrivee', DateType::class, [
            'widget' => 'single_text',
            'label' => 'Date d\'arrivée',
        ])
        ->add('heureArrivee', TimeType::class, [
            'widget' => 'single_text',
            'label' => 'Heure d\'arrivée',
        ])
        ->add('pointArrivee', TextType::class, [
            'label' => 'Point d\'arrivée',
        ])
        ->add('nbPlace', IntegerType::class, [
            'label' => 'Nombre de places',
            'constraints' => [
                new NotBlank(),
                new Positive(),
            ],
        ])
        ->add('prix', TextType::class, [
            'label' => 'Prix',
            'currency' => 'credit',
        ])
    ;
    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Covoiturage::class,
        ]);
    }
}
