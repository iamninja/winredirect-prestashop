{capture name=path}
	{l s='Winbank Redirect' mod='winbankredirect'}
{/capture}

{* Need for prestashop prion v1.4 *}
{* {include file="$tpl_dir./breacrumb.tpl"} *}

<h1 class="page-heading">
	{l s='Order summary' mod='winbankredirect'}
</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if $nb_products <= 0}
	<p class="alert alert-warning">
		{l s='Your shopping cart is empty' mod='winbankredirect'}
	</p>
{else}
	<div class="box cheque-box">
		<h3 class="page-subheading">
			{l s='Winbank Redirect' mod='winbankredirect'}
		</h3>
		<p class="cheque-indent">
			<strong class="dark">
				{l s='You have chosen to pay with Winbank Redirect Method.' mod='winbankredirect'}
				{l s='Here is a short summary of your order:' mod='winbankredirect'}
			</strong>
		</p>
		<p>
			- {l s='The total amount of your order is' mod='winbankredirect'}
			<span id="amount" class="price">
				{displayPrice price=$total_amount}
			</span>
			{if $use_taxes == 1}
				{l s='(tax incl.)' mod='winbankredirect'}
			{/if}
		</p>
		<br>
		<p class="cheque-indent">
			<strong class="dark">
				{l s='Confirm your order below and you will be transfered to Piraeus Bank safe interface to make your paymet. We will never store any of your card details.'}
			</strong>
		</p>
	</div>

	<p class="cart_navigation clearfix" id="cart_navigation">
		<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}"
			class="button-exclusive btn btn-default">
			<i class="icon-chevron-left"></i>
			{l s='Other payment methods' mod='winbankredirect'}
		</a>
		<a class="button btn btn-default button-medium" href="{$link->getModuleLink('winbankredirect','payment'|escape:'html')}">
			<span>
				{l s='I confirm my order' mod='winbankredirect'}
				<i class="icon-chevron-right right"></i>
			</span>
		</a>
	</p>
{/if}