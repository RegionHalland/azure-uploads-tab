<table class="wp-list-table widefat fixed striped media">
	<thead>
		<tr>
			<th scope="col" class="manage-column column-title column-primary">
				<span>File</span>
			</th>
			<th scope="col" class="manage-column desc">
				<span>Länk</span>
			</th>
		</tr>
	</thead>
	<tbody id="the-list">
		@foreach($blobs as $key => $blob)
		<tr class="author-other">
			<td>
				<a href="{{$blob->getUrl()}}" aria-label="{{$blob->getName()}}">
					<strong>{{$blob->getName()}}</strong>
				</a>
			</td>
			<td>
				<code>{{$blob->getUrl()}}</code>
			</td>
		</tr>
		@endforeach
	</tbody>
	<tfoot>
		<tr>
			<th scope="col" class="manage-column column-title column-primary">
				<span>File</span>
			</th>
			<th scope="col" class="manage-column desc">
				<span>Länk</span>
			</th>
		</tr>
	</tfoot>
</table>