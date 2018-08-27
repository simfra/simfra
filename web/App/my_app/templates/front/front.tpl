FRONT
{$fields->generateView()}
{*
{$fields->fields[0]->input}

{$fields->fields[0]->setOption("class","number")}
{$fields->fields[0]->generateView()}

<pre>
{$fields->getFields()|print_r}
</pre>*}