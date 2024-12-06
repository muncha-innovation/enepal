@if ($errors->any())
  <div class="mb-10">
    <ul>
      @foreach ($errors->all() as $error)
        <li class="text-red-500">{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
@if (session()->has('error'))
  <div class="mb-10">
    <ul>
      <li class="text-red-500">{{ session()->get('error') }}</li>
    </ul>
  </div>
@endif
@if (isset($success))
  <div class="mb-10 text-green-500">
    <ul>
      <li class="text-white">{{ $success }}</li>
    </ul>
  </div>
@endif
@if (session()->has('status'))
  <div class="mb-10 text-green-500">
    <ul>
      <li class="text-green-500">{{ session()->get('status') }}</li>
    </ul>
  </div>
@endif
