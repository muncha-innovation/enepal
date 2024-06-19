@extends('layouts.app')
@section('css')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
    <section>
        <div class="bg-white p-4 shadow rounded">
            <form class="space-y-6" action="{{route('admin.settings.index')}}" method="POST">
                @csrf
                
                @include('modules.shared.success_error')
            

                <div class="mb-2">
                    
    
                    @foreach ($settings as $setting)
                    <div class="mb-2">
                        <div class="mb-2">
                            <label for="{{$setting->key}}"
                                class="block text-sm font-medium leading-6 text-gray-900">{{ __($setting->key) }}</label>
                            <div class="mt-2 rounded-md shadow-sm">
                                <input type="text" name="{{$setting->key}}" id="{{$setting->key}}"
                                    value="{{ $setting->value }}"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>
                    </div>


                    
                @endforeach
                    <div class="flex justify-end w-full">
                        <div>
                            <button type="submit"
                                class="inline-block w-full px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Save</button>
                        </div>
                    </div>
            </form>
        </div>
    </section>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2();
    });
</script>
@endpush
