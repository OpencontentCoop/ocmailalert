<h2>{$alert.label|wash()}</h2>

<p>{$alert.body|wash()}</p>

<h3>{'Query'|i18n('ocmailalert')}</h3>
<pre>{$alert.query|wash()}</pre>

<h3>{'Results'|i18n('ocmailalert')}</h3>
{if $search_results.totalCount|gt(0)}
    <table>
        <tr>
            <th>{'First %number of %total results'|i18n('ocmailalert',,hash('%number',count($search_results.searchHits), '%total', $search_results.totalCount))|wash}</th>
        </tr>
        {foreach $search_results.searchHits as $item}
            <tr>
                <td>
                    <a href="{concat('content/view/full/',$item.metadata.mainNodeId)|ezurl(no,full)}">
                        {$item.metadata.name[$language]|wash()}
                    </a>
                    <small>{$item.metadata.classIdentifier|wash()}</small>
                </td>

            </tr>
        {/foreach}
    </table>
{else}
    <p>{'No results'|i18n('ocmailalert')}</p>
{/if}
