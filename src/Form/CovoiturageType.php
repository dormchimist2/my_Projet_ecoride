<?php

namespace App\Form;

use App\Entity\Covoiturage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CovoiturageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDeDepart', DateType::class, [
                'label' => 'Date de départ',
                'widget' => 'single_text',
                'attr' => ['placeholder' => 'Sélectionnez une date'],
            ])
            ->add('heureDeDepart', TimeType::class, [
                'label' => 'Heure de départ',
                'widget' => 'single_text', // Pour un meilleur rendu
                'attr' => ['placeholder' => 'Sélectionnez une heure'],
            ])
            ->add('lieuDeDepart', TextType::class, [
                'label' => 'Point de départ',
                'attr' => ['placeholder' => 'EX : Lyon'],
            ])
            ->add('dateArrivee', DateType::class, [
                'label' => 'Date d\'arrivée',
                'widget' => 'single_text',
                'attr' => ['placeholder' => 'Sélectionnez une date'],
            ])
            ->add('heureArrivee', TimeType::class, [
                'label' => 'Heure d\'arrivée',
                'widget' => 'single_text',
                'attr' => ['placeholder' => 'Sélectionnez une heure'],
            ])
            ->add('lieuArrivee', TextType::class, [
                'label' => 'Point d\'arrivée',
                'attr' => ['placeholder' => 'EX : Paris'],
            ])
            ->add('nombreDePlace', IntegerType::class, [
                'label' => 'Nombre de places disponibles',
                'attr' => ['min' => 1, 'placeholder' => 'Ex : 4'],
            ])
            ->add('prixPersonne', MoneyType::class, [
                'label' => 'Prix par personne',
                'currency' => 'EUR', // Définit la devise
                'attr' => ['placeholder' => 'Ex : 15.00'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Créer le trajet',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Covoiturage::class,
        ]);
    }
}
