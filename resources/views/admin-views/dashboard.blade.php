@extends('layouts.app')


@section('content')
  @if (session('error'))
    <div class="alert alert-error">
      {{ session('error') }}
    </div>
  @endif

@endsection
