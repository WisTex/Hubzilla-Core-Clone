<form>
	{{foreach $templates as $template}}
	<div class="form-check">
		<input class="form-check-input" type="radio" name="template" id="id_template_{{$template}}" value="{{$template}}">
		<label class="form-check-label" for="id_template_{{$template}}">
			{{$template}}
		</label>
	</div>
	{{/foreach}}
</form>
