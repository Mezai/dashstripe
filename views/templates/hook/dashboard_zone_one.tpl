{*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<section id="dashstripe" class="panel widget {if $allow_push} allow_push{/if}">
    <div class="panel-heading">
        <i class="icon-bar-chart"></i> {l s='Stripe balance' mod='dashstripe'}
        <span class="panel-heading-action">
            <a class="list-toolbar-btn" href="#" onclick="toggleDashConfig('dashstripe'); return false;" title="configure">
                <i class="process-icon-configure"></i>
            </a>
           <a class="list-toolbar-btn" href="#" onclick="refreshDashboard('dashstripe'); return false;" title="refresh">
				<i class="process-icon-refresh"></i>
	    </a> 
        </span>
    </div>
    <section id=dashstripe_config" class="dash_config hide">
        <header><i class="icon-wrench"></i> {l s='Configuration' mod='dashstripe'}</header>
        {$dashstripe_config_form}
    </section>
    <section id="dash_live" class="loading">
		<ul class="data_list_large">
			<li>
				<span class="data_label size_l">
					{l s='Available balance' mod='dashstripe'}
					<small class="text-muted"><br/>
						{l s='updated just now' mod='dashstripe'}
					</small>
				</span>
				<span class="data_value size_xxl">
					<span id="available_balance"></span>
				</span>
			</li>
                        <li>
                            <span class="data_label size_l">
                                {l s='Pending balance' mod='dashstripe'}
                                <small class="text-muted"><br/>
                                    {l s='updated just now' mod='dashstripe'}
                                </small>
                            </span>
                            <span class="data_value size_xxl">
                                <span id="pending_balance"></span>
                            </span>
                        </li>
                            
		</ul>			
	</section>
</section>
