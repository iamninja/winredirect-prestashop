{if $row}
<div id="winbankredirect_transaction" class="panel">
	<div class="panel-heading">
		<i class='icon-bank'></i> {l s='Paycenter Information' mod='winbankredirect'}
	</div>
	<div class="well">
		<dl class="dl-horizontal">
			<dt>{l s='Merchant reference' mod='winbankredirect'}</dt>
			<dd>{$merchant_reference|escape:'htmlall':'UTF-8'}</dd>
			<dt>{l s='Installments' mod='winbankredirect'}</dt>
			<dd>{$installments|escape:'htmlall':'UTF-8'}</dd>
		</dl>
	</div>
</div>
{/if}
