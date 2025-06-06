<header class="main-nav">

    <div class="sidebar-user text-center">
        <a class="setting-primary" href="{{ route('profile.parametre') }}">
            <i data-feather="settings"></i>
        </a>
        <div class="mb-4">
            <div class="avatar-circle text-white mx-auto" style="background-color:  #2B6ED4; width: 80px; height: 80px; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 28px; font-weight: bold;">
                <span>{{ substr(auth()->user()->name, 0, 1) }}{{ substr(auth()->user()->lastname, 0, 1) }}</span>
            </div>
        </div>

        <a style="color:  #2B6ED4">
            <h6 class="mt-3 f-14 f-w-600">{{auth()->user()->name}} </h6>
        </a>
    </div>
    <nav>
        <div class="main-navbar">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="mainnav">
                <ul class="nav-menu custom-scrollbar">
                    <li class="back-btn">
                        <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2" aria-hidden="true"></i></div>
                    </li>
                    <li class="sidebar-main-title">
                        <div>
                            <h6>General</h6>
                        </div>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{ prefixActive('/dashboard') }}" href="javascript:void(0)"><i data-feather="home"></i><span>Dashboard</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/dashboard') }};">
                            <li><a href="{{route('index')}}" class="{{routeActive('index')}}">Default</a></li>
                            <li><a href="{{route('dashboard-02')}}" class="{{routeActive('dashboard-02')}}">Ecommerce</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{ prefixActive('/widgets') }}" href="javascript:void(0)"><i data-feather="airplay"></i><span>Widgets</span></a>
                        <ul class="nav-submenu menu-content"  style="display: {{ prefixBlock('/widgets') }};">
                            <li><a href="{{ route('general-widget') }}" class="{{routeActive('general-widget')}}">General</a></li>
                            <li><a href="{{ route('chart-widget') }}" class="{{routeActive('chart-widget')}}">Chart</a></li>
                        </ul>
                    </li>
                    <li class="sidebar-main-title">
                        <div>
                            <h6>Components</h6>
                        </div>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{ prefixActive('/ui-kits') }}" href="javascript:void(0)"><i data-feather="box"></i><span>Ui Kits</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/ui-kits') }};">
                            <li><a href="{{ route('state-color')}}" class="{{routeActive('state-color')}}">State color</a></li>
                            <li><a href="{{ route('typography')}}" class="{{routeActive('typography')}}">Typography</a></li>
                            <li><a href="{{ route('avatars') }}" class="{{routeActive('avatars')}}">Avatars</a></li>
                            <li><a href="{{ route('helper-classes') }}" class="{{routeActive('helper-classes')}}">helper classes</a></li>
                            <li><a href="{{ route('grid') }}" class="{{routeActive('grid')}}">Grid</a></li>
                            <li><a href="{{ route('tag-pills') }}" class="{{routeActive('tag-pills')}}">Tag & pills</a></li>
                            <li><a href="{{ route('progress-bar') }}" class="{{routeActive('progress-bar')}}">Progress</a></li>
                            <li><a href="{{ route('modal') }}" class="{{routeActive('modal')}}">Modal</a></li>
                            <li><a href="{{ route('alert') }}" class="{{routeActive('alert')}}">Alert</a></li>
                            <li><a href="{{ route('popover') }}" class="{{routeActive('popover')}}">Popover</a></li>
                            <li><a href="{{ route('tooltip') }}" class="{{routeActive('tooltip')}}">Tooltip</a></li>
                            <li><a href="{{ route('loader') }}" class="{{routeActive('loader')}}">Spinners</a></li>
                            <li><a href="{{ route('dropdown') }}" class="{{routeActive('dropdown')}}">Dropdown</a></li>
                            <li><a href="{{ route('according') }}" class="{{routeActive('according')}}">Accordion</a></li>
                            <li>
                                <a class="submenu-title  {{ in_array(Route::currentRouteName(), ['tab-bootstrap','tab-material']) ? 'active' : '' }}" href="javascript:void(0)">
                                    Tabs<span class="sub-arrow"><i class="fa fa-chevron-right"></i></span>
                                </a>
                                <ul class="nav-sub-childmenu submenu-content" style="display: {{ in_array(Route::currentRouteName(), ['tab-bootstrap','tab-material']) ? 'block' : 'none' }};">
                                    <li><a href="{{ route('tab-bootstrap') }}" class="{{routeActive('tab-bootstrap')}}">Bootstrap Tabs</a></li>
                                    <li><a href="{{ route('tab-material') }}" class="{{routeActive('tab-material')}}">Line Tabs</a></li>
                                </ul>
                            </li>
                            <li><a href="{{ route('navs') }}" class="{{routeActive('navs')}}">Navs</a></li>
                            <li><a href="{{ route('box-shadow') }}" class="{{routeActive('box-shadow')}}">Shadow</a></li>
                            <li><a href="{{ route('list') }}" class="{{routeActive('list')}}">Lists</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{ prefixActive('/bonus-ui') }}" href="javascript:void(0)"><i data-feather="folder-plus"></i><span>Bonus Ui</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/bonus-ui') }};">
                            <li><a href="{{ route('scrollable') }}" class="{{routeActive('scrollable')}}">Scrollable</a></li>
                            <li><a href="{{ route('tree') }}" class="{{routeActive('tree')}}">Tree view</a></li>
                            <li><a href="{{ route('bootstrap-notify') }}" class="{{routeActive('bootstrap-notify')}}">Bootstrap Notify</a></li>
                            <li><a href="{{ route('rating') }}" class="{{routeActive('rating')}}">Rating</a></li>
                            <li><a href="{{ route('dropzone') }}" class="{{routeActive('dropzone')}}">dropzone</a></li>
                            <li><a href="{{ route('tour') }}" class="{{routeActive('tour')}}">Tour</a></li>
                            <li><a href="{{ route('sweet-alert2') }}" class="{{routeActive('sweet-alert2')}}">SweetAlert2</a></li>
                            <li><a href="{{ route('modal-animated') }}" class="{{routeActive('modal-animated')}}">Animated Modal</a></li>
                            <li><a href="{{ route('owl-carousel') }}" class="{{routeActive('owl-carousel')}}">Owl Carousel</a></li>
                            <li><a href="{{ route('ribbons') }}" class="{{routeActive('ribbons')}}">Ribbons</a></li>
                            <li><a href="{{ route('pagination') }}" class="{{routeActive('pagination')}}">Pagination</a></li>
                            <li><a href="{{ route('steps') }}" class="{{routeActive('steps')}}">Steps</a></li>
                            <li><a href="{{ route('breadcrumb') }}" class="{{routeActive('breadcrumb')}}">Breadcrumb</a></li>
                            <li><a href="{{ route('range-slider') }}" class="{{routeActive('range-slider')}}">Range Slider</a></li>
                            <li><a href="{{ route('image-cropper') }}" class="{{routeActive('image-cropper')}}">Image cropper</a></li>
                            <li><a href="{{ route('sticky') }}" class="{{routeActive('sticky')}}">Sticky </a></li>
                            <li><a href="{{ route('basic-card') }}" class="{{routeActive('basic-card')}}">Basic Card</a></li>
                            <li><a href="{{ route('creative-card') }}" class="{{routeActive('creative-card')}}">Creative Card</a></li>
                            <li><a href="{{ route('tabbed-card') }}" class="{{routeActive('tabbed-card')}}">Tabbed Card</a></li>
                            <li><a href="{{ route('dragable-card') }}" class="{{routeActive('dragable-card')}}">Draggable Card</a></li>
                            <li>
                                <a class="submenu-title {{ in_array(Route::currentRouteName(), ['timeline-v-1','timeline-v-2']) ? 'active' : '' }}" href="javascript:void(0)">
                                    Timeline<span class="sub-arrow"><i class="fa fa-chevron-right"></i></span>
                                </a>
                                <ul class="nav-sub-childmenu submenu-content" style="display: {{ in_array(Route::currentRouteName(), ['timeline-v-1','timeline-v-2']) ? 'block' : 'none' }};">
                                    <li><a href="{{ route('timeline-v-1') }}" class="{{routeActive('timeline-v-1')}}">Timeline 1</a></li>
                                    <li><a href="{{ route('timeline-v-2') }}" class="{{routeActive('timeline-v-2')}}">Timeline 2</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{ prefixActive('/builders') }}" href="javascript:void(0)"><i data-feather="edit-3"></i><span>Builders</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/builders') }};">
                            <li><a href="{{ route('form-builder-1') }}" class="{{routeActive('form-builder-1')}}">Form Builder 1</a></li>
                            <li><a href="{{ route('form-builder-2') }}" class="{{routeActive('form-builder-2')}}">Form Builder 2</a></li>
                            <li><a href="{{ route('pagebuild') }}" class="{{routeActive('pagebuild')}}">Page Builder</a></li>
                            <li><a href="{{ route('button-builder') }}" class="{{routeActive('button-builder')}}">Button Builder</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{ prefixActive('/animation') }}" href="javascript:void(0)"><i data-feather="cloud-drizzle"></i><span>Animation</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/animation') }};">
                            <li><a href="{{ route('animate') }}" class="{{routeActive('animate')}}">Animate</a></li>
                            <li><a href="{{ route('scroll-reval') }}" class="{{routeActive('scroll-reval')}}">Scroll Reveal</a></li>
                            <li><a href="{{ route('aos') }}" class="{{routeActive('aos')}}">AOS animation</a></li>
                            <li><a href="{{ route('tilt') }}" class="{{routeActive('tilt')}}">Tilt Animation</a></li>
                            <li><a href="{{ route('wow') }}" class="{{routeActive('wow')}}">Wow Animation</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{ prefixActive('/icons') }}" href="javascript:void(0)"><i data-feather="command"></i><span>Icons</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/icons') }};">
                            <li><a href="{{ route('flag-icon') }}" class="{{routeActive('flag-icon')}}">Flag icon</a></li>
                            <li><a href="{{ route('font-awesome') }}" class="{{routeActive('font-awesome')}}">Fontawesome Icon</a></li>
                            <li><a href="{{ route('ico-icon') }}" class="{{routeActive('ico-icon')}}">Ico Icon</a></li>
                            <li><a href="{{ route('themify-icon') }}" class="{{routeActive('themify-icon')}}">Thimify Icon</a></li>
                            <li><a href="{{ route('feather-icon') }}" class="{{routeActive('feather-icon')}}">Feather icon</a></li>
                            <li><a href="{{ route('whether-icon') }}" class="{{routeActive('whether-icon')}}">Whether Icon </a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{ prefixActive('/buttons') }}" href="javascript:void(0)"><i data-feather="cloud"></i><span>Buttons</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/buttons') }};">
                            <li><a href="{{ route('buttons') }}" class="{{routeActive('buttons')}}">Default Style</a></li>
                            <li><a href="{{ route('buttons-flat') }}" class="{{routeActive('buttons-flat')}}">Flat Style</a></li>
                            <li><a href="{{ route('buttons-edge') }}" class="{{routeActive('buttons-edge')}}">Edge Style</a></li>
                            <li><a href="{{ route('raised-button') }}" class="{{routeActive('raised-button')}}">Raised Style</a></li>
                            <li><a href="{{ route('button-group') }}" class="{{routeActive('button-group')}}">Button Group</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{ prefixActive('/charts') }}" href="javascript:void(0)"><i data-feather="bar-chart"></i><span>Charts</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/charts') }};">
                            <li><a href="{{ route('chart-apex') }}" class="{{routeActive('chart-apex')}}">Apex Chart</a></li>
                            <li><a href="{{ route('chart-google') }}" class="{{routeActive('chart-google')}}">Google Chart</a></li>
                            <li><a href="{{ route('chart-sparkline') }}" class="{{routeActive('chart-sparkline')}}">Sparkline chart</a></li>
                            <li><a href="{{ route('chart-flot') }}" class="{{routeActive('chart-flot')}}">Flot Chart</a></li>
                            <li><a href="{{ route('chart-knob') }}" class="{{routeActive('chart-knob')}}">Knob Chart</a></li>
                            <li><a href="{{ route('chart-morris') }}" class="{{routeActive('chart-morris')}}">Morris Chart</a></li>
                            <li><a href="{{ route('chartjs') }}" class="{{routeActive('chartjs')}}">Chatjs Chart</a></li>
                            <li><a href="{{ route('chartist') }}" class="{{routeActive('chartist')}}">Chartist Chart</a></li>
                            <li><a href="{{ route('chart-peity') }}" class="{{routeActive('chart-peity')}}">Peity Chart</a></li>
                        </ul>
                    </li>

                    <li class="sidebar-main-title">
                        <div>
                            <h6>Applications</h6>
                        </div>
                    </li>
                    <li class="dropdown">

                         {{-- zedtouu  ta formation --}}
                         <a class="nav-link menu-title {{ prefixActive('/formation') }}" href="javascript:void(0)"><i data-feather="box"></i><span>Formations </span></a>
                         <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/formation') }};">
                             <li><a href="{{ route('formations') }}" class="{{routeActive('formations')}}">Liste de formations</a></li>
                            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin'))

                             <li><a href="{{ route('formationcreate') }}" class="{{routeActive('formationcreate')}}">Nouvelle Formation  </a></li>
                            @endif
                            </ul>
                            @hasanyrole('admin|super-admin')
                         <a class="nav-link menu-title {{ prefixActive('/quizzes') }}" href="javascript:void(0)">
                            <i data-feather="box"></i>
                            <span>Quiz</span>
                        </a>
                        <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/quizzes') }};">
                            <li><a href="{{ route('admin.quizzes.index') }}" class="{{ routeActive('admin.quizzes.index') }}">Liste des Quiz</a></li>
                            <li><a href="{{ route('admin.quizzes.create') }}" class="{{ routeActive('admin.quizzes.create') }}">Nouveau Quiz</a></li>
                        </ul>
                        <a class="nav-link menu-title {{ prefixActive('/feedbacks') }}" href="javascript:void(0)">
                            <i data-feather="box"></i>
                            <span>Avis</span>
                        </a>
                        <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/feedbacks') }};">
                            <li><a href="{{ route('feedbacks') }}" class="{{ routeActive('feedbacks') }}">Liste des Avis</a></li>

                        </ul>

                        @endhasanyrole


                         
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin'))


                         <a class="nav-link menu-title {{ prefixActive('/categorie') }}" href="javascript:void(0)"><i data-feather="box"></i><span>Categories </span></a>
                         <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/categorie') }};">
                             <li><a href="{{ route('categories') }}" class="{{routeActive('categories')}}">Liste de Categories</a></li>
                             <li><a href="{{ route('categoriecreate') }}" class="{{routeActive('categoriecreate')}}">Nouvelle Catégorie </a></li>
                         </ul>

                         {{-- cours --}}
                         <a class="nav-link menu-title {{ prefixActive('/categorie') }}" href="javascript:void(0)"><i data-feather="box"></i><span>Cours </span></a>
                         <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/cours') }};">
                             <li><a href="{{ route('cours') }}" class="{{routeActive('cours')}}">cours List</a></li>
                             <li><a href="{{ route('courscreate') }}" class="{{routeActive('courscreate')}}">Create new </a></li>
                         </ul>
                         {{-- chapitre --}}

                         <a class="nav-link menu-title {{ prefixActive('/chapitre') }}" href="javascript:void(0)"><i data-feather="box"></i><span>Chapitres </span></a>
                         <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/chapitre') }};">
                             <li><a href="{{ route('chapitres') }}" class="{{routeActive('chapitres')}}">chapitres List</a></li>
                             <li><a href="{{ route('chapitrecreate') }}" class="{{routeActive('chapitrecreate')}}">Create new </a></li>
                         </ul>

                          {{-- lesson --}}
                          <a class="nav-link menu-title {{ prefixActive('/lesson') }}" href="javascript:void(0)"><i data-feather="box"></i><span>Leçons </span></a>
                          <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/lesson') }};">
                              <li><a href="{{ route('lessons') }}" class="{{routeActive('lessons')}}">Leçons List</a></li>
                              <li><a href="{{ route('lessoncreate') }}" class="{{routeActive('lessoncreate')}}">Create new </a></li>
                          </ul>
                          @endif






                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title link-nav {{routeActive('file-manager')}}" href="{{ route('file-manager') }}"><i data-feather="git-pull-request"></i><span>File manager</span></a>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title link-nav {{routeActive('kanban')}}" href="{{ route('kanban') }}"><i data-feather="monitor"></i><span>Kanban Board</span></a>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{ prefixActive('/ecommerce') }}" href="javascript:void(0)"><i data-feather="shopping-bag"></i><span>Ecommerce</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/ecommerce') }};">
                            <li><a href="{{ route('product') }}" class="{{routeActive('product')}}">Product</a></li>
                            <li><a href="{{ route('product-page') }}" class="{{routeActive('product-page')}}">Product page</a></li>
                            <li><a href="{{ route('list-products') }}" class="{{routeActive('list-products')}}">Product list</a></li>
                            <li><a href="{{ route('payment-details') }}" class="{{routeActive('payment-details')}}">Payment Details</a></li>
                            <li><a href="{{ route('order-history') }}" class="{{routeActive('order-history')}}">Order History</a></li>
                            <li><a href="{{ route('invoice-template') }}" class="{{routeActive('invoice-template')}}">Invoice</a></li>
                            <li><a href="{{ route('cart') }}" class="{{routeActive('cart')}}">Cart</a></li>
                            <li><a href="{{ route('list-wish') }}" class="{{routeActive('list-wish')}}">Wishlist</a></li>
                            <li><a href="{{ route('checkout') }}" class="{{routeActive('checkout')}}">Checkout</a></li>
                            <li><a href="{{ route('pricing') }}" class="{{routeActive('pricing')}}">Pricing</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{ prefixActive('/email') }}" href="javascript:void(0)"><i data-feather="mail"></i><span>Email</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/email') }};">
                            <li><a href="{{ route('email_inbox') }}" class="{{routeActive('email_inbox')}}">Mail Inbox</a></li>
                            <li><a href="{{ route('email_read') }}" class="{{routeActive('email_read')}}">Read mail</a></li>
                            <li><a href="{{ route('email_compose') }}" class="{{routeActive('email_compose')}}">Compose</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{ prefixActive('/chat') }}" href="javascript:void(0)"><i data-feather="message-circle"></i><span>Chat</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/chat') }};">
                            <li><a href="{{ route('chat') }}" class="{{routeActive('chat')}}">Chat App</a></li>
                            <li><a href="{{ route('chat-video') }}" class="{{routeActive('chat-video')}}">Video chat</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{ prefixActive('/users') }}" href="javascript:void(0)"><i data-feather="users"></i><span>Users</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/users') }};">
                            <li><a href="{{ route('user-profile') }}" class="{{routeActive('user-profile')}}">Users Profile</a></li>
                            {{-- <li><a href="{{ route('edit-profile') }}" class="{{routeActive('edit-profile')}}">Users Edit</a></li> --}}
                            <li><a href="{{ route('user-cards') }}" class="{{routeActive('user-cards')}}">Users Cards</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title link-nav {{routeActive('bookmark')}}" href="{{ route('bookmark') }}"><i data-feather="heart"></i><span>Bookmarks</span></a>
                    </li>
                    <li class="dropdown">
                            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin'))

                        <a class="nav-link menu-title link-nav {{routeActive('contacts')}}" href="{{ route('contacts') }}"><i data-feather="list"></i><span>Utilisateurs et Accès</span></a>
                        @endif
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title link-nav {{routeActive('task')}}" href="{{ route('task') }}"><i data-feather="check-square"></i><span>Tasks</span></a>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title link-nav {{routeActive('calendar-basic')}}" href="{{ route('calendar-basic') }}"><i data-feather="calendar"></i><span>Calender </span></a>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title link-nav {{routeActive('social-app')}}" href="{{ route('social-app') }}"><i data-feather="zap"></i><span>Social App</span></a>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title link-nav {{routeActive('to-do')}}" href="{{ route('to-do') }}"><i data-feather="clock"></i><span>To-Do</span></a>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title link-nav {{routeActive('search')}}" href="{{ route('search') }}"><i data-feather="search"></i><span>Search Result</span></a>
                    </li>
                    <li class="sidebar-main-title">
                        <div>
                            <h6>Pages</h6>
                        </div>
                    </li>
                    <li>
                        {{-- <a class="nav-link menu-title link-nav" href="{{ route('landing-page') }}" class="{{routeActive('landing-page')}}"><i data-feather="navigation-2"></i><span>Landing page</span></a> --}}
                    </li>
                    <li>
                        <a class="nav-link menu-title link-nav {{routeActive('sample-page')}}" href="{{ route('sample-page') }}"><i data-feather="file"></i><span>Sample page</span></a>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title link-nav {{routeActive('internationalization')}}" href="{{ route('internationalization') }}"><i data-feather="aperture"></i><span>Internationalization</span></a>
                    </li>
                    <li class="mega-menu">
                        <a class="nav-link menu-title {{ prefixActive('/') }}" href="javascript:void(0)"><i data-feather="layers"></i><span>Others</span></a>
                        <div class="mega-menu-container menu-content" style="display: {{ prefixBlock('/') }};">
                            <div class="container">
                                <div class="row">
                                    <div class="col mega-box">
                                        <div class="link-section">
                                            <div class="submenu-title">
                                                <h5>Error Page</h5>
                                            </div>
                                            <div class="submenu-content opensubmegamenu">
                                                <ul>
                                                    <li><a href="{{ route('error-page1') }}" class="{{routeActive('error-page1')}}" target="_blank">Error page 1</a></li>
                                                    <li><a href="{{ route('error-page2') }}" class="{{routeActive('error-page2')}}" target="_blank">Error page 2</a></li>
                                                    <li><a href="{{ route('error-page3') }}" class="{{routeActive('error-page3')}}" target="_blank">Error page 3</a></li>
                                                    <li><a href="{{ route('error-page4') }}" class="{{routeActive('error-page4')}}" target="_blank">Error page 4 </a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col mega-box">
                                        <div class="link-section">
                                            <div class="submenu-title">
                                                <h5>Authentication</h5>
                                            </div>
                                            <div class="submenu-content opensubmegamenu">
                                                <ul>
                                                    <li><a href="{{ route('login') }}" class="{{routeActive('login')}}" target="_blank">Login Simple</a></li>
                                                    <li><a href="{{ route('login_one') }}" class="{{routeActive('login_one')}}" target="_blank">Login with bg image</a></li>
                                                    <li><a href="{{ route('login_two') }}" class="{{routeActive('login_two')}}" target="_blank">Login with image two </a></li>
                                                    <li><a href="{{ route('login-bs-validation') }}" class="{{routeActive('login-bs-validation')}}" target="_blank">Login With validation</a></li>
                                                    <li><a href="{{ route('login-bs-tt-validation') }}" class="{{routeActive('login-bs-tt-validation')}}" target="_blank">Login with tooltip</a></li>
                                                    <li><a href="{{ route('login-sa-validation') }}" class="{{routeActive('login-sa-validation')}}" target="_blank">Login with sweetalert</a></li>
                                                    <li><a href="{{ route('sign-up') }}" class="{{routeActive('sign-up')}}" target="_blank">Register Simple</a></li>
                                                    <li><a href="{{ route('sign-up-one') }}" class="{{routeActive('sign-up-one')}}" target="_blank">Register with Bg Image </a></li>
                                                    <li><a href="{{ route('sign-up-two') }}" class="{{routeActive('sign-up-two')}}" target="_blank">Register with Bg video </a></li>
                                                    <li><a href="{{ route('unlock') }}" class="{{routeActive('unlock')}}">Unlock User</a></li>
                                                    {{-- <li><a href="{{ route('forget-password') }}" class="{{routeActive('forget-password')}}">Forget Password</a></li> --}}
                                                    {{-- <li><a href="{{ route('creat-password') }}" class="{{routeActive('creat-password')}}">Creat Password</a></li> --}}
                                                    <li><a href="{{ route('maintenance') }}" class="{{routeActive('maintenance')}}">Maintenance</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col mega-box">
                                        <div class="link-section">
                                            <div class="submenu-title">
                                                <h5>Coming Soon</h5>
                                            </div>
                                            <div class="submenu-content opensubmegamenu">
                                                <ul>
                                                    <li><a href="{{ route('comingsoon') }}" class="{{routeActive('')}}">Coming Simple</a></li>
                                                    <li><a href="{{ route('comingsoon-bg-video') }}" class="{{routeActive('')}}">Coming with Bg video</a></li>
                                                    <li><a href="{{ route('comingsoon-bg-img') }}" class="{{routeActive('')}}">Coming with Bg Image</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col mega-box">
                                        <div class="link-section">
                                            <div class="submenu-title">
                                                <h5>Email templates</h5>
                                            </div>
                                            <div class="submenu-content opensubmegamenu">
                                                <ul>
                                                    <li><a href="{{ route('basic-template') }}" class="{{routeActive('basic-template')}}">Basic Email</a></li>
                                                    <li><a href="{{ route('email-header') }}" class="{{routeActive('email-header')}}">Basic With Header</a></li>
                                                    <li><a href="{{ route('template-email') }}" class="{{routeActive('template-email')}}">Ecomerce Template</a></li>
                                                    <li><a href="{{ route('template-email-2') }}" class="{{routeActive('template-email-2')}}">Email Template 2</a></li>
                                                    <li><a href="{{ route('ecommerce-templates') }}" class="{{routeActive('ecommerce-templates')}}">Ecommerce Email</a></li>
                                                    <li><a href="{{ route('email-order-success') }}" class="{{routeActive('email-order-success')}}">Order Success </a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="sidebar-main-title">
                        <div>
                            <h6>Miscellaneous</h6>
                        </div>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{ prefixActive('/gallery') }}" href="javascript:void(0)"><i data-feather="image"></i><span>Gallery</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/gallery') }};">
                            <li><a href="{{ route('gallery') }}" class="{{routeActive('gallery')}}">Gallery Grid</a></li>
                            <li><a href="{{ route('gallery-with-description') }}" class="{{routeActive('gallery-with-description')}}">Gallery Grid Desc</a></li>
                            <li><a href="{{ route('gallery-masonry') }}" class="{{routeActive('gallery-masonry')}}">Masonry Gallery</a></li>
                            <li><a href="{{ route('masonry-gallery-with-disc') }}" class="{{routeActive('masonry-gallery-with-disc')}}">Masonry with Desc</a></li>
                            <li><a href="{{ route('gallery-hover') }}" class="{{routeActive('gallery-hover')}}">Hover Effects</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{ prefixActive('/blog') }}" href="javascript:void(0)"><i data-feather="edit"></i><span>Blog</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/blog') }};">
                            <li><a href="{{ route('blog') }}" class="{{routeActive('blog')}}">Blog Details</a></li>
                            <li><a href="{{ route('blog-single') }}" class="{{routeActive('blog-single')}}">Blog Single</a></li>
                            <li><a href="{{ route('add-post') }}" class="{{routeActive('add-post')}}">Add Post</a></li>
                        </ul>
                    </li>
                    <li>
                        <a class="nav-link menu-title link-nav {{ routeActive('faq') }}" href="{{ route('faq') }}"><i data-feather="help-circle"></i><span>FAQ</span></a>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{ prefixActive('/job-search') }}" href="javascript:void(0)"><i data-feather="user-check"></i><span>Job Search</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/job-search') }};">
                            <li><a href="{{ route('job-cards-view') }}" class="{{routeActive('job-cards-view')}}">Cards view</a></li>
                            <li><a href="{{ route('job-list-view') }}" class="{{routeActive('job-list-view')}}">List View</a></li>
                            <li><a href="{{ route('job-details') }}" class="{{routeActive('job-details')}}">Job Details</a></li>
                            <li><a href="{{ route('job-apply') }}" class="{{routeActive('job-apply')}}">Apply</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{ prefixActive('/learning') }}" href="javascript:void(0)"><i data-feather="layers"></i><span>Learning</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/learning') }};">
                            <li><a href="{{ route('learning-list-view') }}" class="{{routeActive('learning-list-view')}}">Learning List</a></li>
                            <li><a href="{{ route('learning-detailed') }}" class="{{routeActive('learning-detailed')}}">Detailed Course</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{ prefixActive('/maps') }}" href="javascript:void(0)"><i data-feather="map"></i><span>Maps</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/maps') }};">
                            <li><a href="{{ route('map-js') }}" class="{{routeActive('map-js')}}">Maps JS</a></li>
                            <li><a href="{{ route('vector-map') }}" class="{{routeActive('vector-map')}}">Vector Maps</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{ prefixActive('/editors') }}" href="javascript:void(0)"><i data-feather="git-pull-request"></i><span>Editors</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/editors') }};">
                            <li><a href="{{ route('summernote') }}" class="{{routeActive('summernote')}}">Summer Note</a></li>
                            <li><a href="{{ route('ckeditor') }}" class="{{routeActive('ckeditor')}}">CK editor</a></li>
                            <li><a href="{{ route('simple-MDE') }}" class="{{routeActive('simple-MDE')}}">MDE editor</a></li>
                            <li><a href="{{ route('ace-code-editor') }}" class="{{routeActive('ace-code-editor')}}">ACE code editor</a></li>
                        </ul>
                    </li>
                    <li>
                        <a class="nav-link menu-title link-nav {{routeActive('knowledgebase')}}" href="{{ route('knowledgebase') }}"><i data-feather="database"></i><span>Knowledgebase</span></a>
                    </li>
                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </div>
    </nav>
</header>
