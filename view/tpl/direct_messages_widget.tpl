<div id="dm-widget" class="border rounded-2 overflow-auto" style="max-height: 70vh;">
	<div id="direct-message-template" rel="template" class="d-none">
		<a href="dm/{0}" class="list-group-item list-group-item-action direct-message" data-b64mid="{0}">
			<div class="d-flex w-100 justify-content-between">
				<div class="mb-1"><strong>{1}</strong></div>
				<small class="direct-message-timeago text-nowrap" title="{2}">{2}</small>
			</div>
			<div class="mb-1">{3}</div>
			<small>
				{4}
			</small>
		</a>
	</div>
	<div id="dm-container" class="list-group list-group-flush" data-offset="10">
		{{foreach $entries as $e}}
		<a href="dm/{{$e.b64mid}}" class="list-group-item list-group-item-action direct-message" data-b64mid="{{$e.b64mid}}">
			<div class="d-flex w-100 justify-content-between">
				<div class="mb-1"><strong>{{$e.subject}}</strong></div>
				<small class="direct-message-timeago text-nowrap" title="{{$e.created}}"></small>
			</div>
			<div class="mb-1">{{$e.summary}}</div>
			<small>
				{{$e.recipients}}
			</small>
		</a>
		{{/foreach}}
		<div id="dm-loading" class="list-group-item" style="display: none;">
			{{$loading}}<span class="jumping-dots"><span class="dot-1">.</span><span class="dot-2">.</span><span class="dot-3">.</span></span>
		</div>
	</div>
</div>
<script>
	var dm_last_id = {{$last_id}};
	var get_dm_page_active = false;
</script>
