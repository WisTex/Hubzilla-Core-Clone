<div id="messages-widget" class="border rounded overflow-auto mb-3" style="max-height: 70vh;">
	<div id="messages-template" rel="template" class="d-none">
		<a href="{6}" class="list-group-item list-group-item-action direct-message" data-b64mid="{0}">
			<div class="d-flex w-100 justify-content-between">
				<div class="mb-1 text-truncate" title="{5}">
					{7}
					<strong>{4}</strong>
				</div>
				<small class="messages-timeago text-nowrap" title="{1}"></small>
			</div>
			<div class="mb-1">
				<div class="text-break">{2}</div>
			</div>
			<small>{3}</small>
		</a>
	</div>
	<div id="dm-container" class="list-group list-group-flush" data-offset="10">
		{{foreach $entries as $e}}
		<a href="{{$e.href}}" class="list-group-item list-group-item-action direct-message" data-b64mid="{{$e.b64mid}}">
			<div class="d-flex w-100 justify-content-between">
				<div class="mb-1 text-truncate" title="{{$e.owner_addr}}">
					{{$e.icon}}
					<strong>{{$e.owner_name}}</strong>
				</div>
				<small class="messages-timeago text-nowrap" title="{{$e.created}}"></small>
			</div>
			<div class="mb-1">
				<div class="text-break">{{$e.summary}}</div>
			</div>
			<small>{{$e.recipients}}</small>
		</a>
		{{/foreach}}
		<div id="messages-loading" class="list-group-item" style="display: none;">
			{{$loading}}<span class="jumping-dots"><span class="dot-1">.</span><span class="dot-2">.</span><span class="dot-3">.</span></span>
		</div>
	</div>
</div>
<script>
	var messages_offset = {{$offset}};
	var get_messages_page_active = false;
</script>