<?php
namespace Drupal\custom\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin implementation of the 'Country' field type.
 *
 * @FieldType(
 *   id = "country",
 *   label = @Translation("Country"),
 *   description = @Translation("This field is used to store alpha-numeric values."),
 *   default_widget = "CountryWidget",
 *   default_formatter = "custom_default"
 * )
 */
class CustomField extends FieldItemBase {

 /**
  * {@inheritdoc}
  */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $definition) {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['country'] = DataDefinition::create('string')
    ->setLabel(new TranslatableMarkup('Text value'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $definition) {
    $schema = [
        'columns' => [
          'country' => [
            'type' => 'varchar',
            'length' => 255,
          ],
        ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->getValue();
    if (isset($value['country']) && $value['country'] != '') {
      return FALSE;
    }
    return TRUE;
  }

}