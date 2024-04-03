@extends('layouts.frontend.app')

@section('content')

<section class="container mx-auto px-6 mb-14">
  <div class="bg-gray-300 h-56 w-full relative">
    <img src="https://picsum.photos/400/400" alt="cover" class="absolute -bottom-12 left-10 rounded-xl w-48 h-48 object-cover">
  </div>
</section>

@include('modules.includes.menu')

<section class="container mx-auto px-6">
  <h3 class="text-lg font-semibold text-gray-700 mb-2">About</h3>

  <p class="text-sm text-gray-700">
    The Facebook company is now Meta. Meta builds technologies that help people connect, find communities, and grow businesses. When Facebook launched in 2004, it changed the way people connect. Apps like Messenger, Instagram and WhatsApp further empowered billions around the world. Now, Meta is moving beyond 2D screens toward immersive experiences like augmented and virtual reality to help build the next evolution in social technology.

    We want to give people the power to build community and bring the world closer together. To do that, we ask that you help create a safe and respectful online space. These community values encourage constructive conversations on this page:
  </p>
</section>
@endsection