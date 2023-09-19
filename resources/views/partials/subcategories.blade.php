@foreach($subcategories as $subcategory)
    <li>
        <span data-id="{{ $subcategory->id }}" class="folder">{{ $subcategory->title }}</span>
        @if(count($subcategory->subcategories) > 0)
            <span class="caret">{{ $subcategory->title }}</span>
            <ul class="nested">
                @include('partials.subcategories', ['subcategories' => $subcategory->subcategories])
            </ul>
        @endif
    </li>
@endforeach
