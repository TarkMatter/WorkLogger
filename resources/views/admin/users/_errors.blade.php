@if ($errors->any())
    <div class="p-3 border rounded-md">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif
