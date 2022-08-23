<?php

namespace Drupal\custom\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block with a simple text.
 *
 * @block(
 *   id = "drupalbook_customblock_settings",
 *   admin_label = @Translation("My Timezone Block"),
 * )
 */
class Customblock extends BlockBase{
  /**
   * {@block}
   */
 protected $account;

  public function defaultConfiguration() {
    return ['label_display' => FALSE];
  }
  public function build() {
    $config = $this->getConfiguration();
 $countryList = $this->countryQuery();
//print_R($countryList);exit;
/*return [
      '#theme' => 'customtemplate',
 	  '#country' => $countryList,
    ];
*/
	return \Drupal::formBuilder()->getForm('Drupal\custom\Form\GetmessagesForm');


  }
function countryQuery() {
$query = \Drupal::database()->select('country_list', 'm');
  $query->fields('m', ['country']);
  $results = $query->execute()->fetchAll();
$countryResult=array('--Select Country');
foreach ($results as $record) {
$countryResult[]=$record->country;
}
return ($countryResult);
}
public function getCacheMaxAge() {
    return 0;
  }
 
  /**
   * {@block}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }
 
  /**
   * {@block}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();
 
    $form['drupalbook_customblock_settings'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#description' => $this->t('What you want to display?'),
      '#default_value' => !empty($config['drupalbook_customblock_settings']) ? $config['drupalbook_customblock_settings'] : '',
    ];
 
    return $form;
  }
 
  /**
   * {@block}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['drupalbook_customblock_settings'] = $form_state->getValue('drupalbook_customblock_settings');
  }
}