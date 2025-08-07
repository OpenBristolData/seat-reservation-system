<!-- Font Awesome (for icons) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
      integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
      crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Alpine.js (for dropdown functionality) -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Tailwind CSS (if you're not already using it in your project) -->
<script src="https://cdn.tailwindcss.com"></script>
<nav class="bg-[#00204F] text-white px-4 py-3" x-data="{ mobileMenuOpen: false, userDropdownOpen: false }">
  <div class="flex flex-col md:flex-row justify-between items-center gap-4">
    <!-- Logo + Hamburger -->
    <div class="w-full md:w-auto flex justify-between items-center">
      <a href="{{ auth()->check() ? (auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard')) : '/' }}" class="flex items-center gap-3">
        <img src="https://cdn.jsdelivr.net/gh/OpenBristolData/SLTMobitel-Resource@main/logo.png" alt="Logo" class="h-10" />
      </a>
      <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-white focus:outline-none">
        <i class="fa-solid fa-bars text-2xl"></i>
      </button>
    </div>

    <!-- Main Menu -->
    <div 
      x-show="mobileMenuOpen || window.innerWidth >= 768"
      x-transition:enter="transition ease-out duration-200"
      x-transition:enter-start="opacity-0 scale-95"
      x-transition:enter-end="opacity-100 scale-100"
      x-transition:leave="transition ease-in duration-200"
      x-transition:leave-start="opacity-100 scale-100"
      x-transition:leave-end="opacity-0 scale-95"
      @click.outside="mobileMenuOpen = false"
      class="w-full md:w-auto"
      :class="{ 'hidden': !mobileMenuOpen && window.innerWidth < 768 }"
      x-cloak
    >
      @auth
      <ul class="flex flex-col md:flex-row items-start md:items-center gap-2 md:gap-4 text-sm md:text-base py-4 md:py-0">
        @if(auth()->user()->isAdmin())
          <li>
            <a 
              href="{{ route('admin.dashboard') }}" 
              class="flex items-center gap-2 px-3 py-2 relative
                {{ request()->is('admin/dashboard') 
                   ? 'text-green-400 font-semibold' 
                   : 'text-white hover:text-white after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-green-400 after:scale-x-0 after:origin-left after:transition-transform after:duration-200 hover:after:scale-x-100' }}">
              <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
          </li>
          <li>
            <a 
              href="{{ route('admin.seats.index') }}" 
              class="flex items-center gap-2 px-3 py-2 relative
                {{ request()->is('admin/seats*') 
                   ? 'text-green-400 font-semibold' 
                   : 'text-white hover:text-white after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-green-400 after:scale-x-0 after:origin-left after:transition-transform after:duration-200 hover:after:scale-x-100' }}">
              <i class="fa-solid fa-chair"></i> Seats
            </a>
          </li>
          <li>
            <a 
              href="{{ route('admin.reservations.index') }}" 
              class="flex items-center gap-2 px-3 py-2 relative
                {{ request()->is('admin/reservations*') 
                   ? 'text-green-400 font-semibold' 
                   : 'text-white hover:text-white after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-green-400 after:scale-x-0 after:origin-left after:transition-transform after:duration-200 hover:after:scale-x-100' }}">
              <i class="fa-solid fa-calendar-check"></i> Reservations
            </a>
          </li>
          <li>
            <a 
              href="{{ route('admin.reports') }}" 
              class="flex items-center gap-2 px-3 py-2 relative
                {{ request()->is('admin/reports') 
                   ? 'text-green-400 font-semibold' 
                   : 'text-white hover:text-white after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-green-400 after:scale-x-0 after:origin-left after:transition-transform after:duration-200 hover:after:scale-x-100' }}">
              <i class="fa-solid fa-chart-bar"></i> Reports
            </a>
          </li>
        @else
          <li>
            <a 
              href="{{ route('dashboard') }}" 
              class="flex items-center gap-2 px-3 py-2 relative
                {{ request()->is('dashboard') 
                   ? 'text-green-400 font-semibold' 
                   : 'text-white hover:text-white after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-green-400 after:scale-x-0 after:origin-left after:transition-transform after:duration-200 hover:after:scale-x-100' }}">
              <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
          </li>
          <li>
            <a 
              href="{{ route('reservations.index') }}" 
              class="flex items-center gap-2 px-3 py-2 relative
                {{ request()->is('reservations') 
                   ? 'text-green-400 font-semibold' 
                   : 'text-white hover:text-white after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-green-400 after:scale-x-0 after:origin-left after:transition-transform after:duration-200 hover:after:scale-x-100' }}">
              <i class="fa-solid fa-calendar"></i> My Reservations
            </a>
          </li>
          <li>
            <a 
              href="{{ route('reservations.create') }}" 
              class="flex items-center gap-2 px-3 py-2 relative
                {{ request()->is('reservations/create') 
                   ? 'text-green-400 font-semibold' 
                   : 'text-white hover:text-white after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-green-400 after:scale-x-0 after:origin-left after:transition-transform after:duration-200 hover:after:scale-x-100' }}">
              <i class="fa-solid fa-plus-circle"></i> Book a Seat
            </a>
          </li>
        @endif
      </ul>
      @else
      <ul class="flex flex-col md:flex-row items-start md:items-center gap-2 md:gap-4 text-sm md:text-base py-4 md:py-0">
        <li>
          <a 
            href="{{ route('login') }}" 
            class="flex items-center gap-2 px-3 py-2 relative
              {{ request()->is('login') 
                 ? 'text-green-400 font-semibold' 
                 : 'text-white hover:text-white after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-green-400 after:scale-x-0 after:origin-left after:transition-transform after:duration-200 hover:after:scale-x-100' }}">
            <i class="fa-solid fa-right-to-bracket"></i> Login
          </a>
        </li>
        <li>
          <a 
            href="{{ route('register') }}" 
            class="flex items-center gap-2 px-3 py-2 relative
              {{ request()->is('register') 
                 ? 'text-green-400 font-semibold' 
                 : 'text-white hover:text-white after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-green-400 after:scale-x-0 after:origin-left after:transition-transform after:duration-200 hover:after:scale-x-100' }}">
            <i class="fa-solid fa-user-plus"></i> Register
          </a>
        </li>
      </ul>
      @endauth
    </div>

    <!-- User Dropdown (shown only when authenticated) -->
    @auth
    <div x-data="{ userDropdownOpen: false }" class="relative">
      <button @click="userDropdownOpen = !userDropdownOpen" class="flex items-center gap-2 px-3 py-2 rounded hover:text-green-400">
        <i class="fa-solid fa-circle-user text-2xl"></i>
        <span>{{ auth()->user()->name }}</span>
        <i class="fa-solid fa-caret-down"></i>
      </button>

      <div x-show="userDropdownOpen" 
           @click.outside="userDropdownOpen = false" 
           x-transition
           class="absolute right-0 mt-2 w-40 bg-white shadow-lg rounded-md z-50 text-gray-800"
           style="display: none;">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-blue-100">
            <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout
          </button>
        </form>
      </div>
    </div>
    @endauth
  </div>
</nav>