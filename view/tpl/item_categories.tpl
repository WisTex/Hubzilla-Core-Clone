{{if $categories}}
<!--div class="categorytags"-->
{{foreach $categories as $cat}}
<span class="item-category badge rounded-pill bg-warning"><i class="fa fa-asterisk text-dark"></i>&nbsp;{{if $cat.url}}<a class="text-dark" href="{{$cat.url}}">{{$cat.term}}</a>{{else}}{{$cat.term}}{{/if}}</span>
{{/foreach}}
<!--/div-->
{{/if}}

