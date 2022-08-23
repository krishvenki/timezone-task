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
use Drupal\Core\Ajax\ChangedCommand;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\UpdateBuildIdCommand;
  
class GetmessagesForm extends ConfigFormBase {  
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
    return 'get_custom_form';  
  }

public function buildForm(array $form, FormStateInterface $form_state) {  
    $config = $this->config('welcome.adminsettings');  

$countryList = $this->countryQuery();

    $form['Country'] = [  
      '#type' => 'select',  
      '#title' => $this->t('Country Name'),  
      '#description' => $this->t('Country Name'),  
      '#options' => $countryList, 
		'#ajax' => [
			  'callback' => [$this, 'myAjaxCountryCallback'], //alternative notation
			'disable-refocus' => FALSE,
			'event' => 'change',
			'wrapper' => 'edit-outputcity',
			'progress' => [
			  'type' => 'throbber',
			  'message' => $this->t('Verifying entry...'),
			],
			]
    ];

    $form['City'] = [  
      '#type' => 'select',  
	   '#validated' => true,
      '#title' => $this->t('City Name'),  
      '#description' => $this->t('City Name'),  
      '#options' =>array('select the city'),

		'#prefix' => '<div id="edit-outputcity">',
'#suffix' => '</div>',
		'#ajax' => [
			  'callback' => [$this, 'myAjaxCityCallback'], //alternative notation
			'disable-refocus' => FALSE,
			'event' => 'change',
			'wrapper' => 'edit-outputtimezone',
			'progress' => [
			  'type' => 'throbber',
			  'message' => $this->t('Verifying entry...'),
			],
			]
    ];
	
	 $form['Timezone'] = [  
      '#type' => 'select',  
'#validated' => true,
      '#title' => $this->t('Select Timezone'),  
      '#options' => array('select the timezone'), 

		'#prefix' => '<div id="edit-outputtimezone">',
'#suffix' => '</div>',
	  
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
'#validated' => true,
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
        $timezone =str_replace('_','/',$timezone);
        date_default_timezone_set($timezone);
		$date= date('d').'th '. date('M Y - h:i A');
		return $date;

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

function cityQuery($selectedCountry) {
$query = \Drupal::database()->select('country_list', 'm');
  $query->fields('m', ['city']);
	$query->condition('m.country',$selectedCountry,'='); 
  $results = $query->execute()->fetchAll();
//print_R($results);exit;
$cityResult=array('select the city');
foreach ($results as $record) {
$cityResult[$record->city]=$record->city;
}
return ($cityResult);
}

function timezoneQuery($selectedCountry,$selectedCity) {
$query = \Drupal::database()->select('country_list', 'm');
  $query->fields('m', ['timezone']);
	$query->condition('m.country',$selectedCountry,'='); 
 $query->condition('m.city',$selectedCity,'='); 
  $results = $query->execute()->fetchAll();
$timezoneResult=array('select the timezone');

foreach ($results as $record) {
$timezoneResult[$record->timezone]=$record->timezone;
}
return ($timezoneResult);
}

  
  public function myAjaxCountryCallback(array &$form, FormStateInterface $form_state) {
 
  if ($selectedValue = $form_state->getValue('Country')) {
 	  $selectedCountry = $form['Country']['#options'][$selectedValue];
	  $cityList = $this->cityQuery($selectedCountry);	

  }
$form['City']['#options']=$cityList;

$form_state->setRebuild(TRUE);
  return $form['City'];
 
}
public function myAjaxCityCallback(array &$form, FormStateInterface $form_state) {
   if ($selectedValue = $form_state->getValue('City')) {
 	 $selectedCountry = $form['Country']['#options'][$form_state->getValue('Country')];
	 $selectedCity = $form_state->getValue('City');
	  $timezoneList = $this->timezoneQuery($selectedCountry,$selectedCity);	
		
  }

$form['Timezone']['#options']=$timezoneList;
$form_state->setRebuild(TRUE);
return $form['Timezone'];

}
   
public function myAjaxCallback(array &$form, FormStateInterface $form_state) {
		  if ($selectedValue = $form_state->getValue('Timezone')) {
			 $selectedText = $form_state->getValue('Timezone');
			  $selectedTextTimezone = $this->convertTimeToLocal($selectedText);
			  $form['output']['#value'] = $selectedTextTimezone;
		  }

	$form_state->setRebuild(TRUE);
	return $form['output']; 
}


}  