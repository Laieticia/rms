<div class="iq-top-navbar">
    <div class="iq-navbar-custom">
        <nav class="navbar navbar-expand-lg navbar-light p-0">
            <div class="iq-navbar-logo d-flex align-items-center justify-content-between">
                <i class="ri-menu-line wrapper-menu"></i>
                <a href="{{ route('admin.dashboard') }}" class="header-logo">
                    <img src="{{ url('admin/assets/images/logo.png') }}" class="img-fluid rounded-normal" alt="logo">
                    <h5 class="logo-title ml-3">MontRoyal</h5>
                </a>
            </div>
            <div class="iq-search-bar device-search">
                <form action="{{ route('admin.products.index') }}" method="GET" class="searchbox">
                    <button type="submit" class="search-link" style="border:none;background:none;"><i class="ri-search-line"></i></button>
                    <input type="text" name="search" class="text search-input" placeholder="{{ __('messages.search') }}...">
                </form>
            </div>
            <div class="d-flex align-items-center">
                <button class="navbar-toggler" type="button" data-toggle="collapse"
                    data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-label="Toggle navigation">
                    <i class="ri-menu-3-line"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto navbar-list align-items-center">
                        <li class="nav-item nav-icon search-content">
                            <a href="#" class="search-toggle rounded" id="dropdownSearch" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <i class="ri-search-line"></i>
                            </a>
                            <div class="iq-search-bar iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownSearch">
                                <form action="{{ route('admin.products.index') }}" method="GET" class="searchbox p-2">
                                    <div class="form-group mb-0 position-relative">
                                        <input type="text" name="search" class="text search-input font-size-12"
                                            placeholder="{{ __('messages.search') }}...">
                                        <button type="submit" class="search-link" style="border:none;background:none;"><i class="las la-search"></i></button>
                                    </div>
                                </form>
                            </div>
                        </li>
                        <li class="nav-item nav-icon dropdown" id="notificationBell">
                            <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-bell">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>
                                <span id="notificationBadge" class="badge bg-danger rounded-pill position-absolute"
                                    style="top:0;right:0;font-size:10px;{{ auth()->user()->notifications()->where('is_read', false)->count() === 0 ? 'display:none;' : '' }}">
                                    {{ auth()->user()->notifications()->where('is_read', false)->count() }}
                                </span>
                            </a>
                            <div class="iq-sub-dropdown dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton" style="width:350px;">
                                <div class="card shadow-none m-0">
                                    <div class="card-body p-0">
                                        <div class="cust-title p-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h5 class="mb-0">Notifications</h5>
                                                <span class="badge badge-primary badge-card">
                                                    {{ auth()->user()->notifications()->where('is_read', false)->count() }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="px-3 pt-0 pb-0 sub-card" id="notificationList" style="max-height:300px;overflow-y:auto;">
                                            @php
                                                $notifications = auth()->user()->notifications()->latest()->take(5)->get();
                                            @endphp

                                            @forelse($notifications as $notif)
                                                <a href="#" class="iq-sub-card notification-item {{ $notif->is_read ? '' : 'bg-light' }}"
                                                   data-id="{{ $notif->id }}"
                                                   data-url="{{ $notif->action_url ?? '' }}">
                                                    <div class="media align-items-center cust-card py-3 border-bottom">
                                                        <div class="">
                                                            <img class="avatar-50 rounded-small"
                                                                src="https://ui-avatars.com/api/?name=System&background=e74c3c&color=fff&size=50"
                                                                alt="notif">
                                                        </div>
                                                        <div class="media-body ml-3">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <h6 class="mb-0">{{ $notif->title }}</h6>
                                                                <small class="text-dark"><b>{{ $notif->created_at->format('H:i') }}</b></small>
                                                            </div>
                                                            <small class="mb-0">{{ Str::limit($notif->message, 50) }}</small>
                                                        </div>
                                                    </div>
                                                </a>
                                            @empty
                                                <div class="text-center py-4">
                                                    <i class="bi bi-bell-slash text-muted display-6"></i>
                                                    <p class="text-muted mt-2">Aucune notification</p>
                                                </div>
                                            @endforelse
                                        </div>
                                        <a class="right-ic btn btn-primary btn-block position-relative p-2"
                                           href="{{ route('admin.notifications.index') }}" role="button">
                                            Voir toutes les notifications
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>

                        {{-- ── Sélecteur de langue (Admin) ─────────────────────── --}}
                        <li class="nav-item nav-icon dropdown">
                            <a href="#" class="search-toggle dropdown-toggle d-flex align-items-center gap-1"
                               id="dropdownLang" data-toggle="dropdown"
                               aria-haspopup="true" aria-expanded="false"
                               title="Language / Langue"
                               style="padding:6px 10px;border:1px solid rgba(0,0,0,.12);border-radius:20px;font-size:.8rem;">
                                @if(app()->getLocale() === 'fr')
                                    <span>🇫🇷</span> <span>FR</span>
                                @else
                                    <span>🇬🇧</span> <span>EN</span>
                                @endif
                                <i class="las la-angle-down" style="font-size:.7rem;"></i>
                            </a>
                            <div class="iq-sub-dropdown dropdown-menu dropdown-menu-right" aria-labelledby="dropdownLang" style="min-width:140px;">
                                <div class="card shadow-none m-0">
                                    <div class="card-body p-2">
                                        <a href="{{ route('language.switch', 'fr') }}"
                                           class="iq-sub-card d-flex align-items-center gap-2 py-2 px-3 rounded {{ app()->getLocale() === 'fr' ? 'bg-primary text-white' : '' }}"
                                           style="text-decoration:none;">
                                            <span>🇫🇷</span> <span>Français</span>
                                        </a>
                                        <a href="{{ route('language.switch', 'en') }}"
                                           class="iq-sub-card d-flex align-items-center gap-2 py-2 px-3 rounded {{ app()->getLocale() === 'en' ? 'bg-primary text-white' : '' }}"
                                           style="text-decoration:none;">
                                            <span>🇬🇧</span> <span>English</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>
                        {{-- ─────────────────────────────────────────────────── --}}

                        <li class="nav-item nav-icon dropdown caption-content">
                            <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton4"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="{{ auth()->user()->avatar_url ?? url('admin/assets/images/user/1.png') }}" class="img-fluid rounded" alt="user">
                            </a>
                            <div class="iq-sub-dropdown dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton4">
                                <div class="card shadow-none m-0">
                                    <div class="card-body p-0 text-center">
                                        <div class="media-body profile-detail text-center">
                                            <img src="{{ url('admin/assets/images/page-img/profile-bg.jpg') }}" alt="profile-bg"
                                                class="rounded-top img-fluid mb-4">
                                            <img src="{{ auth()->user()->avatar_url ?? url('admin/assets/images/user/1.png') }}" alt="profile-img"
                                                class="rounded profile-img img-fluid avatar-70">
                                        </div>
                                        <div class="p-3">
                                            <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                                            <p class="mb-0 text-muted small">{{ auth()->user()->email }}</p>
                                            <div class="d-flex align-items-center justify-content-center mt-3">
                                                <a href="{{ route('profile.index') }}" class="btn border mr-2">{{ __('app.profile') }}</a>
                                                <form action="{{ route('logout') }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn border">
                                                        {{ __('messages.logout') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.querySelectorAll('.notification-item').forEach(function (item) {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            var id = this.getAttribute('data-id');
            var url = this.getAttribute('data-url');

            fetch('/admin/notifications/' + id + '/read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            }).finally(function () {
                if (url) {
                    window.location.href = url;
                } else {
                    window.location.reload();
                }
            });
        });
    });
});
</script>
