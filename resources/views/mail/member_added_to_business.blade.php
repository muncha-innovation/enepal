<h1>Welcome, {{$user->name}}</h1>

<p>You have been added to {{$business->name}} as a {{$role}}.</p>

@if($user->force_update_password)
<p>
    Please login to your account and update your password. Your temporary password is <strong>{{$password}}</strong>
</p>
<a href="{{route('login')}}">{{__('Login')}}</a>
@endif

<p>Thank you</p>
<p>Enepali</p>