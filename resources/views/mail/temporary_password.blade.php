<h1>Hello, {{$user->name}}</h1>

<p>
    Please login to your account and update your password. Your temporary password is <strong>{{$password}}</strong>
</p>
<a href="{{route('login')}}">{{__('Login')}}</a>

<p>Thank you</p>
<p>Enepali</p>