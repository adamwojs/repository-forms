<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\RepositoryForms\Form\Type\FieldType;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageAssetFieldType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /**
     * ImageAssetFieldType constructor.
     *
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     */
    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_ezimageasset';
    }

    public function getParent()
    {
        return BinaryBaseFieldType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'destinationContentId',
                HiddenType::class
            );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $image = null;

        if ($view->vars['value']['destinationContentId']) {
            try {
                $contentInfo = $this->contentService->loadContent(
                    $view->vars['value']['destinationContentId']
                );

                $image = $contentInfo->getFieldValue('image');
            } catch (NotFoundException | UnauthorizedException $exception) {
                // FIXME: Ignored exception
            }
        }

        $view->vars['image'] = $image;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'ezrepoforms_fieldtype'
        ]);
    }
}
