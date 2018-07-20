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
use eZ\Publish\API\Repository\LocationService;
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

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /**
     * ImageAssetFieldType constructor.
     *
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     */
    public function __construct(
        ContentService $contentService,
        LocationService $locationService
    ) {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
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
        $defaultLocationPath = null;

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
        if ($view->parent->vars['value']->fieldDefinition->fieldSettings['selectionDefaultLocation']) {
            $targetLocation = $this->locationService->loadLocation(
                $view->parent->vars['value']->fieldDefinition->fieldSettings['selectionDefaultLocation']
            );
            $defaultLocationPath = rtrim($targetLocation->pathString, '/');
        }
        $view->vars['default_location_path'] = $defaultLocationPath;
        $view->vars['image'] = $image;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'ezrepoforms_fieldtype'
        ]);
    }
}
