<div class="context-block">
    <div class="box-header">
        <div class="box-tc">
            <div class="box-ml">
                <div class="box-mr">
                    <div class="box-tl">
                        <div class="box-tr">
                            <h1 class="context-title">{'Alert list'|i18n( 'extension/ocmailalert' )}</h1>
                            <div class="header-mainline"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box-ml">
    <div class="box-mr">
        <div class="box-content">
            <div class="block">
            {if not( $alerts|count )}
                {"No alerts"|i18n( 'extension/ocmailalert' )}
            {else}
                <table class="list" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{"Label"|i18n( 'extension/ocmailalert' )}</th>
                            <th>{"Frequency"|i18n( 'extension/ocmailalert' )}</th>
                            <th>{"Query"|i18n( 'extension/ocmailalert' )}</th>
                            <th>{"Send condition"|i18n( 'extension/ocmailalert' )}</th>
                            <th>{"Recipients"|i18n( 'extension/ocmailalert' )}</th>
                            <th>{"Subject"|i18n( 'extension/ocmailalert' )}</th>
                            <th>{"Body"|i18n( 'extension/ocmailalert' )}</th>
                            <th>{"Last check"|i18n( 'extension/ocmailalert' )}</th>
                            <th>{"Last result"|i18n( 'extension/ocmailalert' )}</th>
                            <th width="1"></th>
                            <th width="1"></th>
                        </tr>
                    </thead>

                    <tbody>
                        {foreach $alerts as $alert sequence array( 'bglight', 'bgdark' ) as $trClass}
                            <tr class="{$trClass}">
                                <td>{$alert.label|wash()}</td>
                                <td>{$alert.frequency|wash()}</td>
                                <td>
                                    <a target="_blank" href="{concat('opendata/console/1/?query=',$alert.query)|ezurl(no)}">
                                        {$alert.query|wash()}
                                    </a>
                                </td>
                                <td>{$alert.condition_operator} {$alert.match_condition_value|wash()}</td>
                                <td>{$alert.recipients_address|implode(', ')}</td>
                                <td>{$alert.subject|wash()}</td>
                                <td>{$alert.body|wash()}</td>
                                <td>
                                    {if $alert.last_call|gt(0)}
                                        <p><a href="{concat( '/ocmailalert/detail/', $alert.id )|ezurl(no)}">
                                            {$alert.last_call|l10n('shortdatetime')}
                                        </a></p>
                                        <a href="{concat( '/ocmailalert/reset/', $alert.id )|ezurl(no)}" title="{'Reset'|i18n( 'extension/ocmailalert' )}">[{'Reset'|i18n( 'extension/ocmailalert' )}]</a>
                                    {/if}
                                </td>
                                <td>{$alert.last_log|wash()}</td>
                                <td>
                                    <a href="{concat( '/ocmailalert/add/', $alert.id )|ezurl(no)}" title="{'Edit'|i18n( 'extension/ocmailalert' )}"><img src={'edit.gif'|ezimage} alt="{'Edit'|i18n( 'extension/ocmailalert')}" /></a>
                                </td>
                                <td>
                                    <a href={concat( '/ocmailalert/remove/', $alert.id )|ezurl} title="{'Remove alert'|i18n( 'extension/ocmailalert' )}"
                                       onclick="return confirm('{'Are you sure you want to remove this alert ?'|i18n( 'extension/ocmailalert' )}')"><img src={'trash-icon-16x16.gif'|ezimage} alt="{'Remove'|i18n( 'extension/ocmailalert')}" /></a>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
                <p>&nbsp;</p>
            {/if}
            </div>
            <div class="context-toolbar">
			{include name=navigator uri='design:navigator/google.tpl'
			                        page_uri=$uri
			                        item_count=$alert_count
			                        view_parameters=$view_parameters
			                        item_limit=$limit}
            </div>
        </div>
    </div>
</div>

<div class="controlbar"><div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br"></div></div></div></div></div></div></div>
