<p class="payment_module" style="padding-bottom:10px;">
	<a href="#" id="winbankredirect-paymethod" title="{l s='Pay via Piraeus Paycenter' mod='winbank_redirect'}" class="winbankredirect">
		{l s='Pay via Piraeus Paycenter' mod='winbank_redirect'}&nbsp;<span>{l s='(Redirect)' mod='winbank_redirect'}</span>
		{* Debug *}
		{* diplay foo var *} {* {$foo} *}
	</a>
	{if $number_of_installments > 1}
		<span>
			Please choose first the number of installments.
		</span>
		<br><br><br>
		<span style="padding-right:10px">{l s='Number of installments:' mod='winbank_redirect'}</span><input id="installments-sl" data-slider-id='ex1Slider' type="text" data-slider-min="0" data-slider-max="{$number_of_installments|escape:'htmlall':'UTF-8'}" data-slider-step="1"  form="winbankredirect-installments"  name="number_of_installments" />
	{/if}

	{*<p>
	Number={$number_of_installments|escape:'htmlall':'UTF-8'}
	{if $number_of_installments > 1}
		<br>
		<p class="cheque-indent">
			<h4 class="dark">Pay via Piraeus Paycenter with installments</h4>
			<strong class="dark">
				{l s='Your order is qualified for payment with installments.' mod='winbank_redirect'}
				{l s='Choose below the number of installments you want.' mod='winbank_redirect'}
			</strong>
		</p>
		<span>Number of installments:</span>
		<div class="well">
			<input id="installments-sl" data-slider-id='ex1Slider' type="text" data-slider-min="0" data-slider-max="{$number_of_installments|escape:'htmlall':'UTF-8'}" data-slider-step="1"  form="winbankredirect-installments"  name="number_of_installments" />
		</div>
	{/if}
	</p>*}
</p>

<form action="{$link->getModuleLink('winbankredirect','paymentConfirmation'|escape:'htmlall':'UTF-8')}" style="display:none" id="winbankredirect-installments" method="POST">
</form>
<script>
	$('#winbankredirect-paymethod').click(function() {
		$('#winbankredirect-installments').submit();
		return false;
	});
</script>
