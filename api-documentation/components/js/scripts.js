var statusCodeTemplate = `
	<div class="row">
		<div class="col-md-12">
			<div class="col-md-12 offset-md-8">
				<div class="box box-gray">
					<div class="box-header with-border">
						<div class="row box-title mt-5">
							<div class="col-xs-12">Status Code</div>
						</div>
					</div>
					<div class="box-body ml-20 mr-20">
						<div class="row mt-10">
							<div class="col-sm-12">
								<table class="table table-bordered table-hover">
									<thead class="bg-gray">
										<tr>
											<th>Code</th>
											<th>Description</th>
										</tr>
									</thead>
									<tbody>
										{{statusCode}}
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
`;

var contentPOSTTemplate = `
	<div class="row">
		<div class="col-md-12">
			<div class="col-md-12 offset-md-8">
				<div class="box box-default">
					<div class="box-header with-border">
						<div class="pl-20 pr-20 mt-5">
							<h4><span class="badge badge-primary bg-{{methodColor}}">{{methodText}}</span>&nbsp;&nbsp;&nbsp;{{urlText}}</h4>
						</div>
					</div>
					<div class="box-body ml-20 mr-20">
						<div class="row">
							<div class="col-sm-12">
								<h4 class="text-primary">Request Parameter</h4>
							</div>
						</div>
						<div class="row mt-10">
							<div class="col-sm-12">
								<h5><strong>Header</strong></h5>
							</div>
							<div class="col-sm-12">
								<table class="table table-bordered table-hover">
									<thead class="bg-gray">
										<tr>
											<th>Header Key</th>
											<th>Description</th>
										</tr>
									</thead>
									<tbody>
										{{requestHeader}}
									</tbody>
								</table>
							</div>
							<div class="col-sm-12">
								<h5><strong>Body</strong></h5>
							</div>
							<div class="col-sm-12">
								<table class="table table-bordered table-hover">
									<thead class="bg-gray">
										<tr>
											<th>Body Key</th>
											<th>Description</th>
										</tr>
									</thead>
									<tbody>
										{{requestData}}
									</tbody>
								</table>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<h5><strong>Example</strong></h5>
							</div>
							<div class="col-sm-12">
								<div class="textbox">
									<p id="{{requestSampleID}}"></p>
								</div>
							</div>
						</div>
						<hr />
						<div class="row">
							<div class="col-sm-12">
								<h4 class="text-primary">Response</h4>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<h5><strong>Response type:</strong> <span class="badge badge-warning bg-yellow">{{responseTypeText}}</span></h5>
							</div>
							<div class="col-sm-12">
								<div class="textbox">
									<p id="{{responseSampleID}}"></p>
								</div>
							</div>
						</div>
						<div class="row mt-10">
							<div class="col-sm-12">
								<table class="table table-bordered table-hover">
									<thead class="bg-gray">
										<tr>
											<th>Key</th>
											<th>Description</th>
											<th>Notes</th>
										</tr>
									</thead>
									<tbody>
										{{responseData}}
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
`;

var contentGETTemplate = `
	<div class="row">
		<div class="col-md-12">
			<div class="col-md-12 offset-md-8">
				<div class="box box-default">
					<div class="box-header with-border">
						<div class="pl-20 pr-20 mt-5">
							<h4><span class="badge badge-primary bg-{{methodColor}}">{{methodText}}</span>&nbsp;&nbsp;&nbsp;{{urlText}}</h4>
						</div>
					</div>
					<div class="box-body ml-20 mr-20">
						<div class="row">
							<div class="col-sm-12">
								<h4 class="text-primary">Request Parameter</h4>
							</div>
						</div>
						<div class="row mt-10">
							<div class="col-sm-12">
								<h5><strong>Header</strong></h5>
							</div>
							<div class="col-sm-12">
								<table class="table table-bordered table-hover">
									<thead class="bg-gray">
										<tr>
											<th>Header Key</th>
											<th>Description</th>
										</tr>
									</thead>
									<tbody>
										{{requestHeader}}
									</tbody>
								</table>
							</div>
							<div class="col-sm-12">
								<h5><strong>Opsi URL Parameter</strong></h5>
							</div>
							<div class="col-sm-12">
								<table class="table table-bordered table-hover">
									<thead class="bg-gray">
										<tr>
											<th>URL Key</th>
											<th>Description</th>
										</tr>
									</thead>
									<tbody>
										{{requestData}}
									</tbody>
								</table>
							</div>
							<div class="col-sm-12">
								<h5><strong>Example</strong></h5>
							</div>
							<div class="col-sm-12 pl-15 pr-15">
								<div class="textbox">
									<b>{{requestMeth}}</b>&nbsp;&nbsp; {{exampleRequest}}
								</div>
							</div>
						</div>
						<hr />
						<div class="row">
							<div class="col-sm-12">
								<h4 class="text-primary">Response</h4>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<h5><strong>Response type:</strong> <span class="badge badge-warning bg-yellow">{{responseTypeText}}</span></h5>
							</div>
							<div class="col-sm-12">
								<div class="textbox">
									<p id="{{responseSampleID}}"></p>
								</div>
							</div>
						</div>
						<div class="row mt-10">
							<div class="col-sm-12">
								<table class="table table-bordered table-hover">
									<thead class="bg-gray">
										<tr>
											<th>Key</th>
											<th>Description</th>
											<th>Notes</th>
										</tr>
									</thead>
									<tbody>
										{{responseData}}
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
`;

var noContentTemplate = `
	<div class="row">
		<div class="col-md-12 mt-150">
			<center>
				<img src="./components/images/not-found.png" alt="No Content" width="500" />
			</center>
		</div>
	</div>
`;

var failedLoadContent = `
	<div class="textbox noContent">
		Gagal load data.<br />
		Pastikan mengakses halaman ini menggunakan php serve, bukan akses langsung path file <b>index.html</b>-nya.<br />
		Halaman ini juga sudah online dan dapat diakses melalui: <a href="{{urlWebHref}}">{{urlWebText}}</a>
	</div>
`;

var homeTemplate = `
<div class="row">
	<div class="col-md-12">
		<div class="col-md-12 offset-md-8">
			<div class="box box-success">
				<div class="box-header with-border">
					<div class="pl-20 pr-20 mt-5">
						<div class="row">
							<div class="col-xs-6">
								<h4>API BG Skin Documentation</h4>
							</div>
							<div class="col-xs-6 text-right">
								<a href="{{statusCodeLink}}" class="btn btn-success">Status Code</a>
							</div>
						</div>
						
					</div>
				</div>
				<div class="box-body ml-20 mr-20">
					Silahkan gunakan dokumentasi API disini untuk melakukan integrasi aplikasi BG Skin Online BG Skin lainnya.<br />
					Untuk url endpoint (baik staging maupun production), silahkan tanyakan langsung pada si pembuat API. <br>
					<p><strong>Tujuan pengembangan web service BG Skin ini adalah sebagai berikut:</strong></p>
<ol>
<li>Media integrasi data antar aplikasi Bg Skin.</li>
</ol>
<span class=\"badge badge-warning\">Aplikasi BG Skin harus menyamakan pengkodean data referensi terlebih dahulu</span>
					<div class="pb-40" />
					Last Updated: <b class="text-danger">{{lastUpdated}}</b><br /><br />
					
					<a href="#" class="btn btn-warning" target="_blank">Postman Collection</a>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="col-md-12 offset-md-8">
			<div class="box box-danger">
				<div class="box-header with-border">
					<div class="pl-20 pr-20 mt-5">
						<div class="row">
							<div class="col-xs-6">
								<h4>UPDATE NOTES</h4>
							</div>
						</div>
						
					</div>
				</div>
				<div class="box-body ml-20 mr-20">
					{{updateNotes}}
				</div>
			</div>
		</div>
	</div>
</div>
`;
//<a href="{{postmanLink}}" class="btn btn-warning" target="_blank">Postman Collection</a>

var titleComponent = `
	<div class="row">
		<div class="col-md-12">
			<div class="col-md-12 offset-md-8">
			<h4><span class="callout callout-danger"><i class="fa fa-bookmark"></i>&nbsp; {{title}}</span></h4>
			</div>
		</div>
	</div>
`;

$(document).ready(function () {
	$.getJSON('content.json', function(content) {
		$("#onContent").css('display', 'block');

		var queryString = window.location.search;
		var urlParams = new URLSearchParams(queryString);
		var slug = urlParams.get('slug');

		content.menu.map((value, key) => {
			var component = `<li class="header" id="menu` + key + `">` + value.section + `</li>`;

			value.data.map((v, k) => {
				if (slug === null) {
					slug = 'home';
				}

				var icon = 'fa-chevron-right';
				if (content.available_indicator === true) {
					icon = 'fa-circle';
					if (content.data.filter(i => i.section === v.slug).length > 0) {
						icon += ' text-success';
					} else {
						icon += ' text-danger';
					}
				}

				component += `<li class="header` + (slug === v.slug ? ' active' : '') + `" id="submenu` + k + `" style="padding: 0;"><a href="?slug=` + v.slug + `"><i class="fa ` + icon + `"></i>` + v.title + `</a></li>`
			})

			var activeHead = value.data.filter(item => item.slug === slug);
			if (activeHead.length > 0) {
				$("#headTitle").html(activeHead[0].title);
			}

			$("#sidebarMenu").append(component)
		})

		var currentData = content.data.filter(item => item.section === slug);

		// Belum ada data
		if (currentData.length === 0 && slug !== 'home') {
			$("#infoWrapper").append(noContentTemplate);
		}

		switch(slug) {
			case 'status-code':
				(
					currentData.map((value, key) => {
						var data = value.data;
						
						// Request Description
						var desc = '';
						data.status_code.map((v, k) => {
							desc += `
								<tr>
									<td>` + v.status + `</td>
									<td>` + v.desc + `</td>
								</tr>
							`
						})
						var content = statusCodeTemplate.replace('{{statusCode}}', desc);
						$("#infoWrapper").append(content);
					})
				)
				break;
			case 'home':
				// Update Notes
				var update = '<i>tidak ada catatan khusus</i>';
				if (typeof content.update_notes !== 'undefined') {
					update = '';
					content.update_notes.map(item => {
						if (update == '') { update += '<ul>'; }
						update += '<li>' + item + '</li>';
					});
					if (update != '') { update += '</ul>'; }
				}

				var statusCode = "?slug=status-code";
				var content = homeTemplate.replace('{{lastUpdated}}', content.last_updated)
					.replace('{{statusCodeLink}}', statusCode)
					.replace('{{postmanLink}}', content.postman_collection)
					.replace('{{updateNotes}}', update);
				$("#infoWrapper").append(content);
				break;
			default:
				(
					currentData.map((value, key) => {
						var data = value.data;
			
						// Decide if GET/POST
						var content = contentPOSTTemplate;
						if (data.method === 'get' || data.method === 'delete') {
							content = contentGETTemplate;
							content = content.replace('{{requestMeth}}', data.method.toUpperCase());
						}

						// Method
						content = content.replace('{{methodText}}', data.method.toUpperCase());
						// Method
						content = content.replace('{{methodColor}}', (data.method === 'get' ? 'green' : (data.method === 'delete' ? 'red' : 'yellow')));
						// URL
						content = content.replace('{{urlText}}', '<span class="text-' + (data.method === 'get' ? 'success' : (data.method === 'delete' ? 'danger' : 'warning')) + '">' + data.url + '</span>');
						// Request Header Description
						var reqHead = '';
						data.request.header.map((v, k) => {
							reqHead += `
								<tr>
									<td>` + v.key + `</td>
									<td>` + v.description + `</td>
								</tr>
							`
						})
						content = content.replace('{{requestHeader}}', reqHead);
						// Request Description
						var reqDesc = '';
						data.request.value.map((v, k) => {
							reqDesc += `
								<tr>
									<td>` + v.key + `</td>
									<td>` + v.description + `</td>
								</tr>
							`
						})

						if (data.request.example !== null && typeof data.request.example === 'string') {
							content = content.replace('{{exampleRequest}}', data.request.example);
						}

						content = content.replace('{{requestData}}', reqDesc);
						// Request Type
						content = content.replace('{{responseTypeText}}', data.response.format);
						// Request Sample
						content = content.replace('{{requestSampleID}}', 'requestSample' + key);
						// Response Sample
						content = content.replace('{{responseSampleID}}', 'responseSample' + key);
						// Response Description
						var resDesc = '';
						data.response.value.map((v, k) => {
							resDesc += `
								<tr>
									<td>` + v.key + `</td>
									<td>` + v.description + `</td>
									<td>` + (v.note !== null && v.note !== '' ? v.note : '-') + `</td>
								</tr>
							`
						})
						content = content.replace('{{responseData}}', resDesc);
						content += '<br />';

						if (value.title !== null) {
							var header = titleComponent.replace('{{title}}', value.title);
							content = header + content.replace('box-default', 'box-danger');
						}
			
						$("#infoWrapper").append(content);

						if (data.method === 'post' || data.method === 'patch') {
							$("#requestSample" + key).JSONView(JSON.stringify(data.request.example), {collapsed: false, nl2br: true, recursive_collapser: true});
						}
						$("#responseSample" + key).JSONView(JSON.stringify(data.response.example), {collapsed: false, nl2br: true, recursive_collapser: true});
					})
				)
				break;
		}
	})
	.fail(function() {
		var url = "https://pwmp-swagger.netlify.com";
		var showContent = failedLoadContent.replace('{{urlWebHref}}', url).replace('{{urlWebText}}', url);
		$("#noContent").append(showContent);
		$("#noContent").css('display', 'block');
	});
});