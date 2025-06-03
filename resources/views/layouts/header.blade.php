<header class="header-top">
     <nav class="navbar navbar-light">
          <div class="navbar-left">
          <div class="logo-area">
               <a class="navbar-brand" href="/dashboard">
				   <h5>Doğuweb Muhasebe</h5>
               </a>
            <a href="#" class="sidebar-toggle">
				<i class="bi bi-list" style="font-size: 1rem;"></i>
			</a>
            </div>
               <!-- Top Menu -->
               <div class="top-menu">
                    <div class="hexadash-top-menu position-relative">
                         <ul class="d-flex align-items-center flex-wrap">
                              <li><a href="{{ route('dashboard') }}" class="active">Dashboard</a></li>
                              <li class="has-subMenu">
                                  
                              </li>
                         </ul>
                    </div>
               </div>
          </div>
          <div class="navbar-right">
               <ul class="navbar-right__menu">
                    <li class="nav-author">
                         <div class="dropdown-custom">
                              <a href="javascript:;" class="nav-item-toggle">
								  <i class="bi bi-person-circle fs-2 text-secondary"></i>
                              <span class="nav-item__title">
                                        {{ Auth::check() ? Auth::user()->name : 'Misafir' }}
                                        <i class="las la-angle-down nav-item__arrow"></i>
                                   </span> </a>
                              <div class="dropdown-parent-wrapper">
                                   <div class="dropdown-wrapper">
                                        <div class="nav-author__options">
                                             <ul>
                                                  <li><a href="{{ route('profil') }}"><i class="uil uil-user"></i>
                                                            Profil</a></li>
                                             </ul>
                                             <a href="{{ route('logout') }}" class="nav-author__signout"
                                                  onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                  <i class="uil uil-sign-out-alt"></i> Çıkış yap
                                             </a>
                                             <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                  style="display: none;">
                                                  @csrf
                                             </form>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </li>
               </ul>
          </div>
     </nav>
</header>