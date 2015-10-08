{if $row}
<div id="winbankredirect_transaction" class="panel">
	<div class="panel-heading">
		<i class='icon-bank'></i> {l s='Paycenter Information' mod='winbank_redirect'}
	</div>
	<div class="well">
		<dl class="dl-horizontal">
			<dt>{l s='Merchant reference' mod='winbank_redirect'}</dt>
			<dd>{$merchant_reference|escape:'htmlall':'UTF-8'}</dd>
			<dt>{l s='Installments' mod='winbank_redirect'}</dt>
			<dd>{$installments|escape:'htmlall':'UTF-8'}</dd>
		</dl>
	</div>
</div>
{/if}
