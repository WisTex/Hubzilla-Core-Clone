<div class="generic-content-wrapper">
	<div class="section-title-wrapper">
		<h2>{{$ptitle}}</h2>
	</div>
	{{$nickname_block}}
	<form action="settings/privacy" id="settings-form" method="post" autocomplete="off">
		<input type='hidden' name='form_security_token' value='{{$form_security_token}}' />

		<div class="section-content-tools-wrapper">

			{{if $permission_limits}}
								<div class="multi-collapse collapse show">
									<h2 class="text-danger mb-3"><i class="fa fa-warning"></i> {{$permission_limits_warning.0}}</h2>
									<h3 class="mb-3">{{$permission_limits_warning.1}}</h3>
									<button type="button" class="btn btn-primary"  data-bs-toggle="collapse" data-bs-target=".multi-collapse" aria-expanded="false" aria-controls="collapseExample">{{$permission_limits_warning.2}}</button>
								</div>
								<div class="multi-collapse collapse">
								{{foreach $permiss_arr as $permit}}
									{{include file="field_select.tpl" field=$permit}}
								{{/foreach}}
								<div class="settings-submit-wrapper" >
									<button type="submit" name="submit" class="btn btn-primary">{{$submit}}</button>
								</div>
								</div>
			{{/if}}

		</div>
	</form>
</div>
