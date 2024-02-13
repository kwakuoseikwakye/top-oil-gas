@section('sidebar')
<!--begin::Aside Menu Sidebar-->
<div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
    <!--begin::Menu Container-->
    <div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1"
        data-menu-dropdown-timeout="500">
        <!--begin::Menu Nav-->

        <ul class="menu-nav">
           


            @php
            $parent = DB::table("tblmodule")
            ->select('tblmodule_priv.modRead','tblmodule_priv.userid','tblmodule.hasChild','tblmodule.isChild',
            'tblmodule.modName', 'tblmodule.modLabel',
            'tblmodule.modURL', 'tblmodule.modIcon', 'tblmodule.modID')
            ->join('tblmodule_priv','tblmodule.modID', 'tblmodule_priv.modID')
            ->where('tblmodule_priv.userid', Auth::user()->email)
            ->where('tblmodule_priv.modRead','1')
            ->where('tblmodule.modStatus','1')
            ->where('tblmodule.isChild','0')
            ->orderBy('tblmodule.arrange', 'ASC')
            ->orderBy('tblmodule.id', 'ASC')
            ->get();
            $parentMods = ['parent'=>$parent];
            @endphp
            @foreach ($parent as $parentMod)
            <li class="menu-item @if (Route::currentRouteName() === strtolower($parentMod->modName)) menu-item-active @endif"
                aria-haspopup="true" data-menu-toggle="hover">
                <a href="{{ config('app.url') }}/{{ $parentMod->modURL }}" class="menu-link menu-toggle">
                    <span class="nav-icon">
                        <i class="{{ $parentMod->modIcon }} mr-2"></i>
                    </span><span class="menu-text">{{ $parentMod->modLabel }}</span>
                </a>
            </li>
            @endforeach
        </ul>
        <!--end::Menu Nav-->
    </div>
    <!--end::Menu Container-->
</div>
@show
