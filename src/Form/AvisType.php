<?php
namespace App\Form;

use App\Entity\Avis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('note', RangeType::class, [
            'label' => 'Note (entre 0 et 5)',
            'attr' => [
                'min' => 0,
                'max' => 5,
                'step' => 1,
            ],
            'constraints' => [
                new Range([
                    'min' => 0,
                    'max' => 5,
                    'notInRangeMessage' => 'La note doit être entre {{ min }} et {{ max }}.',
                ]),
            ],
        ])
        
            ->add('commentaire', TextareaType::class, [
                'label' => 'Commentaire',
                'constraints' => [
                    new Length(['max' => 250])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}
