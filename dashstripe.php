<?php

include 'vendor/autoload.php';
use Stripe\Stripe;
use Stripe\Balance;
class DashStripe extends Module {
    public function __construct() {
        $this->name = 'dashstripe';
        $this->version = '1.0.0';
        $this->author = 'JET';
        $this->tab = 'dashboard';
        $this->allow_push = true;

        parent::__construct();

        $this->displayName = $this->l('Stripe Dashboard Balance');
        $this->description = $this->l('Adds a block with the balance of your Stripe account');
        
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        
        Stripe::setApiKey(Configuration::get('DASHSTRIPE_SECRET_KEY'));
    }

    /**
     * Install module
     * @return bool
     */
    public function install() {
        return parent::install()
            && $this->registerHook('dashboardZoneOne')
            && $this->registerHook('dashboardData');
    }
    public function hookDashboardData($params) {
        return array(
            'data_value' => array(
                'available_balance' => $this->getAvailableBalance(),
                'pending_balance' => $this->getPendingBalance(),
            ),
        ); 
    }   

    private function getPendingBalance() {
        $response = Balance::retrieve();

        return ($response->pending[0]['amount'] / 100).' '.$response->pending[0]['currency'];
    } 
    /**
     * getStripeBalance
     * @return string balance and currency 
     */
    private function getAvailableBalance() {
        $response = Balance::retrieve();
        return ($response->available[0]['amount'] / 100).' '.$response->available[0]['currency']; 
    }    
    public function renderConfigForm() {
        $fields_form = array(
            'form' => array(
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right submit_dash_config',
                    'reset' => array(
                        'title' => $this->l('Cancel'),
                        'class' => 'btn btn-default cancel_dash_config',          
                    )
                )
            ),  
        );

        $fields_form['form']['input'][] = array(
            'label' => $this->l('Stripe secret key'),
            'hint' => $this->l('Your stripe secret key'),
            'name' => 'DASHSTRIPE_SECRET_KEY',
            'type' => 'text',

        );
        

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->id = (int)Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitDashConfig';
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form));
        }

    public function getConfigFieldsValues() {
        return array(
            'DASHSTRIPE_SECRET_KEY' => Tools::getValue('DASHSTRIPE_SECRET_KEY', Configuration::get('DASHSTRIPE_SECRET_KEY')),
            'DASHSTRIPE_TEST_MODE' => Tools::getValue('DASHSTRIPE_TEST_MODE', Configuration::get('DASHSTRIPE_TEST_MODE')),
        );
    }
    public function hookDashboardZoneOne($params) {
        $this->context->smarty->assign(
           array_merge(array(
               'dashstripe_config_form' => $this->renderConfigForm(),
            )
        ), $this->getConfigFieldsValues());        

        return $this->display(__FILE__, 'dashboard_zone_one.tpl');
    }

}
