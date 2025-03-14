<header>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
<div class="container-fluid">
<a class="navbar-brand" href="{{ route('pages.index') }}">Brand
Name</a>
<button class="navbar-toggler" type="button" data-bstoggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav"
aria-expanded="false" aria-label="Toggle navigation">
<span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="navbarNav">
<ul class="navbar-nav">
<li class="nav-item">
<a class="nav-link active" aria-current="page" href="{{
route('pages.index') }}">Home</a>
</li>
<li class="nav-item">
<a class="nav-link" href="{{ route('pages.about')
}}">About</a>
</li>
<li class="nav-item">
<a class="nav-link" href="{{ route('pages.visidanmisi')
}}">Visi dan Misi</a>
</li>
<li class="nav-item">
<a class="nav-link" href="{{ route('pages.alumni')
}}">Alumni</a>
</li>
<li class="nav-item">
<a class="nav-link" href="{{ route('pages.prestasi')
}}">Prestasi2</a>
</li>
</ul>
</div>
</div>
</nav>
</header>