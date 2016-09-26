<?php
/**
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2016 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_MODULE_DIR_.'/stripe/vendor/autoload.php');

class DashStripe extends Module {
    public function __construct() {
        $this->name = 'dashstripe';
        $this->version = '1.0.0';
        $this->author = 'JET';
        $this->tab = 'dashboard';
        $this->allow_push = true;
        $this->dependencies = array('stripe');
        $this->module_key = '2b992ae52c5f3bf2ab0c77fa42c8de85';
        parent::__construct();

        $this->displayName = $this->l('Stripe Dashboard Balance');
        $this->description = $this->l('Adds a block with the balance of your Stripe account');
        
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        
        \Stripe\Stripe::setApiKey(Configuration::get('DASHSTRIPE_SECRET_KEY'));
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
        $response = \Stripe\Balance::retrieve();
        foreach ($response->pending as $key => $value) {
           if ($value['currency'] === trim(Tools::strtolower(Configuration::get('DASHSTRIPE_CURRENCY')))) {
                return ($value['amount'] / 100).' '.$value['currency'];
           }
        }    
    } 
    /**
     * getStripeBalance
     * @return string balance and currency 
     */
    private function getAvailableBalance() {
        $response = \Stripe\Balance::retrieve();
        foreach ($response->available as $key => $value) {
           if ($value['currency'] === trim(Tools::strtolower(Configuration::get('DASHSTRIPE_CURRENCY')))) {
                return ($value['amount'] / 100).' '.$value['currency'];
           }
        }        
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
        $fields_form['form']['input'][] = array(
            'label' => $this->l('Stripe currency'),
            'hint' => $this->l('Please select currency, for example: gbp'),
            'name' => 'DASHSTRIPE_CURRENCY',
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
            'DASHSTRIPE_CURRENCY' => Tools::getValue('DASHSTRIPE_CURRENCY',
                Configuration::get('DASHSTRIPE_CURRENCY')),
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
