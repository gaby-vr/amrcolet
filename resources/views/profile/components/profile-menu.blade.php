<div class="sidenav-main nav-expanded nav-lock nav-collapsible sidenav-light sidenav-active-square">
    <div class="brand-sidebar">
        <h1 class="logo-wrapper">
            <a class="brand-logo darken-1" href="{{ route('home') }}">
                <img class="logo-med-up hide-on-med-and-down inline" src="{{ asset('img/logo-mini.png') }}" alt="materialize logo" />
                <img class="logo-sm-down show-on-medium-and-down hide-on-med-and-up inline" src="{{ asset('img/logo-mini.png') }}" alt="{{ config('app.name', 'AMR Colet') }} logo" />
                <span class="logo-text hide-on-med-and-down">{{ config('app.name', 'AMR Colet') }}</span>
            </a>
            <a class="navbar-toggler" href="#"><i class="material-icons">radio_button_checked</i></a>
        </h1>
    </div>
    <ul class="sidenav sidenav-collapsible leftside-navigation collapsible sidenav-fixed menu-shadow" id="slide-out" data-menu="menu-navigation" data-collapsible="menu-accordion">
        <li class="navigation-header">
            <a class="navigation-header-text">Configurare</a><i class="navigation-header-icon material-icons">more_horiz</i>
        </li>
        <li class="bold">
            <a class="waves-effect waves-cyan {{ request()->routeIs('dashboard.invoice.show') ? 'active' : '' }}" href="{{ route('dashboard.invoice.show') }}"><i class="material-icons">format_list_bulleted</i><span class="menu-title" data-i18n="Date facturare">Date facturare</span></a>
        </li>
        <li class="bold {{ request()->is('dashboard/setari*') ? 'active open' : '' }}">
            <a class="collapsible-header waves-effect waves-cyan" href="javaScript:void(0)"><i class="material-icons">settings</i><span class="menu-title" data-i18n="Setari">Setari</span></a>
            <div class="collapsible-body">
                <ul class="collapsible collapsible-sub" data-collapsible="accordion">
                    <li><a href="{{ route('dashboard.settings.repayment.show') }}" class="{{ request()->routeIs('dashboard.settings.repayment.show') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Rambursuri">Rambursuri</span></a>
                    </li>
                    <li><a href="{{ route('dashboard.settings.security.show') }}" class="{{ request()->routeIs('dashboard.settings.security.show') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Securitate">Securitate</span></a>
                    </li>
                    <li><a href="{{ route('dashboard.settings.notifications.show') }}" class="{{ request()->routeIs('dashboard.settings.notifications.show') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Notificari">Notificari</span></a>
                    </li>
                    <li><a href="{{ route('dashboard.settings.print.show') }}" class="{{ request()->routeIs('dashboard.settings.print.show') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Printare">Printare</span></a>
                    </li>
                    <li><a href="{{ route('dashboard.settings.schedule.show') }}" class="{{ request()->routeIs('dashboard.settings.schedule.show') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Program">Program</span></a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="bold">
            <a class="waves-effect waves-cyan {{ request()->routeIs('dashboard.addresses.show') ? 'active' : '' }}" href="{{ route('dashboard.addresses.show') }}"><i class="material-icons">map</i><span class="menu-title" data-i18n="Adrese">Adrese</span></a>
        </li>
        <li class="bold">
            <a class="waves-effect waves-cyan {{ request()->routeIs('dashboard.templates.show') ? 'active' : '' }}" href="{{ route('dashboard.templates.show') }}"><i class="material-icons">layers</i><span class="menu-title" data-i18n="Sabloane">Sabloane</span></a>
        </li>
        <li class="bold">
            <a class="waves-effect waves-cyan {{ request()->routeIs('dashboard.purse.show') ? 'active' : '' }}" href="{{ route('dashboard.purse.show') }}"><i class="material-icons">account_balance_wallet</i><span class="menu-title" data-i18n="Plata in avans">Plata in avans</span></a>
        </li>
        <li class="navigation-header">
            <a class="navigation-header-text">Informatii si documente</a><i class="navigation-header-icon material-icons">more_horiz</i>
        </li>
        <li class="bold">
            <a class="waves-effect waves-cyan {{ request()->routeIs('dashboard.orders.pending') ? 'active' : '' }}" href="{{ route('dashboard.orders.pending') }}"><i class="material-icons">hourglass_empty</i><span class="menu-title" data-i18n="Comenzi in asteptare">Comenzi in asteptare</span></a>
        </li>
        <li class="bold">
            <a class="waves-effect waves-cyan {{ request()->routeIs('dashboard.orders.show') || request()->routeIs('dashboard.orders.view') ? 'active' : '' }}" href="{{ route('dashboard.orders.show') }}"><i class="material-icons">assignment</i><span class="menu-title" data-i18n="Lista comenzi">Lista comenzi</span></a>
        </li>
        <li class="bold">
            <a class="waves-effect waves-cyan {{ request()->routeIs('dashboard.borderouri.show') ? 'active' : '' }}" href="{{ route('dashboard.borderouri.show') }}"><i class="material-icons">insert_drive_file</i><span class="menu-title" data-i18n="Lista borderouri">Lista borderouri</span></a>
        </li>
        <li class="bold">
            <a class="waves-effect waves-cyan {{ request()->routeIs('dashboard.invoices.show') ? 'active' : '' }}" href="{{ route('dashboard.invoices.show') }}"><i class="material-icons">receipt</i><span class="menu-title" data-i18n="Lista facturi">Lista facturi</span></a>
        </li>
        <li class="bold">
            <a class="waves-effect waves-cyan {{ request()->routeIs('dashboard.repayments.show') ? 'active' : '' }}" href="{{ route('dashboard.repayments.show') }}"><i class="material-icons">rotate_left</i><span class="menu-title" data-i18n="Situatie rambursuri">Situatie rambursuri</span></a>
        </li>
        <li class="bold">
            <a class="waves-effect waves-cyan {{ request()->routeIs('dashboard.financiar.show') ? 'active' : '' }}" href="{{ route('dashboard.financiar.show') }}"><i class="material-icons">import_contacts</i><span class="menu-title" data-i18n="Financiar">Financiar</span></a>
        </li>
        <li class="bold">
            <a class="waves-effect waves-cyan {{ request()->routeIs('dashboard.plugin.show') ? 'active' : '' }}" href="{{ route('dashboard.plugin.show') }}"><i class="material-icons">insert_link</i><span class="menu-title" data-i18n="E-commerce">E-commerce (WIP)</span></a>
        </li>
    </ul>
    <div class="navigation-background"></div><a class="sidenav-trigger btn-sidenav-toggle btn-floating btn-medium waves-effect waves-light hide-on-large-only" href="#" data-target="slide-out"><i class="material-icons">menu</i></a>
</div>