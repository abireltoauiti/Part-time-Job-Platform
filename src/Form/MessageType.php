<?php

namespace App\Form;

use App\Entity\Message;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', TextType::class, [
                'label' => 'Objet',
                'attr' => ['placeholder' => 'Sujet du message']
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Message',
                'attr' => ['placeholder' => 'Tapez votre message ici...', 'rows' => 6]
            ])
            ->add('recipient', EntityType::class, [
                'class' => User::class,
                'label' => 'Destinataire',
                'choice_label' => function(User $user) {
                    return $user->getNom() . ' (' . $user->getEmail() . ')';
                },
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :roleC AND u.roles LIKE :roleE OR u.roles LIKE :roleC')
                        ->setParameter('roleC', '%ROLE_CANDIDAT%')
                        ->setParameter('roleE', '%ROLE_ENTREPRISE%');
                },
                'placeholder' => 'Sélectionnez un utilisateur',
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => ['class' => 'btn btn-primary mt-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
