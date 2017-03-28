
    <div class="box-header">
        <div class="box-tc">
            <div class="box-ml">
                <div class="box-mr">
                    <div class="box-tl">
                        <div class="box-tr">
                            <h1 class="context-title">
                                {$alert.label|wash()}
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
                <table class="special block" cellspacing="0" style="margin:0;">
                    <tr class="bgdark">
                        <th>{'Query'|i18n( 'extension/ocmailalert' )}</th>
                        <td>
                            <pre>{$alert.query|wash()}</pre>
                        </td>
                    </tr>
                    <tr class="bglight">
                        <th>{'Send alert if result count is'|i18n( 'extension/ocmailalert' )}</th>
                        <td>
                            {$alert.condition_operator|wash()} {$alert.match_condition_value|wash()}
                        </td>
                    </tr>
                    {if $alert.last_call|gt(0)}
                    <tr class="bglight">
                        <th>{'Last check'|i18n( 'extension/ocmailalert' )}</th>
                        <td>
                            {$alert.last_call|l10n('shortdatetime')}
                        </td>
                    </tr>
                    <tr class="bgdark">
                        <th>{'Last log'|i18n( 'extension/ocmailalert' )}</th>
                        <td>
                            {$alert.last_log|wash()}
                        </td>
                    </tr>
                    <tr class="bglight">
                        <th>{'Last sent mail'|i18n( 'extension/ocmailalert' )}</th>
                        <td>
                            <pre style="white-space: pre-line">{$alert.last_mail}</pre>
                        </td>
                    </tr>
                    {/if}
                </table>
                {* DESIGN: Content END *}
            </div>
        </div>
    </div>


