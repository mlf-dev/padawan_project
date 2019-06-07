<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('dateInscription')
            //->add('statut')
            ->add('githubRepository', TextType::class, ['label'=>'Quel est le nom de votre répertoire Github ?'])
            //->add('project')
            //->add('padawan')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
            // supprimer le titre automatique du formulaire :
            'label'=>false
        ]);
    }
}
