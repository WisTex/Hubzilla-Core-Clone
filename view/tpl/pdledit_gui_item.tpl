<div class="pdledit_gui_item card mb-3" data-src="{{$entry.src}}">
	<div class="card-header d-flex justify-content-between">
		<span class="text-uppercase">{{$entry.name}}</span>
		<div class="badge rounded-pill{{if $entry.type === 'widget'}} bg-info text-dark{{/if}}{{if $entry.type === 'content'}} bg-primary{{/if}}{{if $entry.type === 'menu'}} bg-secondary{{/if}}{{if $entry.type === 'block'}} bg-warning{{/if}}">
			{{$entry.type}}
		</div>
	</div>
	<div class="card-body">
		<button type="button" class="btn btn-sm btn-outline-primary pdledit_gui_item_src">Edit</button>
		<button type="button" class="btn btn-sm btn-outline-danger pdledit_gui_item_remove">Remove</button>
		<i class="fa fa-fw fa-arrows-alt m-2 float-end cursor-pointer pdledit_gui_item_handle"></i>
	</div>
</div>
