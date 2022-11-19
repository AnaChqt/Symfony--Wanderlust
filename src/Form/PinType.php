<?php

namespace App\Form;

use App\Entity\Pin;
// use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image (JPG or PNG file)',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Delete ?',
                'download_uri' => true,
                // 'contraints' => [
                //     new Image(['maxSize' => '8M'])
                // ],
                'imagine_pattern' => 'squared_thumbnail_small',
                // A partir d'ici c'est si je veux afficher un lien pour télécharger l'image
                // 'download_label' => 'Téléchfhhharger',

                // 'image_uri' => true,
                // 'asset_helper' => true,
            ])
            ->add('title')
            ->add('description');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pin::class,
        ]);
    }
}
