@extends('layouts.frontend.app')

@section('content')

<section class="container mx-auto px-6 mb-14">
  <div class="bg-gray-300 h-56 w-full relative bg-center" style="background-image: url('https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D')">
    <img src="https://media-cdn.tripadvisor.com/media/photo-p/2b/77/0d/c6/caption.jpg" alt="cover" class="absolute -bottom-12 left-10 rounded-xl w-48 h-48 object-cover">
  </div>
</section>

<div class="mb-4">
  @include('modules.includes.menu')
</div>

<section class="container mx-auto px-6 max-w-7xl">
  <div class="grid grid-cols-12 gap-3">
    <main class="col-span-8">
      <section>
        <div class="flex justify-between my-4 border-b w-full pb-5">
          <h4 class="text-2xl font-semibold text-gray-900">Menu (Main)</h4>
          <div>
            <a href="#" class="text-sm font-semibold text-indigo-600 whitespace-nowrap items-center">View all</a>
          </div>
        </div>

        <div class="mb-8">
          <h5 class="font-medium mb-3">Small Plates</h5>
          <ul class="grid grid-cols-2 gap-8">
            <li>
              <h4 class="inline-flex justify-between w-full">
                <span class="font-medium text-base">Avocado Toast</span>
                <span>Rs. 725.00</span>
              </h4>
              <p class="text-sm">Spiced Avocado, Sprouts, Crumbled Feta, Sourdough Toast</p>
            </li>
            <li>
              <h4 class="inline-flex justify-between w-full">
                <span class="font-medium text-base">Blue Tribe Bites – Mock Chicken Fritters</span>
                <span>₹800.00</span>
              </h4>
              <p class="text-sm">BBQ Sauce, Sriracha, Garlic, Sweet Peppers</p>
            </li>
            <li>
              <h4 class="inline-flex justify-between w-full">
                <span class="font-medium text-base">Vegetable &amp; Cheese Samosa</span>
                <span>₹725.00</span>
              </h4>
              <p class="text-sm">with House-made Chilli-Tamarind Chutney</p>
            </li>
            <li>
              <h4 class="inline-flex justify-between w-full">
                <span class="font-medium text-base">Fries</span>
                <span>₹625.00</span>
              </h4>
              <p class="text-sm">with Truffle Essence or Peri-Peri Sauce</p>
            </li>
            <li>
              <h4 class="inline-flex justify-between w-full">
                <span class="font-medium text-base">Panko-Crusted Mustard Fish</span>
                <span>₹775.00</span>
              </h4>
              <p class="text-sm">Crispy Fish Fingers, Classic Tartar, Pickle Salad</p>
            </li>
            <li>
              <h4 class="inline-flex justify-between w-full">
                <span class="font-medium text-base">Skewered Hot Wings</span>
                <span>₹825.00</span>
              </h4>
              <p class="text-sm">with Ranch Dressing</p>
            </li>
            <li>
              <h4 class="inline-flex justify-between w-full">
                <span class="font-medium text-base">Prawn Balchao Toast</span>
                <span>₹825.00</span>
              </h4>
              <p class="text-sm">Spicy Goan Prawn Spread, Sourdough Toast</p>
            </li>
          </ul>
        </div>
      </section>
    </main>

    <aside class="col-span-4">
      <div class="sticky top-20">
        <div class="mb-4">
          <a href="#">
            <img class="w-full" src="https://maps.googleapis.com/maps/api/staticmap?key=AIzaSyBHDlZeuOrKam8hwQCouMlwI-hNAqacPtM&amp;size=288x144&amp;language=en-GB&amp;markers=color%3A0x6AADCC%7C28.5898640%2C77.2164180&amp;signature=WmuI_yZrgCMHHDli-7uiNpcIyWU=" alt="Google Map for Indian Accent" class="mIVYPx0abX4- MiUu-Hmt5zk-" title="" data-test="restaurant-google-map-image" fetchpriority="auto">
            <span class="flex gap-2 mt-2">
              <span>
                <svg class="h-7 w-7" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 7a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm1 3a1 1 0 1 0-2 0 1 1 0 0 0 2 0Z" fill="#2D333F"></path>
                  <path d="M4 10a8 8 0 1 1 16 0c0 2.813-2.433 6.59-7.3 11.33a1 1 0 0 1-1.4 0C6.433 16.59 4 12.813 4 10Zm14 0a6 6 0 0 0-12 0c0 1.21.8 4 6 9.21 5.2-5.21 6-8 6-9.21Z" fill="#2D333F"></path>
                </svg>
              </span>
              <p class="text-gray-600">The Lodhi, Lodhi Road, New Delhi, Delhi 110003</p>
            </span></a>
        </div>

        <div>
          <h5 class="text-lg font-medium text-gray-900 mb-1">Additional Information</h5>
          <div class="border-b w-full pb-5"></div>
        </div>
      </div>
    </aside>
  </div>
</section>

<section class="container mx-auto px-6 py-4">
  <div class="flex">
  </div>
</section>
@endsection