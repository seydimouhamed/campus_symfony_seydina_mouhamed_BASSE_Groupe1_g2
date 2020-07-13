<?php

namespace App\Form;

use App\Entity\Etudiant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtudiantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prenom')
            ->add('nom')
            ->add('email')
            ->add('telephone')
            ->add('date_naissance')
            ->add('profil',ChoiceType::class, [
                'choices'  => [
                    '--' => null,
                    'Logé' => "loge",
                    'Boursier' => "boursier",
                    'Non Boursier' => "nonBoursier",
                ],
                "attr"=> ['id'=>"selectID"],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Etudiant::class,
        ]);
    }
}
