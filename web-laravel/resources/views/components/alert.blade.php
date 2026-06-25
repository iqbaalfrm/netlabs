@if (session('success'))
<div class="mx-8 mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative flex items-center gap-3">
    <i class="fas fa-check-circle text-green-500 text-lg"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if (session('error'))
<div class="mx-8 mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative flex items-center gap-3">
    <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

@if ($errors->any())
<div class="mx-8 mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
    <ul class="list-disc list-inside">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif