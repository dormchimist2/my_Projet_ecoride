<?php
namespace App\Form;

use App\Entity\Userx;
use App\Entity\Voiture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class VoitureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('marque', TextType::class, [
                'label' => 'Marque',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La marque est obligatoire.']),
                    new Assert\Length(['max' => 50, 'maxMessage' => 'La marque ne peut pas dépasser 50 caractères.']),
                ],
            ])
            ->add('modele', TextType::class, [
                'label' => 'Modèle',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le modèle est obligatoire.']),
                    new Assert\Length(['max' => 100, 'maxMessage' => 'Le modèle ne peut pas dépasser 100 caractères.']),
                ],
            ])
            ->add('couleur', TextType::class, [
                'label' => 'Couleur',
                'constraints' => [
                    new Assert\Length(['max' => 50, 'maxMessage' => 'La couleur ne peut pas dépasser 50 caractères.']),
                ],
            ])
            ->add('dateMatriculation', DateType::class, [
                'widget' => 'single_text', 
                'html5' => true, 
                'label' => 'Date de matriculation',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La date de matriculation est obligatoire.']),
                ],
            ])
            
            ->add('nImmatriculation', TextType::class, [
                'label' => 'Numéro d\'immatriculation',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le numéro d\'immatriculation est obligatoire.']),
                    new Assert\Length(['max' => 50, 'maxMessage' => 'Le numéro d\'immatriculation ne peut pas dépasser 50 caractères.']),
                ],
            ])
            ->add('moteur', TextType::class, [
                'label' => 'Type de moteur',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le type de moteur est obligatoire.']),
                    new Assert\Length(['max' => 50, 'maxMessage' => 'Le type de moteur ne peut pas dépasser 50 caractères.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voiture::class,
        ]);
    }
}
