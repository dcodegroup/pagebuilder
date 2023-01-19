<x-admin-layout>
@section('title', 'Revisions for page ' . $page->title)

<a href="{{ route(\Dcodegroup\PageBuilder\Routes::admin('pages.edit'), $page) }}" class="button">
    Back to page
</a>

@if ($revisions->isNotEmpty())
    <table>
        <thead>
        <tr>
            <th>Content</th>
            <th>Updated</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($revisions as $revision)
            <tr>
                <td>
                    <h1>{{ $revision->title }}</h1>
                    <hr />
                    @if ($revision->abstract)
                        <h5>Abstract</h5>
                        {{ $revision->abstract }}
                        <hr />
                    @endif
                    <h5>Content</h5>
                    {!! $page->render() !!}
                </td>
                <td>{!! nl2br($revision->updatedByForHumans) !!}</td>
                <td>
                    <div class="button-group">
                        {{ Form::open(['route' => [\Dcodegroup\PageBuilder\Routes::admin('pages.revisions.restore'), $revision], 'method' => 'put']) }}
                            <button type="submit" class="button success">Restore</button>
                        {{ Form::close() }}
                        @include('page-builder::_partials.delete-confirm', [
                            'route' => \Dcodegroup\PageBuilder\Routes::admin('pages.revisions.destroy'),
                            'object' => $revision,
                            'label' => 'this page revision',
                            'type' => 'revision'
                        ])
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $revisions->appends(request()->except(['page','_token']))->links() }}
@else
    @include('page-builder::_partials.components.no-results', ['label' => 'pages'])
@endif
</x-admin-layout>
