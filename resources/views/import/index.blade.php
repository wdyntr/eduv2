{{-- resources/views/import/index.blade.php --}}
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

@if(session('success'))
    <div style="color:green">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div style="color:red">{{ $errors->first() }}</div>
@endif

{{-- 1 form, 1 file ODS --}}
<form action="/import/upload" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file" accept=".ods,.xlsx,.xls">
    <button type="submit">Import</button>
</form>

</body>