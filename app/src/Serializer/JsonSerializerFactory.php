<?php

declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class JsonSerializerFactory
{
  /**
   * reference normalizers: https://symfony.com/doc/current/serializer.html
   * @return Serializer
   */
  public static function create(): SerializerInterface
  {
    /** use if you need annotations or attributes (requires doctrine AnnotationReader or AttributeLoader) */
    $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
    $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);

    $infoExtractor = new PropertyInfoExtractor(
      [],
      [new PhpDocExtractor(), new ReflectionExtractor()]
    );
    $objectNormalizer = new ObjectNormalizer(
      classMetadataFactory: $classMetadataFactory,
      nameConverter: $metadataAwareNameConverter,
      propertyTypeExtractor: $infoExtractor,
    );

    $normalizers = [
      $objectNormalizer,
      new ArrayDenormalizer(),
    ];

    $encoders = [
      'json' => new JsonEncoder()
    ];

    return new Serializer($normalizers, $encoders);
  }
}
