<p class="payment_module">
	<a href="#" id="winbankredirect-paymethod" title="{l s='Pay via Piraeus Paycenter' mod='winbankpayment'}" class="winbankredirect">
		{l s='Pay via Piraeus Paycenter' mod='winbankredirect'}&nbsp;<span>{l s='(Redirect)' mod='winbankredirect'}</span>
		{if $number_of_installments > 1}
			<span>
				Please choose first the number of installments.
			</span>
		{/if}
	</a>
	<p>
	Number={$number_of_installments}
	{if $number_of_installments > 1}
		<br>
		<p class="cheque-indent">
			<h4 class="dark">Pay via Piraeus Paycenter with installments</h4>
			<strong class="dark">
				{l s='Your order is qualified for payment with installments.' mod='winbankredirect'}
				{l s='Choose below the number of installments you want.' mod='winbankredirect'}
			</strong>
		</p>
		<span>Number of installments:</span>
		<div class="btn-group" data-toggle="buttons">
		 	{for $number=0 to $number_of_installments}
				{if $number!=1}
					<label class="btn btn-primary">
						<input type="radio" name="number_of_installments" form="winbankredirect-installments" id="installments{$number}"value="{$number}" autocomplete="off" {if $number == 0}checked{/if}> {$number}
					</label>
				{/if}
		 	{/for}
		</div>
	{/if}
	</p>
</p>

<form action="{$link->getModuleLink('winbankredirect','paymentConfirmation'|escape:'html')}" style="display:none" id="winbankredirect-installments" method="POST">
</form>
<script>
	$('#winbankredirect-paymethod').click(function() {
		$('#winbankredirect-installments').submit();
		return false;
	});
</script>