<div class="crm-block crm-form-block crm-smartdebit-settings-form-block">
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="top"}
  </div>

  <h3>Configuration</h3>
  <table class="form-layout-compressed">
    <tbody>
    {foreach from=$elementNames item=elementName}
      <tr>
        <td>
          <label for="{$elementName}">{$form.$elementName.label} {help id=$elementName title=$form.$elementName.label}</label>
          {$form.$elementName.html}
        </td>
      </tr>
    {/foreach}
    </tbody>
  </table>

  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>
