<?php
namespace App\Form;

use App\Entity\PreferenceCdt;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PreferenceCdtType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('accepteFumeurs', CheckboxType::class, [
                'label'    => 'J\'accepte les fumeurs',
                'required' => false,
                'attr'     => ['class' => 'form-check-input'],
            ])
            ->add('accepteAnimauxG', CheckboxType::class, [
                'label'    => 'J\'accepte les animaux de grande taille',
                'required' => false,
                'attr'     => ['class' => 'form-check-input'],
            ])
            ->add('accepteAnimauxP', CheckboxType::class, [
                'label'    => 'J\'accepte les petits animaux',
                'required' => false,
                'attr'     => ['class' => 'form-check-input'],
            ])
            ->add('musiqueAutorisee', CheckboxType::class, [
                'label'    => 'Musique autorisée',
                'required' => false,
                'attr'     => ['class' => 'form-check-input'],
            ])
            ->add('discussionAutorisee', CheckboxType::class, [
                'label'    => 'Discussions autorisées',
                'required' => false,
                'attr'     => ['class' => 'form-check-input'],
            ])
            ->add('siegeBebe', CheckboxType::class, [
                'label'    => 'Siège bébé disponible',
                'required' => false,
                'attr'     => ['class' => 'form-check-input'],
            ])
            ->add('adPreference', TextareaType::class, [
                'label'    => 'Préférences supplémentaires',
                'required' => false,
                'attr' => [
                    'rows'        => 4,
                    'class'       => 'form-control',
                    'placeholder' => 'Entrez des détails supplémentaires si nécessaire.'
                ]
            ]);

            }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => PreferenceCdt::class,
            'include_user' => false, // Permet de choisir d'afficher ou non le champ user
        ]);
    }
}

