<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" key="t-menu">Menu</li>
                <li>
                    <a href="{{ route('admin.home') }}" class="waves-effect">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-chat">Dashboard</span>
                    </a>
                </li>
                @canany(['user-list', 'role-list', 'permission-list', 'activity-list'])
                    <li>
                        <a href="javascript:void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-user"></i>
                            <span key="t-utility">Manage Users</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="true">
                            @canany(['role-list', 'role-add'])
                                <li {{ \Request::is('admin/role') || \Request::is('admin/role/*') ? 'class=mm-active' : '' }}>
                                    <a href="{{ route('admin.role.index') }}"
                                        class="waves-effect {{ \Request::is('admin/role') || \Request::is('admin/role/*') ? 'active' : '' }}">
                                        <span key="t-dashboards">Roles</span>
                                    </a>
                                </li>
                            @endcanany

                            @canany(['permission-list', 'permission-add'])
                                <li
                                    {{ \Request::is('admin/permission') || \Request::is('admin/permission/*') ? 'class=mm-active' : '' }}>
                                    <a href="{{ route('admin.permission.index') }}"
                                        class="waves-effect {{ \Request::is('admin/permission') || \Request::is('admin/permission/*') ? 'active' : '' }}">
                                        <span key="t-dashboards">Permission</span>
                                    </a>
                                </li>
                            @endcanany

                            @canany(['user-list', 'user-add'])
                                <li {{ \Request::is('admin/user') || \Request::is('admin/user/*') ? 'class=mm-active' : '' }}>
                                    <a href="{{ route('admin.user.index') }}"
                                        class="waves-effect {{ \Request::is('admin/user') || \Request::is('admin/user/*') ? 'active' : '' }}">
                                        <span key="t-dashboards">User</span>
                                    </a>
                                </li>
                            @endcanany
                            {{-- @can('activity-list')
                                <li>
                                    <a href="{{ route('admin.activity.index') }}" class="" key="t-vertical">Activity
                                        Log</a>
                                </li>
                            @endcan --}}
                        </ul>
                    </li>
                @endcan
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
