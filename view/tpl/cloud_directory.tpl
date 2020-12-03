<div id="cloud-drag-area" class="section-content-wrapper-np">
	{{if $tiles}}
	<table id="cloud-index">
		<tr id="new-upload-progress-bar-1"></tr> {{* this is needed to append the upload files in the right order *}}
	</table>

	{{if $parentpath}}
	<div class="cloud-container" >
		<div class="cloud-icon tiles">
			<a href="{{$parentpath.path}}">
				<div class="cloud-icon-container">
					<i class="fa fa-fw fa-level-up" ></i>
				</div>
			</a>
		</div>
		<div class="cloud-title">
			<a href="{{$parentpath.path}}">..</a>
		</div>
	</div>
	{{/if}}

	{{foreach $entries as $item}}
	<div class="cloud-container">
		<div class="cloud-icon tiles"><a href="{{$item.relPath}}">
		{{if $item.photo_icon}}
		<img src="{{$item.photo_icon}}" title="{{$item.type}}" >
		{{else}}
		<div class="cloud-icon-container">
			<i class="fa fa-fw {{$item.iconFromType}}" title="{{$item.type}}"></i>
		</div>
		{{/if}}
		</div>
		<div class="cloud-title">
			<a href="{{$item.relPath}}">
				{{$item.displayName}}
			</a>
		</div>
		{{if $item.is_owner}}
			{{* add file tools here*}}
		{{/if}}
	</div>
	{{/foreach}}
	<div class="clear"></div>
	{{else}}

	<table id="cloud-index">
		<tr>
			<th width="1%">{{* icon *}}</th>
			<th width="94%">{{$name}}</th>
			<th width="1%">{{* categories *}}</th>
			<th width="1%">{{* lock icon *}}</th>
			<th width="1%">{{* tools icon *}}</th>
			<th width="1%" class="d-none d-md-table-cell">{{$size}}</th>
			<th width="1%" class="d-none d-md-table-cell">{{$lastmod}}</th>
		</tr>
		{{if $parentpath}}
		<tr id="cloud-index-0">
			<td><i class="fa fa-level-up"></i>{{*$parentpath.icon*}}</td>
			<td colspan="7"><a href="{{$parentpath.path}}" title="{{$parent}}">..</a></td>
		</tr>
		{{/if}}
		<tr id="new-upload-progress-bar-1"></tr> {{* this is needed to append the upload files in the right order *}}
		{{foreach $entries as $item}}
		<tr id="cloud-index-{{$item.attachId}}" class="cloud-index">
			<td><i class="fa {{$item.iconFromType}}" title="{{$item.type}}"></i></td>
			<td><a href="{{$item.relPath}}" class="p-2">{{$item.displayName}}</a></td>
			<td>{{$item.terms}}</td>
			<td class="cloud-index-tool p-2">{{if $item.lockstate == 'lock'}}<i class="fa fa-fw fa-{{$item.lockstate}}"></i>{{/if}}</td>
			{{if $item.is_owner}}
			<td class="cloud-index-tool">
				<div class="dropdown">
					<button class="btn btn-link btn-sm" id="dropdown-button-{{$item.attachId}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fa fa-ellipsis-v"></i>
					</button>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-button-{{$item.attachId}}">
						<a id="cloud-tool-perms-btn-{{$item.attachId}}" class="dropdown-item cloud-tool-perms-btn" href="#" data-id="{{$item.attachId}}"><i class="fa fa-fw fa-{{$item.lockstate}}"></i> Adjust permissions</a>
						<a id="cloud-tool-rename-btn-{{$item.attachId}}" class="dropdown-item cloud-tool-rename-btn" href="#" data-id="{{$item.attachId}}"><i class="fa fa-fw fa-pencil"></i> Rename</a>
						<a id="cloud-tool-move-btn-{{$item.attachId}}" class="dropdown-item cloud-tool-move-btn" href="#" data-id="{{$item.attachId}}"><i class="fa fa-fw fa-copy"></i> Move or copy</a>
						{{if !$item.collection}}
						<a id="cloud-tool-share-btn-{{$item.attachId}}" class="dropdown-item cloud-tool-share-btn" href="/rpost?attachment=[attachment]{{$item.resource}},{{$item.revision}}[/attachment]&acl[allow_cid]={{$item.raw_allow_cid}}&acl[allow_gid]={{$item.raw_allow_gid}}&acl[deny_cid]={{$item.raw_deny_cid}}&acl[deny_gid]={{$item.raw_deny_gid}}" data-id="{{$item.attachId}}"><i class="fa fa-fw fa-share-square-o"></i> Post</a>
						<a id="cloud-tool-download-btn-{{$item.attachId}}" class="dropdown-item cloud-tool-download-btn" href="/attach/{{$item.resource}}" data-id="{{$item.attachId}}"><i class="fa fa-fw fa-cloud-download"></i> Download</a>
						{{/if}}
						<a id="cloud-tool-delete-btn-{{$item.attachId}}" class="dropdown-item cloud-tool-delete-btn" href="#" data-id="{{$item.attachId}}" onclick="dropItem('{{$item.fileStorageUrl}}/{{$item.attachId}}/delete/json', '#cloud-index-{{$item.attachId}},#cloud-tools-{{$item.attachId}}'); return false;"><i class="fa fa-fw fa-trash-o"></i> {{$delete}}</a>
					</div>
				</div>
			</td>
			{{else}}
			<td class="cloud-index-tool">
				{{if $item.is_creator || $is_admin || !$item.collection}}
				<div class="dropdown">
					<button class="btn btn-link btn-sm" id="dropdown-button-{{$item.attachId}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fa fa-ellipsis-v"></i>
					</button>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-button-{{$item.attachId}}">
						{{if !$item.collection}}
						<a id="cloud-tool-download-btn-{{$item.attachId}}" class="dropdown-item cloud-tool-download-btn" href="/attach/{{$item.resource}}" data-id="{{$item.attachId}}"><i class="fa fa-fw fa-cloud-download"></i> Download</a>
						{{/if}}
						{{if $item.is_creator || $is_admin}}
						<a id="cloud-tool-delete-btn-{{$item.attachId}}" class="dropdown-item cloud-tool-delete-btn" href="#" data-id="{{$item.attachId}}" onclick="dropItem('{{$item.fileStorageUrl}}/{{$item.attachId}}/delete/json', '#cloud-index-{{$item.attachId}},#cloud-tools-{{$item.attachId}}'); return false;"><i class="fa fa-fw fa-trash-o"></i> {{if $item.is_creator}}{{$delete}}{{else}}{{$admin_delete}}{{/if}}</a>
						{{/if}}
					</div>
				</div>
				{{/if}}
			</td>
			{{/if}}
			<td class="d-none d-md-table-cell p-2">{{$item.sizeFormatted}}</td>
			<td class="d-none d-md-table-cell p-2">{{$item.lastmodified}}</td>
		</tr>
		<tr id="cloud-tools-{{$item.attachId}}" class="cloud-tools">
			<td id="attach-edit-panel-{{$item.attachId}}" colspan="7">
				<form id="attach_edit_form_{{$item.attachId}}" action="attach_edit/{{$nick}}/{{$item.attachId}}" method="post" class="acl-form" data-form_id="attach_edit_form_{{$item.attachId}}" data-allow_cid='{{$item.allow_cid}}' data-allow_gid='{{$item.allow_gid}}' data-deny_cid='{{$item.deny_cid}}' data-deny_gid='{{$item.deny_gid}}'>
					<input type="hidden" name="attach_id" value="{{$item.attachId}}" />
					<input type="hidden" name="resource" value="{{$item.resource}}" />
					<input type="hidden" name="filename" value="{{$item.displayName}}" />
					<input type="hidden" name="folder" value="{{$item.folder}}" />
					<div id="cloud-tool-rename-{{$item.attachId}}" class="cloud-tool">
						{{include file="field_input.tpl" field=$item.newfilename}}
					</div>
					<div id="cloud-tool-move-{{$item.attachId}}" class="cloud-tool">
						{{include file="field_select.tpl" field=$item.newfolder}}
						{{include file="field_checkbox.tpl" field=$item.copy}}
					</div>
					<div id="cloud-tool-submit-{{$item.attachId}}" class="cloud-tool">
						{{if !$item.collection}}{{include file="field_checkbox.tpl" field=$item.notify}}{{/if}}
						{{if $item.collection}}{{include file="field_checkbox.tpl" field=$item.recurse}}{{/if}}
						{{include file="field_input.tpl" field=$item.categories}}
						<div id="attach-submit-{{$item.attachId}}" class="form-group">
							<button id="cloud-tool-cancel-btn-{{$item.attachId}}" class="btn btn-outline-secondary btn-sm cloud-tool-cancel-btn" type="button" data-id="{{$item.attachId}}">
									Cancel
							</button>
							<div id="attach-edit-perms-{{$item.attachId}}" class="btn-group float-right">
								<button id="dbtn-acl-{{$item.attachId}}" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#aclModal" title="{{$permset}}" type="button">
									<i id="jot-perms-icon-{{$item.attachId}}" class="fa fa-{{$item.lockstate}} jot-icons jot-perms-icon"></i>
								</button>
								<button id="dbtn-submit-{{$item.attachId}}" class="btn btn-primary btn-sm" type="submit" name="submit">
									{{$edit}}
								</button>
							</div>
						</div>
					</div>
					<div id="cloud-tool-categories-{{$item.attachId}}" class="">

					</div>
					<!--div id="cloud-tool-share-{{$item.attachId}}" class="">
						<div id="attach-edit-tools-share-{{$item.attachId}}" class="btn-group form-group">
							<button id="link-btn-{{$item.attachId}}" class="btn btn-outline-secondary btn-sm" type="button" onclick="openClose('link-code-{{$item.attachId}}');" title="{{$link_btn_title}}">
								<i class="fa fa-link jot-icons"></i>
							</button>
						</div>
					</div>
					{{if !$item.collection}}
					<a href="/rpost?attachment=[attachment]{{$item.resource}},{{$item.revision}}[/attachment]" id="attach-btn" class="btn btn-outline-secondary btn-sm" title="{{$attach_btn_title}}">
						<i class="fa fa-share-square-o jot-icons"></i>
					</a>
					{{/if}}
					<div id="link-code-{{$item.attachId}}" class="form-group link-code">
						<label for="linkpasteinput-{{$item.attachId}}">{{$cpldesc}}</label>
						<input type="text" class="form-control" id="linkpasteinput-{{$item.attachId}}" name="linkpasteinput-{{$item.attachId}}" value="{{$item.fullPath}}" onclick="this.select();"/>
					</div-->
				</form>
			</td>
		</tr>
		{{/foreach}}
	</table>
{{/if}}
</div>
