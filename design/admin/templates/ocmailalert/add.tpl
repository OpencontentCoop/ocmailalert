{if is_set( $error_message )}
<div class="message-error">
    <h2>{'Input did not validate'|i18n( 'design/admin/settings' )}</h2>
    <p>{$error_message}</p>
</div>
{/if}
<form action="{concat('/ocmailalert/add/', cond(is_set($alert.id),$alert.id,'') )|ezurl(no)}" method="post">
    <div class="box-header">
        <div class="box-tc">
            <div class="box-ml">
                <div class="box-mr">
                    <div class="box-tl">
                        <div class="box-tr">
                            <h1 class="context-title">
                                {if is_set($alert.id)}
                                    {'Edit alert'|i18n( 'extension/ocmailalert' )}
                                {else}
                                    {'Configure new alert'|i18n( 'extension/ocmailalert' )}
                                {/if}
                            </h1>
                            {* DESIGN: Mainline *}<div class="header-mainline"></div>
                            {* DESIGN: Header END *}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {* DESIGN: Content START *}
    <div class="box-ml">
        <div class="box-mr">
            <div class="box-content">
                <table class="list cache block" cellspacing="0" style="margin:0;">
                    <tr class="bgdark">
                        <td>{'Label'|i18n( 'extension/ocmailalert' )}</td>
                        <td>
                            <input type="text" class="box" name="label" value="{if $alert}{$alert.label|wash()}{/if}"/>
                        </td>
                    </tr>
                    <tr class="bglight">
                        <td>{'Frequency'|i18n( 'extension/ocmailalert' )}</td>
                        <td>
                            <select name="frequency" id="Frequency">
                                {foreach $frequencies as $frequencyIdentifier => $frequencyName}
                                    <option value="{$frequencyIdentifier}" {if and($alert, $alert.frequency|eq($frequencyIdentifier))}selected="selected"{/if}>{$frequencyName}</option>
                                {/foreach}
                            </select>
                        </td>
                    </tr>
                    <tr class="bgdark">
                        <td>{'Query'|i18n( 'extension/ocmailalert' )}</td>
                        <td>
                            <input type="text" class="box" name="query" value="{if $alert}{$alert.query|wash()}{/if}"/>
                        </td>
                    </tr>
                    <tr class="bglight">
                        <td>{'Send alert if result count is'|i18n( 'extension/ocmailalert' )}</td>
                        <td>
                            <select name="condition" id="Condition">
                                {foreach $conditions as $conditionIdentifier => $conditionName}
                                    <option value="{$conditionIdentifier}" {if and($alert, $alert.condition|eq($conditionIdentifier))}selected="selected"{/if}>{$conditionName|wash()}</option>
                                {/foreach}
                            </select>

                            <input type="text" name="condition_value" value="{if $alert}{$alert.condition_value|wash()}{else}0{/if}"/>
                        </td>
                    </tr>
                    <tr class="bgdark">
                        <td>{'Email recipients (one per line)'|i18n( 'extension/ocmailalert' )}</td>
                        <td>
                            <textarea class="box" name="recipients" id="Recipients" rows="4" cols="70">{if $alert}{$alert.recipients|wash()}{/if}</textarea>
                        </td>
                    </tr>
                    <tr class="bglight">
                        <td>{'Mail alert subject'|i18n( 'extension/ocmailalert' )}</td>
                        <td>
                            <input type="text" class="box" name="subject" value="{if $alert}{$alert.subject|wash()}{/if}"/>
                        </td>
                    </tr>
                    <tr class="bgdark">
                        <td>{'Mail alert body'|i18n( 'extension/ocmailalert' )}</td>
                        <td>
                            <textarea class="box" name="body" id="Body" rows="5" cols="70">{if $alert}{$alert.body|wash()}{/if}</textarea>
                        </td>
                    </tr>
                </table>
                {* DESIGN: Content END *}
            </div>
        </div>
    </div>
                            
    {* Buttons. *}
    <div class="controlbar">
    {* DESIGN: Control bar START *}
        <div class="box-bc">
            <div class="box-ml">
                <div class="box-mr">
                    <div class="box-tc">
                        <div class="box-bl">
                            <div class="box-br">
                                <div class="block object-right">
                                    <input class="defaultbutton" type="submit" name="SaveAlertButton" value="{'Save'|i18n( 'extension/ocmailalert' )}" />
                                </div>
                            {* DESIGN: Control bar END *}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
