<div class="card mb-3 h-card">
	<div class="row">
		<div class="col-4">
			<a href="{{$link}}" >
				<img class="u-photo" src="{{$photo}}" alt="{{$name}}" width="80px" height="80px">
			</a>
		</div>
		<div class="col-7 m-1">
			<div>
				<strong class="fn p-name text-truncate">{{$name}}</strong>
			</div>
			<div>
				<small class="text-muted p-adr text-truncate">{{$addr}}</small>
			</div>
			{{if $connect}}
			<div class="mt-1">
				<a href="follow?f=&url={{$follow}}&interactive=1" class="btn btn-success btn-sm" rel="nofollow">
					<i class="fa fa-plus"></i> {{$connect}}
				</a>
			</div>
			{{/if}}
		</div>
	</div>
</div>

