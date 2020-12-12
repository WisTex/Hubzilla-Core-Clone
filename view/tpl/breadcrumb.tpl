<nav aria-label="breadcrumb">
	<ol class="breadcrumb bg-transparent">
		{{foreach $breadcrumbs as $breadcrumb}}
		{{if $breadcrumb@last}}
		<li class="breadcrumb-item active h3" aria-current="page">{{$breadcrumb.name}}</li>
		{{else}}
		<li class="breadcrumb-item h3 cloud-index attach-drop" data-folder="{{$breadcrumb.hash}}" title="{{$breadcrumb.hash}}"><a href="{{$breadcrumb.path}}">{{$breadcrumb.name}}</a></li>
		{{/if}}
		{{/foreach}}
	</ol>
</nav>
