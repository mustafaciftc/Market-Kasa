<div class="sidebar-wrapper">
     <div class="sidebar sidebar-collapse" id="sidebar">
          <div class="sidebar__menu-group">
               <ul class="sidebar_nav">
                    <li>
                         <a href="{{ route('satisyap') }}" class="">
                              <span class="nav-icon uil uil-shopping-bag"></span>
                              <span class="menu-text">Satış Yap</span>

                         </a>
                    </li>

                    <li class="menu-title mt-30">
                         <span>Uygulamalar</span>
                    </li>

                    <li class="has-child">
                         <a href="#" class="">
                              <span class="nav-icon uil uil-transaction"></span>
                              <span class="menu-text">İşlemlerim</span>
                              <span class="toggle-icon"></span>
                         </a>
                         <ul>

                              <li class="">
                              <a href="{{ route('satisislem') }}">Alışveriş işlemlerim</a>
                              </li>
                              <li class="">
                                   <a href="{{ route('gelirgiderislem') }}">Gelir gider
                                        işlemlerim</a>
                              </li>

                         </ul>
                    </li>
                    <li>
                         <a href="{{ route('satisyonetim') }}" class="">
                              <span class="nav-icon uil uil-shop"></span>
                              <span class="menu-text">Satış Yönetimi</span>
                         </a>
                    </li>
                    <li>
                         <a href="{{ route('gelirgideryonetim') }}" class="">
                              <span class="nav-icon uil uil-briefcase"></span>
                              <span class="menu-text">Gelir-gider yönetimi</span>
                         </a>
                    </li>
                    <li>
                         <a href="{{ route('veresiyeyonetimi') }}" class="">
                              <span class="nav-icon uil uil-credit-card-search"></span>
                              <span class="menu-text">Veresiye yönetimi</span>
                         </a>
                    </li>
                  
                   
                    <li class="has-child">
                         <a href="#" class="">
                              <span class="nav-icon uil uil-bag"></span>
                              <span class="menu-text">Ürünler</span>
                              <span class="toggle-icon"></span>
                         </a>
                         <ul>

                              <li class="">
                                   <a href="{{ route('urunyonetimi') }}">Ürün Yönetimi</a>
                              </li>
                              <li class="">
                                   <a href="{{ route('kategoriyonetimi') }}">Kategori Yönetimi</a>
                              </li>

                         </ul>
                    </li>

                    <li class="has-child">
                         <a href="#" class="">
                              <span class="nav-icon uil uil-users-alt"></span>
                              <span class="menu-text">Kullanıcılar</span>
                              <span class="toggle-icon"></span>
                         </a>
                         <ul>
                              <li class="">
                                   <a href="{{ route('kullaniciekle') }}">Kullanıcı Ekle</a>
                              </li>

                              <li class="">
                                   <a href="{{ route('kullaniciliste') }}">Kullanıcı Listele</a>
                              </li>
                              <li class="">
                                   <a href="{{ route('yetki') }}">Yetki Ayarları</a>
                              </li>
                         </ul>
                    </li>
                    <li class="menu-title mt-30">
                         <span>Diğer</span>
                    </li>
                    <li class="has-child">
                         <a href="#" class="">
                              <span class="nav-icon uil uil-setting"></span>
                              <span class="menu-text">Ayarlar</span>
                              <span class="toggle-icon"></span>
                         </a>
                         <ul>

                              <li class="">
                                   <a href="{{ route('ayarlar') }}">Genel Ayarlar</a>
                              </li>

                         </ul>
                    </li>

               </ul>
          </div>
     </div>
</div>