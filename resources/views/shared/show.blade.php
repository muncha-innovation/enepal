@extends('layouts.app')
@section('content')
  <h1>{{ $model->name }}</h1>
  <table class="table-auto">
    <tbody>
      @foreach ($model->getAttributes() as $key => $value)
        <tr class="border-black">
          <th class="border-black">{{ $key }}</th>
          <td class="border-black">{{ $value }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endsection
