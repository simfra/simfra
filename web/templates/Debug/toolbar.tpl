<div id="debug_content">
<ul id="debug_content_status" title="HTTP Status" class="{if count($dev.errors.warning)>0} devtoolbar_status_warning{elseif count($dev.errors.notice)>0}devtoolbar_status_notice{else}devtoolbar_status_ok{/if}">
{$dev.http}
</ul>
<ul id="con" title="link {$dev.page.route}" style="color: white;">
<p><img src="/img/toolbar/menu_34x32.png" />{if $ismobile!=1} {$dev.lang} | {$dev.page.controler}::{$dev.page.method} {/if}</p>
{if isset($dev.page) }
    <li style="text-align: center;color: white;">
        <table style="width: 80%;color: white;margin: 0 auto;min-width: 300px; margin-top: 20px;margin-bottom: 20px;">
        <tr>
            <td class="first">Controller:</td>
            <td class="second">{$dev.page.controler}</td>
        </tr>
        <tr>
            <td class="first">Method:</td>
            <td class="second">{$dev.page.method}</td>
        </tr>
        <tr>
            <td class="first">Class:</td>
            <td class="second">{$dev.class_path}</td>
        </tr>        
        <tr>
            <td class="first">Route:</td>
            <td class="second">{$dev.page.route}</td>
        </tr>
        <tr>
            <td class="first">Session:</td>
            <td class="second">{if $dev.session!=''}{$dev.session}{else}brak{/if}</td>
        </tr>        
        </table>
    </li>
{/if}    
</ul>
<ul  title="Script time execution" style="min-width:50px;height: 40px; background-color: #6ac334;display: block;line-height: 40px;text-align: center;float: left;font-weight: bold;padding-left: 10px; padding-right: 10px;">
<img src="/img/toolbar/run_34x32.png" /> {$dev.time} ms
</ul>
{* #9F1FFF *}
<ul id="ajax" title="Ajax Requests">
<img src="/img/toolbar/ajax_34x32.png" />
<span id="ajax_request" data-count="0">0</span>
<li>
        <table id="ajax_list">
        <tr><td></td></tr>
        </table>    
</li>
</ul>
<ul title="Memory usage" style="min-width:40px;height: 40px; display: block;line-height: 40px;text-align: center;float: left;font-weight: bold; padding-left: 10px; padding-right: 10px;">
{$dev.memory} kb
</ul>
<ul id="database" title="Database queries" >
<p><img src="/img/toolbar/database_34x32.png" /> {if isset($dev.database)}
{$a=(count($dev.database)-1)}{$a}{if $ismobile!=1} ({$dev.database.time}ms){/if}
{else}
0 w (0.000 ms)
{/if}</p>
{if isset($dev.database)}
    <li style="text-align: left;color: white;">
        <table style="color: white;margin: 0 auto;min-width: 300px; margin-top: 20px;margin-bottom: 20px;overflow: auto;padding:10px;">
        <tr><td>{if isset($dev.database)}Queries count: {$a=(count($dev.database)-1)}{$a}{/if}{if $ismobile==1} - in time ({$dev.database.time}ms){/if}</td></tr>
        {foreach from=$dev.database item=value key=counter}
        {if is_numeric($key)}
        <tr><td>
         {$counter+1} [{$value.time} ms] [{$value.file} - {$value.function}] {if $value.result==1}<b style="color: #6ac334;">OK</b>{else}<b style="color: red;">ERROR</b>{/if} - QUERY: "{$value.query}"  
        </td>
        </tr> 
{/if}
{/foreach}
        </table>
    </li>
{/if}
</ul>
<ul id="files" title="Files used" >
{$a=(count($dev.files))}
<p><img src="/img/toolbar/files_34x32.png" /> {$a}</p>
{if isset($dev.files)}
    <li style="color: white;">
        <table style="color: white;margin: 0 auto;min-width: 300px; margin-top: 20px;margin-bottom: 20px;overflow: auto;padding:10px;">
        <tr><td>{if isset($dev.files)}Files count: {$a} {/if}</td></tr>
        {foreach from=$dev.files key=counter item=value}
        <tr><td>
        {$counter+1} - {$value} 
        </td>
        </tr> 
        {/foreach}
        </table>
    </li>
{/if}
</ul>
{$notice=count($dev.errors.notice)}
{$warning=count($dev.errors.warning)}
<ul  title="Errors" style="min-width:50px;height: 40px; background-color: #6ac334;display: block;line-height: 40px;text-align: center;float: left;font-weight: bold;padding-left: 10px; padding-right: 10px;">
<img src="/img/toolbar/settings_34x32.png" /> {if isset($dev.errors)} {$notice+$warning}{else} 0 {/if}
</ul>
{if count($dev.errors)>0}

<ul id="notice" >{if $ismobile!=1}NOTICE: {/if}{$notice}
    {if count($dev.errors.notice)>0}
    <li>
    {foreach from=$dev.errors.notice item=value key=key}
    <span style="display: block;" title="File: {$value.file} Line: {$value.line} ">{$key+1} - {$value.error}</span>
    {/foreach}
    </li>
    {/if}
</ul>
<ul id="warning" >{if $ismobile!=1}WARNING: {/if}{$warning}
    {if count($dev.errors.warning)>0}
    <li>
    {foreach from=$dev.errors.warning item=value key=key}
    <span style="display: block;" title="File: {$value.file} Line: {$value.line} ">{$key+1} - {$value.error}</span>
    {/foreach}
    </li>
    {/if}
</ul>
{/if}
<ul id="template_info" ><img src="/img/toolbar/template_34x32.png" />{if $ismobile!=1}Template Info{/if}
    <li><div>{$dev_templates}</div></li>
</ul>
{literal}
<script type="text/javascript">
<!--

if(typeof $=='undefined') {
    var headTag = document.getElementsByTagName("head")[0];
    var jqTag = document.createElement('script');
    jqTag.type = 'text/javascript';
    jqTag.src = '/js/jquery-3.1.1.min.js'; 
    jqTag.onload = start;
    headTag.appendChild(jqTag);
} else {
     start();
}


function start()
{
$( document ).ready(function() {
  $("#debug_content").before().css('left', $(window).width()-40);
});

$(window).resize(function() {
    $("#debug_content").before().css('left', $(window).width()-40);
});

var counter =0;
$("#debug_content_status").on('click',function(){
dev($("#debug_content"));
});



$('body').on('keydown', function(event) {
  if (event.keyCode == 192 && event.ctrlKey == true) {
    dev($("#debug_content"));
  }
});




$("body").on('click', "#ajax_list tbody tr[data-response]",function(){
    if($(this).find("span[name='hidden_list']").hasClass("span_show")!=true){
        $(this).find("span[name='hidden_list']").addClass("span_show");
    }
    else{
        $(this).find("span[name='hidden_list']").removeClass("span_show");
    }
});

$("body").on('click', "span.dev_toolbar_plus",function(){
    if($(this).next().hasClass("span_show")!=true){
        $(this).next().addClass("span_show");
        $(this).html("-");
    }
    else{
        $(this).next().removeClass("span_show");
        $(this).html("+");
    }
});


$( document ).ajaxComplete(function(event,request, settings) {
    var ile = $("#ajax_request").attr("data-count");//$("#ajax_request").text();
    ile++;
    var status = "<span style=\"float: left; display: block;\"><span style='color: #6ac334'>"+ile+" -Status</span>: <span style=\"color: white; \">"+request.status+ "</span> <span style='color: #6ac334'>Url: </span><span style=\"color: white; \">"+ settings.url  + "</span> <span style='color: #6ac334'>Data</span>: <span style=\"color: white; \">" + settings.data + "</span></span>";
    var title = " Response: "+request.responseText;
    //var ile = $("#ajax_requests").data("count");
    $("#ajax_request").addClass("pulse");
    setTimeout(function(){$("#ajax_request").removeClass("pulse");},2000);
    $("#ajax_request").attr("data-count",ile);
    $("#ajax_request").text(ile);
    $("<tr data-response='" + ile + "' ><td>"+status+'<span name="hidden_list" style="display:none; min-width: 100px; min-height: 30px;transition: opacity 3s easy-out; opacity: 0;">'+title+'</span></td></tr>').prependTo("table#ajax_list > tbody");
});





function dev(pasek)
{
    if( $("#debug_content").css("left") =="0px") {
         $(pasek).animate({ left: $(window).width()-40 }, 300);
    }
    else{
        $(pasek).animate({ left: "0px" }, 300);         
    }
}

}
-->
</script>
{/literal}
</div>