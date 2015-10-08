{if $row}
<div id="winbankredirect_transaction" class="panel">
	<div class="panel-heading">
		<i class='icon-bank'></i> {l s='Paycenter Information' mod='winbankredirect'}
	</div>
	<div class="well">
		<dl class="dl-horizontal">
			<dt>{l s='Merchant reference' mod='winbankredirect'}</dt>
			<dd>{$merchant_reference}</dd>
			<dt>{l s='Installments' mod='winbankredirect'}</dt>
			<dd>{$installments}</dd>
		</dl>
	</div>
</div>
{/if}