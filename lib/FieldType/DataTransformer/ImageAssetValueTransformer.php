<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
declare(strict_types=1);

namespace EzSystems\RepositoryForms\FieldType\DataTransformer;

use eZ\Publish\Core\FieldType\ImageAsset\Value;
use Symfony\Component\Form\DataTransformerInterface;

class ImageAssetValueTransformer extends BinaryFileValueTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (!$value instanceof Value) {
            return null;
        }

        if ($value->destinationContentId === null) {
            return null;
        }

        return array_merge(
            $this->getDefaultProperties(),
            ['destinationContentId' => $value->destinationContentId]
        );
    }

    public function reverseTransform($value)
    {
        if ($value === null || !is_array($value)) {
            return null;
        }
        if (null !== $value['file']) {
//        $valueObject = $this->getReverseTransformedValue($value);
        }

        return new Value($value['destinationContentId']);
    }
}
