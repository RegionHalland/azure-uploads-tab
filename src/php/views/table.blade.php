<div class="wrap">
	<form method="post">
		{{ $listTable->search_box('Sök', 'aut-search') }}
	</form>
	{{ $listTable->display() }}
</div>
