<div class="d-grid gap-2 mb-3">
	{{foreach $menu as $m}}
	<button id="{{$m.id}}" class="{{$m.class}}" type="{{$m.type}}">
		{{if $m.icon}}<i class="fa fa-fw fa-{{$m.icon}}"></i> {{/if}}{{$m.label}}
	</button>
	{{/foreach}}
</div>
