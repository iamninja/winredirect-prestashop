{capture name=path}
	{l s='Winbank Redirect' mod='winbank_redirect'}
{/capture}

{* Need for prestashop prion v1.4 *}
{* {include file="$tpl_dir./breacrumb.tpl"} *}

<h1 class="page-heading">
	{l s='Order summary' mod='winbank_redirect'}
	{* Debug *}
	{* foo var *} {* {$foo}vagios *}
</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{* Debug *}
{* {$cart_property|@var_dump} *}

{if $nb_products <= 0}
	<p class="alert alert-warning">
		{l s='Your shopping cart is empty' mod='winbank_redirect'}
	</p>
{else}
	<div class="box cheque-box">
		<h3 class="page-subheading">
			{l s='Winbank Redirect' mod='winbank_redirect'}
		</h3>
		<p class="cheque-indent">
			<strong class="dark">
				{l s='You have chosen to pay with Winbank Redirect Method.' mod='winbankredirect'}
				{l s='Here is a short summary of your order:' mod='winbank_redirect'}
			</strong>
		</p>
		<p>
			{l s='The total amount of your order is' mod='winbank_redirect'}
			<span id="amount" class="price">
				{displayPrice price=$total_amount}
			</span>
			{if $use_taxes == 1}
				{l s='(tax incl.)' mod='winbank_redirect'}
			{/if}
			<p>
				- {l s='The total amount of the products in your cart is' mod='winbankredirect'}
				<span id="amount" class="price">
					{displayPrice price=$products_amount}
				</span>
			</p>
			<p>
				- {l s='For shipping you pay' mod='winbank_redirect'}
				<span id="amount" class="price">
					{displayPrice price=$shipping_amount}
				</span>
			</p>
			{if $number_of_installments > 1}
				{l s='You have choosen to pay with ' mod='winbank_redirect'}
				{$number_of_installments}
				{l s=' installments' mod='winbank_redirect'}
			{/if}
			<p>

			</p>
		</p>
		<br>
		<p class="cheque-indent">
			<strong class="dark">
				{l s='Confirm your order below and you will be transfered to Piraeus Bank safe interface to make your paymet. We will never store any of your card details.' mod='winbank_redirect'}
			</strong>
		</p>
	</div>

	<p class="cart_navigation clearfix" id="cart_navigation">
		<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}"
			class="button-exclusive btn btn-default">
			<i class="icon-chevron-left"></i>
			{l s='Other payment methods' mod='winbank_redirect'}
		</a>
		<a class="button btn btn-default button-medium" id="winbankredirect-redirect-link" href="http://google.com">
			<span>
				{l s='I confirm my order' mod='winbank_redirect'}
				<i class="icon-chevron-right right"></i>
			</span>
		</a>
	</p>

	<form action="{$api_url}" style="display:none" id="winbankredirect-form" method="POST">
		<input type="hidden" name="AcquirerId" value="{$acquirer_id}" />
		<input type="hidden" name="MerchantId" value="{$merchant_id}" />
		<input type="hidden" name="PosId" value="{$pos_id}" />
		<input type="hidden" name="User" value="{$user}" />
		<input type="hidden" name="LanguageCode" value="{$language_code}" />
		<input type="hidden" name="MerchantReference" value="{$merchant_reference}" />
		<input type="hidden" name="ParamBackLink" value="{$param_back_link}" />
	</form>
	<script>
		$('#winbankredirect-redirect-link').click(function() {
			$('#winbankredirect-form').submit();
			return false;
		});
	</script>
{/if}
