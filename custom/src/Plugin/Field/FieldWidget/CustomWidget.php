<?php
namespace Drupal\custom\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'MyFieldWidget' widget.
 *
 * @FieldWidget(
 *   id = "CustomWidget",
 *   label = @Translation("My Field widget"),
 *   field_types = {
 *     "custom"
 *   }
 * )
 */
class CustomWidget extends WidgetBase {

 /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
   
    $element['customwidget'] =  [
      '#type' => 'textfield',
      '#title' => 'My Custom Field',
      '#description' => 'Custom field to be used for alpha-numeric values',
      '#default_value' => isset($items[$delta]->title) ? $items[$delta]->title : NULL,
      '#weight' => 0,
    ];

    return $element;
  }

}