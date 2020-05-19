<li data-li="register_participant" style="display: none;">
  <a class="crm-participant-counted" href="{crmURL p='civicrm/participant/add' q="reset=1&action=add&context=standalone&eid=`$event.id`"}"><b>{ts}Register Participant{/ts}</b></a>
</li>

{literal}
<script type="text/javascript">
  (function() {
    CRM.$(document).ready(function () {
      if (CRM.$("div.crm-actions-ribbon").length) {
        var registerParticipantElement = CRM.$('[data-li="register_participant"]');

        CRM.$(registerParticipantElement).prependTo('#crm-participant-list > .crm-participant-list-inner > ul');
        registerParticipantElement.show();
      }
    });
  })();
</script>
{/literal}
