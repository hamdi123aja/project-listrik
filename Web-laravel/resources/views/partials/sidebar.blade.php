<aside class="sidebar">
    <h2 style="margin:0 0 2px">Monitoring Listrik</h2>
    <div class="muted" style="font-size:13px;margin-bottom:18px">Berbasis IoT</div>
    <nav class="menu">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('history') }}" class="{{ request()->routeIs('history') ? 'active' : '' }}">History</a>
    </nav>
    <div style="margin-top:18px;font-size:13px;color:#6f6060">
        Login: {{ auth()->user()->name }}
    </div>
    <div style="margin-top:2px;font-size:12px;color:#8a8a8a">
        Role: Administrator
    </div>
    <form method="POST" action="{{ route('logout') }}" style="margin-top:8px">
        @csrf
        <button class="btn btn-secondary" style="width:100%">Logout</button>
    </form>
</aside>
