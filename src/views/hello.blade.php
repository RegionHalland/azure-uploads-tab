<table class="wp-list-table widefat fixed striped media">
	<thead>
	<tr>
		<td id="cb" class="manage-column column-cb check-column">
            <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
            <input id="cb-select-all-1" type="checkbox"></td><th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
                <a href="//localhost:3000/wp/wp-admin/upload.php?orderby=title&amp;order=asc"><span>File</span><span class="sorting-indicator"></span></a></th><th scope="col" id="author" class="manage-column column-author sortable desc"><a href="//localhost:3000/wp/wp-admin/upload.php?orderby=author&amp;order=asc"><span>Author</span><span class="sorting-indicator"></span></a></th><th scope="col" id="parent" class="manage-column column-parent sortable desc"><a href="//localhost:3000/wp/wp-admin/upload.php?orderby=parent&amp;order=asc"><span>Uploaded to</span><span class="sorting-indicator"></span></a></th><th scope="col" id="comments" class="manage-column column-comments num sortable desc"><a href="//localhost:3000/wp/wp-admin/upload.php?orderby=comment_count&amp;order=asc"><span><span class="vers comment-grey-bubble" title="Comments"><span class="screen-reader-text">Comments</span></span></span><span class="sorting-indicator"></span></a></th><th scope="col" id="date" class="manage-column column-date sortable asc"><a href="//localhost:3000/wp/wp-admin/upload.php?orderby=date&amp;order=desc"><span>Date</span><span class="sorting-indicator"></span></a></th>	</tr>
	</thead>
	<tbody id="the-list">

    @foreach($blobs as $key => $blob)
        <tr id="post-377" class="author-other status-inherit">
        <th scope="row" class="check-column">
        </th>
        <td class="title column-title has-row-actions column-primary" data-colname="File">		<strong class="has-media-icon">
        <a href="{{$blob->getUrl()}}" aria-label="“{{$blob->getName()}}” (Edit)">
            <span class="media-icon application-icon">
                <img width="48" height="64" src="//localhost:3000/wp/wp-includes/images/media/document.png" class="attachment-60x60 size-60x60" alt="">
            </span>
            {{$blob->getName()}}</a></strong>
            <p class="filename">
            <span class="screen-reader-text">File name: </span>
            {{$blob->getUrl()}}</p>
            </th>
    @endforeach


</tbody>
    <tfoot>
	<tr>
		<td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">Select All</label><input id="cb-select-all-2" type="checkbox"></td><th scope="col" class="manage-column column-title column-primary sortable desc"><a href="//localhost:3000/wp/wp-admin/upload.php?orderby=title&amp;order=asc"><span>File</span><span class="sorting-indicator"></span></a></th><th scope="col" class="manage-column column-author sortable desc"><a href="//localhost:3000/wp/wp-admin/upload.php?orderby=author&amp;order=asc"><span>Author</span><span class="sorting-indicator"></span></a></th><th scope="col" class="manage-column column-parent sortable desc"><a href="//localhost:3000/wp/wp-admin/upload.php?orderby=parent&amp;order=asc"><span>Uploaded to</span><span class="sorting-indicator"></span></a></th><th scope="col" class="manage-column column-comments num sortable desc"><a href="//localhost:3000/wp/wp-admin/upload.php?orderby=comment_count&amp;order=asc"><span><span class="vers comment-grey-bubble" title="Comments"><span class="screen-reader-text">Comments</span></span></span><span class="sorting-indicator"></span></a></th><th scope="col" class="manage-column column-date sortable asc"><a href="//localhost:3000/wp/wp-admin/upload.php?orderby=date&amp;order=desc"><span>Date</span><span class="sorting-indicator"></span></a></th>	</tr>
	</tfoot>
</table>




