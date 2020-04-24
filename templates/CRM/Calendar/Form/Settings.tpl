<div class="crm-block crm-form-block crm-form-civimobilesettings-block">
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="top"}
  </div>
  <div>
    <table class="form-layout-compressed">
      <tbody>
      {foreach from=$elementNames item=elementName}
      <tr class="crm-group-form-block-isReserved">
        <td class="label" style="width: 30%">
          <label for="{$elementName}">
            {$form.$elementName.label} {help id=$elementName title=$form.$elementName.label}
          </label>
        </td>
        <td>
          <div>
            {$form.$elementName.html}
          </div>
        </td>
      </tr>
      {/foreach}
      </tbody>
    </table>
  </div>
  {if $synchronizationNotice}
    <div class="status">
      {$synchronizationNotice}
    </div>
  {/if}
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>

{literal}
  <script type="text/javascript">
    CRM.$(function ($) {
      $("#_qf_Settings_submit-bottom").click(function(){
        localStorage.removeItem('dashboard');
      });
    });
  </script>
{/literal}
