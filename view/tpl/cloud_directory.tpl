<div id="cloud-drag-area" class="section-content-wrapper-np">
{{if $tiles}}
	<table id="cloud-index">
		<tr id="new-upload-progress-bar-1"></tr> {{* this is needed to append the upload files in the right order *}}
	</table>

	{{if $parentpath}}
	<div class="cloud-container" >

	<div class="cloud-icon tiles"><a href="{{$parentpath.path}}">
	<div class="cloud-icon-container"><i class="fa fa-fw fa-level-up" ></i></div>
	</a>
	</div>
	<div class="cloud-title"><a href="{{$parentpath.path}}">..</a>
	</div>
	</div>
	{{/if}}

	{{foreach $entries as $item}}
	<div class="cloud-container">
	<div class="cloud-icon tiles"><a href="{{$item.relPath}}">
	{{if $item.photo_icon}}
	<img src="{{$item.photo_icon}}" title="{{$item.type}}" >
	{{else}}
	<div class="cloud-icon-container"><i class="fa fa-fw {{$item.iconFromType}}" title="{{$item.type}}"></i></div>
	{{/if}}
	</a>
	</div>
	<div class="cloud-title"><a href="{{$item.relPath}}">
	{{$item.displayName}}
	</a>
	</div>
	{{if $item.is_owner}}

	{{/if}}
	</div>
	{{/foreach}}
	<div class="clear"></div>
{{else}}
	<table id="cloud-index">
		<tr>
			<th width="1%"></th>
			<th width="92%">{{$name}}</th>
			<th width="1%"></th><th width="1%"></th><th width="1%"></th><th width="1%"></th>
			<th width="1%">{{*{{$type}}*}}</th>
			<th width="1%" class="d-none d-md-table-cell">{{$size}}</th>
			<th width="1%" class="d-none d-md-table-cell">{{$lastmod}}</th>
		</tr>
	{{if $parentpath}}
		<tr>
			<td><i class="fa fa-level-up"></i>{{*$parentpath.icon*}}</td>
			<td><a href="{{$parentpath.path}}" title="{{$parent}}">..</a></td>
			<td></td><td></td><td></td><td></td>
			<td>{{*[{{$parent}}]*}}</td>
			<td class="d-none d-md-table-cell"></td>
			<td class="d-none d-md-table-cell"></td>
		</tr>
	{{/if}}
		<tr id="new-upload-progress-bar-1"></tr> {{* this is needed to append the upload files in the right order *}}
	{{foreach $entries as $item}}
		<tr id="cloud-index-{{$item.attachId}}">
			<td><i class="fa {{$item.iconFromType}}" title="{{$item.type}}"></i></td>
			<td><a href="{{$item.relPath}}">{{$item.displayName}}</a></td>
	{{if $item.is_owner}}
			<td class="cloud-index-tool">{{$item.attachIcon}}</td>
			<td class="cloud-index-tool"><div id="file-edit-{{$item.attachId}}" class="spinner-wrapper"><div class="spinner s"></div></div></td>
			<td class="cloud-index-tool cursor-pointer" onclick="openCloseTR('cloud-tools-{{$item.attachId}}');"><i class="fa fa-pencil"></i></td>
			<td class="cloud-index-tool"><a href="#" title="{{$delete}}" onclick="dropItem('{{$item.fileStorageUrl}}/{{$item.attachId}}/delete/json', '#cloud-index-{{$item.attachId}},#cloud-tools-{{$item.attachId}}'); return false;"><i class="fa fa-trash-o drop-icons"></i></a></td>

	{{else}}
			<td></td><td></td><td></td>{{if ($is_admin || $item.is_creator) && $item.attachId}}<td class="cloud-index-tool"><a href="#" title="{{if $is_admin}}{{$admin_delete}}{{else}}{{$delete}}{{/if}}" onclick="dropItem('{{$item.fileStorageUrl}}/{{$item.attachId}}/delete/json', '#cloud-index-{{$item.attachId}},#cloud-tools-{{$item.attachId}}'); return false;"><i class="fa fa-trash-o drop-icons"></i></a>{{else}}<td>{{/if}}</td>
	{{/if}}
			<td>{{*{{$item.type}}*}}</td>
			<td class="d-none d-md-table-cell">{{$item.sizeFormatted}}</td>
			<td class="d-none d-md-table-cell">{{$item.lastmodified}}</td>
		</tr>
		<tr id="cloud-tools-{{$item.attachId}}" class="cloud-tools">
			<td id="attach-edit-panel-{{$item.attachId}}" colspan="9">
				<form id="attach_edit_form_{{$item.attachId}}" action="attach_edit/{{$nick}}/{{$item.attachId}}" method="post" class="acl-form" data-form_id="attach_edit_form_{{$item.attachId}}" data-allow_cid='{{$item.allow_cid}}' data-allow_gid='{{$item.allow_gid}}' data-deny_cid='{{$item.deny_cid}}' data-deny_gid='{{$item.deny_gid}}'>
					<input type="hidden" name="attach_id" value="{{$item.attachId}}" />
					<input type="hidden" name="resource" value="{{$item.resource}}" />
					<input type="hidden" name="filename" value="{{$item.displayName}}" />
					<input type="hidden" name="folder" value="{{$item.folder}}" />
					{{include file="field_input.tpl" field=$item.newfilename}}
					{{include file="field_select.tpl" field=$item.newfolder}}
					{{include file="field_checkbox.tpl" field=$item.copy}}
					{{if !$item.collection}}{{include file="field_checkbox.tpl" field=$item.notify}}{{/if}}
					{{if $item.collection}}{{include file="field_checkbox.tpl" field=$item.recurse}}{{/if}}
					<div id="attach-edit-tools-share" class="btn-group form-group">
						{{if !$item.collection}}
						<a href="/rpost?attachment=[attachment]{{$item.resource}},{{$item.revision}}[/attachment]" id="attach-btn" class="btn btn-outline-secondary btn-sm" title="{{$attach_btn_title}}">
							<i class="fa fa-share-square-o jot-icons"></i>
						</a>
						{{/if}}
						<button id="link-btn-{{$item.attachId}}" class="btn btn-outline-secondary btn-sm" type="button" onclick="openClose('link-code-{{$item.attachId}}');" title="{{$link_btn_title}}">
							<i class="fa fa-link jot-icons"></i>
						</button>
					</div>
					<div id="attach-edit-perms" class="btn-group pull-right">
						<button id="dbtn-acl-{{$item.attachId}}" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#aclModal" title="{{$permset}}" type="button">
							<i id="jot-perms-icon-{{$item.attachId}}" class="fa fa-{{$item.lockstate}} jot-icons jot-perms-icon"></i>
						</button>
						<button id="dbtn-submit-{{$item.attachId}}" class="btn btn-primary btn-sm" type="submit" name="submit">
							{{$edit}}
						</button>
					</div>
					<div id="link-code-{{$item.attachId}}" class="form-group link-code">
						<label for="">{{$cpldesc}}</label>
						<input type="text" class="form-control" id="linkpasteinput" name="cutpasteextlink" value="{{$item.fullPath}}" onclick="this.select();"/>
					</div>
				</form>
			</td>
		</tr>
	{{/foreach}}
	</table>
{{/if}}
</div>
