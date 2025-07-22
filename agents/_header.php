<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toptier Agents</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        inter: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Mobile sidebar transition */
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        /* Desktop sidebar always visible */
        @media (min-width: 768px) {
            .sidebar {
                transform: translateX(0) !important;
            }
        }

        /* Hide scrollbar for better mobile experience */
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        /* Smooth transitions for hover effects */
        .hover-scale {
            transition: transform 0.2s ease-in-out;
        }

        .hover-scale:hover {
            transform: scale(1.05);
        }

        /* Background gradients for mobile optimization */
        .bg-mesh {
            background-image: radial-gradient(circle at 1px 1px, rgba(15, 23, 42, 0.15) 1px, transparent 0);
            background-size: 24px 24px;
        }

        /* Custom styles for enhanced UI */
        .backdrop-blur-xl {
            backdrop-filter: blur(24px);
        }
        .backdrop-blur-sm {
            backdrop-filter: blur(4px);
        }
        /* Dropdown styles */
        .dropdown {
            position: relative;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background: white;
            border: 1px solid rgb(226 232 240 / 0.6);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgb(0 0 0 / 0.1);
            z-index: 1000;
            min-width: 200px;
        }

        /* Table dropdown styles - positioned properly */
        .table-dropdown {
            position: relative;
        }
        .table-dropdown-content {
            display: none;
            position: absolute;
            left: -150px;
            top: 50%;
            transform: translateY(-50%);
            background: white;
            border: 1px solid rgb(226 232 240 / 0.6);
            border-radius: 12px;
            box-shadow: 0 20px 40px rgb(0 0 0 / 0.15);
            z-index: 9999;
            min-width: 150px;
        }
        .table-dropdown.active .table-dropdown-content {
            display: block;
        }

        /* Filter dropdown styles */
        .filter-dropdown {
            position: relative;
        }
        .filter-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background: white;
            border: 1px solid rgb(226 232 240 / 0.6);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgb(0 0 0 / 0.1);
            z-index: 1000;
            min-width: 250px;
            padding: 16px;
        }
        .filter-dropdown.active .filter-dropdown-content {
            display: block;
        }
        .dropdown:hover .dropdown-content,
        .dropdown.active .dropdown-content {
            display: block;
        }
        .dropdown-content a {
            display: block;
            padding: 12px 16px;
            color: #1e293b;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
        }
        .dropdown-content a:hover {
            background: rgb(241 245 249);
            color: #0f172a;
        }
        .dropdown-content a:first-child {
            border-radius: 12px 12px 0 0;
        }
        .dropdown-content a:last-child {
            border-radius: 0 0 12px 12px;
        }
        .table-dropdown-content a {
            display: block;
            padding: 12px 16px;
            color: #1e293b;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
            text-align: left;
        }
        .table-dropdown-content a:hover {
            background: rgb(241 245 249);
            color: #0f172a;
        }
        .table-dropdown-content a:first-child {
            border-radius: 12px 12px 0 0;
        }
        .table-dropdown-content a:last-child {
            border-radius: 0 0 12px 12px;
        }

        /* Mobile dropdown styles */
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid rgba(226, 232, 240, 0.6);
            border-radius: 12px;
            padding: 8px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            z-index: 50;
            min-width: 200px;
        }

        .dropdown-menu.show {
            display: block;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/40 relative overflow-x-hidden">
    <!-- Enhanced Background with Mesh Gradient -->
    <div class="fixed inset-0 bg-mesh pointer-events-none"></div>
    <div class="fixed top-0 right-0 w-96 h-96 bg-gradient-to-br from-violet-400/20 to-purple-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="fixed bottom-0 left-0 w-80 h-80 bg-gradient-to-tr from-blue-400/20 to-cyan-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Mobile menu button -->
    <button id="mobile-menu-btn" class="fixed top-4 left-4 z-50 md:hidden bg-white/80 backdrop-blur-xl rounded-xl p-2 shadow-lg border border-slate-200/60 hover:bg-white transition-all duration-200">
        <svg id="menu-icon" class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
        <svg id="close-icon" class="w-6 h-6 text-slate-700 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>

    <!-- Mobile overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-40 md:hidden hidden"></div>

    <!-- Premium Sidebar -->
    <div id="sidebar" class="sidebar fixed left-0 top-0 h-full w-20 bg-white/80 backdrop-blur-xl border-r border-slate-200/60 z-50 shadow-lg shadow-slate-900/5">
        <div class="flex flex-col h-full">
            <!-- Logo Header -->
            <div class="p-4 border-b border-slate-200/60">
                <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl p-3 flex items-center justify-center shadow-inner">
                    <!-- <div class="w-8 h-8 bg-gradient-to-br from-violet-600 to-purple-700 rounded-lg shadow-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div> -->
                    <img src="assets/img/favicon.png" alt="">
                </div>
            </div>

            <!-- Navigation -->
            <div class="flex-1 p-3">
                <div class="mb-8">
                    <div class="text-xs text-slate-500 uppercase tracking-wider text-center mb-4 font-medium">Main</div>
                    <div class="space-y-2">
                        <!-- Active dashboard item -->
                        <div class="relative">
                            <div class="absolute left-[-12px] top-1/2 -translate-y-1/2 w-1 h-8 bg-gradient-to-b from-violet-600 to-purple-700 rounded-r-full shadow-md"></div>
                            <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center mx-auto cursor-pointer shadow-lg shadow-violet-500/25 hover:shadow-xl hover:shadow-violet-500/30 transition-all duration-300 hover-scale">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                    <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="9,22 9,12 15,12 15,22" />
                                </svg>
                            </div>
                        </div>

                        <!-- Other nav items -->
                        <div class="w-12 h-12 rounded-xl hover:bg-slate-100/80 cursor-pointer flex items-center justify-center mx-auto transition-all duration-300 hover-scale hover:shadow-md">
                            <svg class="w-5 h-5 text-slate-500 hover:text-slate-700 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-5 5-4-4-3 3" />
                            </svg>
                        </div>
                        <div class="w-12 h-12 rounded-xl hover:bg-slate-100/80 cursor-pointer flex items-center justify-center mx-auto transition-all duration-300 hover-scale hover:shadow-md">
                            <svg class="w-5 h-5 text-slate-500 hover:text-slate-700 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle stroke-linecap="round" stroke-linejoin="round" stroke-width="2" cx="9" cy="7" r="4" />
                            </svg>
                        </div>
                        <div class="w-12 h-12 rounded-xl hover:bg-slate-100/80 cursor-pointer flex items-center justify-center mx-auto transition-all duration-300 hover-scale hover:shadow-md">
                            <svg class="w-5 h-5 text-slate-500 hover:text-slate-700 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7.5 4.27 9 5.15" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m3.3 7 8.7 5 8.7-5" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22V12" />
                            </svg>
                        </div>
                        <div class="w-12 h-12 rounded-xl hover:bg-slate-100/80 cursor-pointer flex items-center justify-center mx-auto transition-all duration-300 hover-scale hover:shadow-md">
                            <svg class="w-5 h-5 text-slate-500 hover:text-slate-700 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                            </svg>
                        </div>
                        <div class="w-12 h-12 rounded-xl hover:bg-slate-100/80 cursor-pointer flex items-center justify-center mx-auto transition-all duration-300 hover-scale hover:shadow-md">
                            <svg class="w-5 h-5 text-slate-500 hover:text-slate-700 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1Z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <div class="text-xs text-slate-500 uppercase tracking-wider text-center mb-4 font-medium">Other</div>
                    <div class="space-y-2">
                        <div class="w-12 h-12 rounded-xl hover:bg-slate-100/80 cursor-pointer flex items-center justify-center mx-auto transition-all duration-300 hover-scale hover:shadow-md">
                            <svg class="w-5 h-5 text-slate-500 hover:text-slate-700 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <circle stroke-linecap="round" stroke-linejoin="round" stroke-width="2" cx="12" cy="12" r="10" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 17h.01" />
                            </svg>
                        </div>
                        <div class="w-12 h-12 rounded-xl hover:bg-red-50 cursor-pointer flex items-center justify-center mx-auto transition-all duration-300 hover-scale hover:shadow-md">
                            <svg class="w-5 h-5 text-slate-500 hover:text-red-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="16,17 21,12 16,7" />
                                <line stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="21" x2="9" y1="12" y2="12" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Avatar Footer -->
            <div class="p-3 border-t border-slate-200/60">
                <div class="dropdown">
                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl p-3 flex items-center justify-center shadow-inner cursor-pointer hover:bg-gradient-to-br hover:from-slate-100 hover:to-slate-200 transition-all duration-300">
                        <div class="relative w-8 h-8">
                            <img class="w-full h-full rounded-full object-cover shadow-md" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face" alt="User Avatar" />
                            <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-gradient-to-br from-emerald-400 to-green-500 rounded-full border-2 border-white shadow-sm"></div>
                        </div>
                    </div>
                    <div class="dropdown-content" style="left: 0; bottom: 100%; top: auto; min-width: 180px;">
                        <a href="#profile">View Profile</a>
                        <a href="#settings">Settings</a>
                        <a href="#help">Help & Support</a>
                        <hr class="my-1 border-slate-200">
                        <a href="#logout" class="text-red-600 hover:bg-red-50">Sign Out</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="md:ml-20 min-h-screen">


        <!-- Enhanced Top Bar -->
        <div class="bg-white/70 backdrop-blur-xl border-b border-slate-200/60 sticky top-0 z-40">
            <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4 sm:space-x-6 flex-1 mr-4">
                        <!-- Mobile menu button space -->
                        <div class="w-10 md:w-0"></div>

                        <div class="relative flex-1 max-w-sm sm:max-w-md">
                            <svg class="absolute left-3 sm:left-4 top-1/2 transform -translate-y-1/2 text-slate-400 w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                            <input type="text" placeholder="Search anything..." class="pl-10 sm:pl-12 w-full bg-white/80 border-slate-200/60 shadow-sm focus:shadow-md transition-all duration-300 rounded-xl h-10 sm:h-12 border px-4 focus:outline-none focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20" oninput="performSearch(this.value)" />
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <!-- Bell notification dropdown -->
                        <div class="dropdown">
                            <button class="rounded-xl border-slate-200/60 bg-white/80 hover:bg-white hover:shadow-md transition-all duration-300 h-10 sm:h-12 px-3 border flex items-center justify-center relative">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                </svg>
                                <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center">
                                    <span class="text-xs text-white font-semibold">3</span>
                                </div>
                            </button>
                            <div class="dropdown-content" style="min-width: 300px; right: 0;">
                                <div class="p-4 border-b border-slate-200">
                                    <h4 class="font-semibold text-slate-900">Notifications</h4>
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    <a href="#" class="flex items-start space-x-3 p-4 hover:bg-slate-50">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-slate-900">New booking received</p>
                                            <p class="text-xs text-slate-500">Sarah Chen booked Oceanview Resort</p>
                                            <p class="text-xs text-slate-400 mt-1">2 minutes ago</p>
                                        </div>
                                    </a>
                                    <a href="#" class="flex items-start space-x-3 p-4 hover:bg-slate-50">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-slate-900">Payment confirmed</p>
                                            <p class="text-xs text-slate-500">Noah Koch's payment processed</p>
                                            <p class="text-xs text-slate-400 mt-1">1 hour ago</p>
                                        </div>
                                    </a>
                                    <a href="#" class="flex items-start space-x-3 p-4 hover:bg-slate-50">
                                        <div class="w-2 h-2 bg-amber-500 rounded-full mt-2 flex-shrink-0"></div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-slate-900">Commission updated</p>
                                            <p class="text-xs text-slate-500">Your commission rate has been updated</p>
                                            <p class="text-xs text-slate-400 mt-1">3 hours ago</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="p-3 border-t border-slate-200">
                                    <a href="#" class="text-sm text-violet-600 hover:text-violet-700 font-medium">View all notifications</a>
                                </div>
                            </div>
                        </div>

                        <!-- User dropdown - hidden on mobile, visible on desktop -->
                        <div class="hidden sm:flex items-center space-x-3 sm:space-x-4 bg-white/60 rounded-xl px-3 sm:px-4 py-2 border border-slate-200/60 relative h-10 sm:h-12">
                            <img class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover shadow-md" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face" alt="User Avatar">
                            <div class="hidden sm:block">
                                <p class="font-semibold text-slate-900">John Due</p>
                                <p class="text-xs text-slate-500">Administrator</p>
                            </div>
                            <button id="user-dropdown-btn" class="cursor-pointer">
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <!-- Dropdown Menu -->
                            <div id="user-dropdown" class="dropdown-menu">
                                <div class="space-y-1">
                                    <a href="#" class="flex items-center px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Profile
                                    </a>
                                    <a href="#" class="flex items-center px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Settings
                                    </a>
                                    <div class="border-t border-slate-200 my-1"></div>
                                    <a href="#" class="flex items-center px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Main Content -->
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 sm:space-y-8">