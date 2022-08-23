<?php  
  
/**  
 * @file  
 * Contains Drupal\custom\Form\MessagesForm.  
 */  
  
namespace Drupal\custom\Form;  
  
use Drupal\Core\Form\ConfigFormBase;  
use Drupal\Core\Form\FormStateInterface;  
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;

  
class MessagesForm extends ConfigFormBase {  
  /**  
   * {@inheritdoc}  
   */  
  protected function getEditableConfigNames() {  
    return [  
      'welcome.adminsettings',  
    ];  
  }  
  
  /**  
   * {@inheritdoc}  
   */  
  public function getFormId() {  
    return 'custom_form';  
  }

public function buildForm(array $form, FormStateInterface $form_state) {  
    $config = $this->config('welcome.adminsettings');  
  
    $form['Country'] = [  
      '#type' => 'textfield',  
      '#title' => $this->t('Country Name'),  
      '#description' => $this->t('Country Name'),  
      '#default_value' => '',  
    ];
	 $form['City'] = [  
      '#type' => 'textfield',  
      '#title' => $this->t('City Name'),  
      '#description' => $this->t('City Name'),  
      '#default_value' => '',  
    ];  
	
	 $form['Timezone'] = [  
      '#type' => 'select',  
      '#title' => $this->t('Select Timezone'),  
      '#description' => $this->t('City Name'),  
      '#options' => array(
	   '' => t('Select TimeZone'),
        'America_Chicago' => t('America/Chicago'),
		'America_New_York' => t('America/New_York'),
        'Asia_Tokyo' => t('Asia/Tokyo'),
		'Asia_Dubai' => t('Asia/Dubai'),
		'Asia_Kolkata' => t('Asia/Kolkata'),
		'Asia_Tokyo' => t('Asia/Tokyo'),
      ), 
	  
	  '#ajax' => [
      'callback' => [$this, 'myAjaxCallback'], //alternative notation
    'disable-refocus' => FALSE,
    'event' => 'change',
    'wrapper' => 'edit-output',
    'progress' => [
      'type' => 'throbber',
      'message' => $this->t('Verifying entry...'),
    ],
	]
	
    ]; 
	
	$form['output'] = [
      '#type' => 'textfield',
      '#size' => '60',
      '#disabled' => TRUE,
      '#value' => '',      
      '#prefix' => '<div id="edit-output">',
      '#suffix' => '</div>',
    ];
	
	
  
    return parent::buildForm($form, $form_state);  
  }    
  
   public function submitForm(array &$form, FormStateInterface $form_state) {  
   // parent::submitForm($form, $form_state);  
	$countryName = $form_state->getValue('Country');
	$cityName = $form_state->getValue('City');
	$timeZoneName = $form_state->getValue('Timezone');

	$connection = \Drupal\Core\Database\Database::getConnection() ;
	$result = $connection->insert('country_list')
	  ->fields([
		'country' => $countryName,
		'city' => $cityName,
		'timezone' => $timeZoneName,
	  ])
	  ->execute();
		\Drupal::messenger()->addMessage('successfully saved Country and City in the Database');
    }

function convertTimeToLocal($timezone='Asia/Kolkata') {
       
        date_default_timezone_set($timezone);
		$date= date('d').'th '. date('M Y - h:i A');
		return $date;

}


  public function validateForm(array &$form, FormStateInterface $form_state) {
	  
     
 
      if(($form_state->getValue('Country'))=='') {
      $form_state->setErrorByName('Country', $this->t('Please enter the Country'));
    }else
    if(($form_state->getValue('City'))=='') {
      $form_state->setErrorByName('City', $this->t('Please enter the City'));
    }else
	if(($form_state->getValue('Timezone'))=='') {
      $form_state->setErrorByName('City', $this->t('Please Select the Timezone'));
    }
  }
  
  public function myAjaxCallback(array &$form, FormStateInterface $form_state) {
  // Prepare our textfield. check if the example select field has a selected option.
  if ($selectedValue = $form_state->getValue('Timezone')) {
      // Get the text of the selected option.
	  
	  $selectedText = $form['Timezone']['#options'][$selectedValue];
      //$selectedText = $form['Timezone']['#options'][$selectedValue];
      // Place the text of the selected option in our textfield.
	  $selectedTextTimezone = $this->convertTimeToLocal($selectedText);
      $form['output']['#value'] = $selectedTextTimezone;
  }
  // Return the prepared textfield.
  return $form['output']; 
}
   
  
}  