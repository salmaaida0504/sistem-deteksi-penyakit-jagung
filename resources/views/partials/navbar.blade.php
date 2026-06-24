<nav data-aos="fade-down" data-aos-delay="200" class="navbar" id="navbar">
    <div class="navbar-inner">
        <a href="{{ route('home') }}" class="navbar-logo">
            <img src="{{ asset('images/NAVBAR LOGO.png') }}" alt="navbar logo">
        </a>

        <div class="navbar-menu" id="navbarMenu">
            <a href="{{ route('home') }}" class="beranda {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
            <a href="{{ route('edukasi') }}" class="{{ request()->routeIs('edukasi') ? 'active' : '' }}">Edukasi</a>
            <a href="{{ route('produk') }}" class="{{ request()->routeIs('produk') ? 'active' : '' }}">Produk</a>
        </div>

        <button class="hamburger" id="hamburgerBtn" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</nav>
