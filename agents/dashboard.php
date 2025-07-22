<?php
include_once '_header.php';
?>
            <!-- Welcome Section -->
            <div class="space-y-2">
                <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-slate-900 via-slate-800 to-slate-700 bg-clip-text text-transparent">Welcome back, John</h1>
                <p class="text-slate-600 text-sm sm:text-base">Here's what's happening with your business today.</p>
            </div>

            <!-- Stats Cards and Commission Tracking -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 sm:gap-8">
                <!-- Enhanced Stats Cards -->
                <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                    <!-- Total Sales Revenue -->
                    <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-4 sm:p-6 border border-slate-200/60 shadow-lg shadow-slate-900/5 hover:shadow-xl hover:shadow-slate-900/10 transition-all duration-300">
                        <div class="flex items-start justify-between">
                            <div class="space-y-3 sm:space-y-4 flex-1">
                                <div>
                                    <p class="text-sm font-medium text-slate-600 mb-2">Total Sales Revenue</p>
                                    <p class="text-2xl sm:text-3xl font-bold text-slate-900">$49,420</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="flex items-center space-x-1 bg-emerald-50 px-2 py-1 rounded-lg">
                                        <!-- Fixed: Correct up arrow -->
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 14l5-5 5 5" />
                                        </svg>
                                        <span class="text-sm font-semibold text-emerald-600">40%</span>
                                    </div>
                                    <span class="text-xs text-slate-500">vs last month</span>
                                </div>
                            </div>
                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-violet-500/25 ml-4">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-5 5-4-4-3 3" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Total Commission - Fixed: Only user icon and centered -->
                    <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-4 sm:p-6 border border-slate-200/60 shadow-lg shadow-slate-900/5 hover:shadow-xl hover:shadow-slate-900/10 transition-all duration-300">
                        <div class="flex items-start justify-between">
                            <div class="space-y-3 sm:space-y-4 flex-1">
                                <div>
                                    <p class="text-sm font-medium text-slate-600 mb-2">Total Commission</p>
                                    <p class="text-2xl sm:text-3xl font-bold text-slate-900">$12,710</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="flex items-center space-x-1 bg-rose-50 px-2 py-1 rounded-lg">
                                        <!-- Fixed: Correct down arrow -->
                                        <svg class="w-3 h-3 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 10l-5 5-5-5" />
                                        </svg>
                                        <span class="text-sm font-semibold text-rose-600">12%</span>
                                    </div>
                                    <span class="text-xs text-slate-500">vs last month</span>
                                </div>
                            </div>
                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/25 ml-4">
                                <!-- Fixed: Only user icon, properly centered -->
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                    <circle stroke-linecap="round" stroke-linejoin="round" stroke-width="2" cx="9" cy="7" r="4" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Partner Commission -->
                    <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-4 sm:p-6 border border-slate-200/60 shadow-lg shadow-slate-900/5 hover:shadow-xl hover:shadow-slate-900/10 transition-all duration-300">
                        <div class="flex items-start justify-between">
                            <div class="space-y-3 sm:space-y-4 flex-1">
                                <div>
                                    <p class="text-sm font-medium text-slate-600 mb-2">Partner Commission</p>
                                    <p class="text-2xl sm:text-3xl font-bold text-slate-900">$19,380</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="flex items-center space-x-1 bg-emerald-50 px-2 py-1 rounded-lg">
                                        <!-- Fixed: Correct up arrow -->
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 14l5-5 5 5" />
                                        </svg>
                                        <span class="text-sm font-semibold text-emerald-600">28%</span>
                                    </div>
                                    <span class="text-xs text-slate-500">vs last month</span>
                                </div>
                            </div>
                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/25 ml-4">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7.5 4.27 9 5.15" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m3.3 7 8.7 5 8.7-5" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22V12" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Commission Tracking -->
                <div class="lg:col-span-3 bg-white/70 backdrop-blur-xl rounded-2xl p-4 sm:p-6 lg:p-8 border border-slate-200/60 shadow-lg shadow-slate-900/5">
                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h3 class="text-lg sm:text-xl font-bold text-slate-900 mb-2">Commission Overview</h3>
                                <p class="text-sm text-slate-600">Track your earnings performance</p>
                            </div>
                            <button class="bg-gradient-to-r from-violet-600 to-purple-700 hover:from-violet-700 hover:to-purple-800 text-white rounded-xl shadow-lg shadow-violet-500/25 hover:shadow-xl hover:shadow-violet-500/30 transition-all duration-300 px-4 py-2 text-sm sm:text-base">
                                View Details
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-4 sm:gap-6 py-4">
                            <div class="text-center">
                                <p class="text-sm text-slate-600 mb-1">This Week</p>
                                <p class="text-xl sm:text-2xl font-bold text-slate-900">70%</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-slate-600 mb-1">Projects</p>
                                <p class="text-xl sm:text-2xl font-bold text-slate-900">30%</p>
                            </div>
                        </div>

                        <div class="flex flex-col lg:flex-row items-center justify-between gap-6 pt-4">
                            <!-- Enhanced DataPlot -->
                            <div class="w-[150px] h-[150px] sm:w-[188px] sm:h-[188px] flex-shrink-0">
                                <div class="relative size-full">
                                    <svg class="block size-full" viewBox="0 0 188 188" fill="none">
                                        <!-- Blue outer ring (70%) -->
                                        <circle
                                            cx="94"
                                            cy="94"
                                            r="84"
                                            stroke="url(#blueGradient)"
                                            stroke-width="20"
                                            fill="none"
                                            stroke-dasharray="526.8 526.8"
                                            stroke-dashoffset="158"
                                            stroke-linecap="round"
                                            transform="rotate(-90 94 94)"
                                        />
                                        <!-- Orange inner ring (30%) -->
                                        <circle
                                            cx="94"
                                            cy="94"
                                            r="60"
                                            stroke="url(#orangeGradient)"
                                            stroke-width="16"
                                            fill="none"
                                            stroke-dasharray="377 377"
                                            stroke-dashoffset="264"
                                            stroke-linecap="round"
                                            transform="rotate(-90 94 94)"
                                        />
                                        <!-- Inner white circle -->
                                        <circle cx="94" cy="94" r="44" fill="#F8F8F8"/>

                                        <defs>
                                            <linearGradient id="blueGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                                <stop offset="0%" stop-color="#43ACFC"/>
                                                <stop offset="100%" stop-color="#59B6FC"/>
                                            </linearGradient>
                                            <linearGradient id="orangeGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                                <stop offset="0%" stop-color="#FECC6A"/>
                                                <stop offset="100%" stop-color="#FEB528"/>
                                            </linearGradient>
                                        </defs>
                                    </svg>
                                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-center">
                                        <div class="text-xs sm:text-sm text-slate-500 mb-0.5">Total</div>
                                        <div class="text-lg sm:text-2xl text-slate-900 font-semibold">100%</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Legend -->
                            <div class="flex flex-col space-y-4 sm:space-y-6">
                                <div class="flex items-center space-x-3 sm:space-x-4">
                                    <div class="w-4 h-4 sm:w-5 sm:h-5 rounded-xl shadow-sm" style="background: linear-gradient(135deg, #43ACFC 0%, #59B6FC 100%);"></div>
                                    <div class="flex items-center space-x-4 sm:space-x-8">
                                        <span class="text-sm font-medium text-slate-700 min-w-[40px] sm:min-w-[60px]">Paid</span>
                                        <span class="text-lg sm:text-xl font-bold text-slate-900">70%</span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 sm:space-x-4">
                                    <div class="w-4 h-4 sm:w-5 sm:h-5 rounded-xl shadow-sm" style="background: linear-gradient(135deg, #FECC6A 0%, #FEB528 100%);"></div>
                                    <div class="flex items-center space-x-4 sm:space-x-8">
                                        <span class="text-sm font-medium text-slate-700 min-w-[40px] sm:min-w-[60px]">Pending</span>
                                        <span class="text-lg sm:text-xl font-bold text-slate-900">30%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Total Earned -->
                            <div class="text-center lg:text-right space-y-2">
                                <p class="text-sm font-medium text-slate-600">Total Earned</p>
                                <p class="text-2xl sm:text-3xl lg:text-4xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent tracking-tight">$100.00</p>
                                <div class="flex items-center justify-center lg:justify-end space-x-1 text-emerald-600">
                                    <!-- Fixed: Correct up arrow -->
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 14l5-5 5 5" />
                                    </svg>
                                    <span class="text-xs font-semibold">+12.5%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Recent Bookings -->
            <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-4 sm:p-6 lg:p-8 border border-slate-200/60 shadow-lg shadow-slate-900/5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 sm:mb-8">
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-slate-900 mb-2">Recent Bookings</h3>
                        <p class="text-sm text-slate-600">Latest customer reservations</p>
                    </div>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
                        <div class="filter-dropdown">
                            <button class="rounded-xl border-slate-200/60 bg-white/60 hover:bg-white hover:shadow-md transition-all duration-300 px-3 py-2 border flex items-center justify-center sm:justify-start w-full sm:w-auto" onclick="toggleFilter()">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2.586a1 1 0 0 1-.293.707l-6.414 6.414a1 1 0 0 0-.293.707V17l-4 4v-6.586a1 1 0 0 0-.293-.707L3.293 7.293A1 1 0 0 1 3 6.586V4Z" />
                                </svg>
                                Filter
                            </button>
                            <div class="filter-dropdown-content">
                                <h4 class="font-semibold text-slate-900 mb-4">Filter Bookings</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                                        <select onchange="applyFilters()" id="statusFilter" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                                            <option value="">All Status</option>
                                            <option value="confirmed">Confirmed</option>
                                            <option value="pending">Pending</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Type</label>
                                        <select onchange="applyFilters()" id="typeFilter" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                                            <option value="">All Types</option>
                                            <option value="business">Business</option>
                                            <option value="leisure">Leisure</option>
                                            <option value="family">Family</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Guest Type</label>
                                        <select onchange="applyFilters()" id="guestFilter" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                                            <option value="">All Guests</option>
                                            <option value="vip">VIP Guest</option>
                                            <option value="premium">Premium Guest</option>
                                            <option value="regular">Regular Guest</option>
                                            <option value="executive">Executive Guest</option>
                                            <option value="business">Business Guest</option>
                                        </select>
                                    </div>
                                    <div class="flex space-x-2 pt-2">
                                        <button onclick="clearFilters()" class="flex-1 px-3 py-2 text-sm border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">Clear</button>
                                        <button onclick="toggleFilter()" class="flex-1 px-3 py-2 text-sm bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition-colors">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                            <input type="text" placeholder="Search bookings..." class="pl-10 w-full sm:w-72 bg-white/60 border-slate-200/60 shadow-sm focus:shadow-md transition-all duration-300 rounded-xl border px-4 py-2 focus:outline-none focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20" oninput="searchBookings(this.value)" />
                        </div>
                    </div>
                </div>

                <!-- Desktop Table -->
                <div class="hidden lg:block border border-slate-200/60 rounded-xl overflow-hidden bg-white/40 backdrop-blur-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50/80 backdrop-blur-sm">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Guest</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Hotel</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Date & Duration</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Type</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Status</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold text-slate-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200/60">
                                <tr class="hover:bg-white/60 transition-all duration-200">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center space-x-4">
                                            <img class="w-12 h-12 rounded-full object-cover shadow-md" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=48&h=48&fit=crop&crop=face" alt="Noah Koch" />
                                            <div>
                                                <p class="font-semibold text-slate-900">Noah Koch</p>
                                                <p class="text-xs text-slate-500">Premium Guest</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div>
                                            <p class="font-medium text-slate-900">Azure Heights Hotel</p>
                                            <p class="text-xs text-slate-500">Suite 204</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5a2.25 2.25 0 0 0 2.25-2.25m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-slate-900">May 12, 2025</p>
                                                <p class="text-xs text-slate-500">7 Days</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="bg-blue-50 text-blue-700 border-blue-200 rounded-lg px-3 py-1 text-xs font-medium border">Business</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="bg-emerald-50 text-emerald-700 border-emerald-200 rounded-lg px-3 py-1 text-xs font-medium border">Confirmed</span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="table-dropdown">
                                            <button class="rounded-lg hover:bg-slate-100 transition-all duration-200 p-2" onclick="toggleTableDropdown(this)">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm0 7a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm0 7a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" />
                                                </svg>
                                            </button>
                                            <div class="table-dropdown-content">
                                                <a href="#view">View Details</a>
                                                <a href="#edit">Edit Booking</a>
                                                <a href="#contact">Contact Guest</a>
                                                <a href="#cancel" class="text-red-600 hover:bg-red-50">Cancel Booking</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-white/60 transition-all duration-200">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center space-x-4">
                                            <img class="w-12 h-12 rounded-full object-cover shadow-md" src="https://images.unsplash.com/photo-1534751516642-a1af1ef26a56?w=48&h=48&fit=crop&crop=face" alt="Arlene Auer" />
                                            <div>
                                                <p class="font-semibold text-slate-900">Arlene Auer</p>
                                                <p class="text-xs text-slate-500">VIP Guest</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div>
                                            <p class="font-medium text-slate-900">Skyline Haven</p>
                                            <p class="text-xs text-slate-500">Penthouse</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5a2.25 2.25 0 0 0 2.25-2.25m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-slate-900">May 15, 2025</p>
                                                <p class="text-xs text-slate-500">3 Days</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="bg-purple-50 text-purple-700 border-purple-200 rounded-lg px-3 py-1 text-xs font-medium border">Leisure</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="bg-emerald-50 text-emerald-700 border-emerald-200 rounded-lg px-3 py-1 text-xs font-medium border">Confirmed</span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="table-dropdown">
                                            <button class="rounded-lg hover:bg-slate-100 transition-all duration-200 p-2" onclick="toggleTableDropdown(this)">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm0 7a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm0 7a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" />
                                                </svg>
                                            </button>
                                            <div class="table-dropdown-content">
                                                <a href="#view">View Details</a>
                                                <a href="#edit">Edit Booking</a>
                                                <a href="#contact">Contact Guest</a>
                                                <a href="#cancel" class="text-red-600 hover:bg-red-50">Cancel Booking</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-white/60 transition-all duration-200">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center space-x-4">
                                            <img class="w-12 h-12 rounded-full object-cover shadow-md" src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=48&h=48&fit=crop&crop=face" alt="Kelley Jerde" />
                                            <div>
                                                <p class="font-semibold text-slate-900">Kelley Jerde</p>
                                                <p class="text-xs text-slate-500">Regular Guest</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div>
                                            <p class="font-medium text-slate-900">Whispering Palms Resort</p>
                                            <p class="text-xs text-slate-500">Ocean View</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5a2.25 2.25 0 0 0 2.25-2.25m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-slate-900">May 18, 2025</p>
                                                <p class="text-xs text-slate-500">5 Days</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="bg-green-50 text-green-700 border-green-200 rounded-lg px-3 py-1 text-xs font-medium border">Family</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="bg-amber-50 text-amber-700 border-amber-200 rounded-lg px-3 py-1 text-xs font-medium border">Pending</span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="table-dropdown">
                                            <button class="rounded-lg hover:bg-slate-100 transition-all duration-200 p-2" onclick="toggleTableDropdown(this)">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm0 7a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm0 7a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" />
                                                </svg>
                                            </button>
                                            <div class="table-dropdown-content">
                                                <a href="#view">View Details</a>
                                                <a href="#edit">Edit Booking</a>
                                                <a href="#contact">Contact Guest</a>
                                                <a href="#cancel" class="text-red-600 hover:bg-red-50">Cancel Booking</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Additional rows for more content to scroll -->
                                <tr class="hover:bg-white/60 transition-all duration-200">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center space-x-4">
                                            <img class="w-12 h-12 rounded-full object-cover shadow-md" src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=48&h=48&fit=crop&crop=face" alt="Marcus Johnson" />
                                            <div>
                                                <p class="font-semibold text-slate-900">Marcus Johnson</p>
                                                <p class="text-xs text-slate-500">Executive Guest</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div>
                                            <p class="font-medium text-slate-900">Grand Plaza Hotel</p>
                                            <p class="text-xs text-slate-500">Executive Suite</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5a2.25 2.25 0 0 0 2.25-2.25m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-slate-900">May 20, 2025</p>
                                                <p class="text-xs text-slate-500">4 Days</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="bg-blue-50 text-blue-700 border-blue-200 rounded-lg px-3 py-1 text-xs font-medium border">Business</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="bg-amber-50 text-amber-700 border-amber-200 rounded-lg px-3 py-1 text-xs font-medium border">Pending</span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="table-dropdown">
                                            <button class="rounded-lg hover:bg-slate-100 transition-all duration-200 p-2" onclick="toggleTableDropdown(this)">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm0 7a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm0 7a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" />
                                                </svg>
                                            </button>
                                            <div class="table-dropdown-content">
                                                <a href="#view">View Details</a>
                                                <a href="#edit">Edit Booking</a>
                                                <a href="#contact">Contact Guest</a>
                                                <a href="#cancel" class="text-red-600 hover:bg-red-50">Cancel Booking</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr class="hover:bg-white/60 transition-all duration-200">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center space-x-4">
                                            <img class="w-12 h-12 rounded-full object-cover shadow-md" src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=48&h=48&fit=crop&crop=face" alt="Sarah Chen" />
                                            <div>
                                                <p class="font-semibold text-slate-900">Sarah Chen</p>
                                                <p class="text-xs text-slate-500">Premium Guest</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div>
                                            <p class="font-medium text-slate-900">Oceanview Resort</p>
                                            <p class="text-xs text-slate-500">Deluxe Room</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5a2.25 2.25 0 0 0 2.25-2.25m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-slate-900">May 22, 2025</p>
                                                <p class="text-xs text-slate-500">6 Days</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="bg-purple-50 text-purple-700 border-purple-200 rounded-lg px-3 py-1 text-xs font-medium border">Leisure</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="bg-emerald-50 text-emerald-700 border-emerald-200 rounded-lg px-3 py-1 text-xs font-medium border">Confirmed</span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <button class="rounded-lg p-2 cursor-not-allowed opacity-50" disabled>
                                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm0 7a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm0 7a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Cards -->
                <div class="lg:hidden space-y-4">
                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-4 border border-slate-200/60 hover:bg-white/80 transition-all duration-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <img class="w-12 h-12 rounded-full object-cover shadow-md" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=48&h=48&fit=crop&crop=face" alt="Noah Koch">
                                <div>
                                    <p class="font-semibold text-slate-900">Noah Koch</p>
                                    <p class="text-xs text-slate-500">Premium Guest</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="bg-blue-50 text-blue-700 border-blue-200 rounded-lg px-2 py-1 text-xs font-medium border">Business</span>
                                <span class="bg-emerald-50 text-emerald-700 border-emerald-200 rounded-lg px-2 py-1 text-xs font-medium border">Confirmed</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-slate-900">Azure Heights Hotel</p>
                                    <p class="text-xs text-slate-500">Suite 204</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-slate-900">May 12, 2025</p>
                                    <p class="text-xs text-slate-500">7 Days</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-200/60">
                            <button class="w-full flex items-center justify-center space-x-2 text-violet-600 hover:text-violet-700 transition-colors">
                                <span class="text-sm">View Details</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 5 7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-4 border border-slate-200/60 hover:bg-white/80 transition-all duration-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <img class="w-12 h-12 rounded-full object-cover shadow-md" src="https://images.unsplash.com/photo-1534751516642-a1af1ef26a56?w=48&h=48&fit=crop&crop=face" alt="Arlene Auer">
                                <div>
                                    <p class="font-semibold text-slate-900">Arlene Auer</p>
                                    <p class="text-xs text-slate-500">VIP Guest</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="bg-purple-50 text-purple-700 border-purple-200 rounded-lg px-2 py-1 text-xs font-medium border">Leisure</span>
                                <span class="bg-emerald-50 text-emerald-700 border-emerald-200 rounded-lg px-2 py-1 text-xs font-medium border">Confirmed</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-slate-900">Skyline Haven</p>
                                    <p class="text-xs text-slate-500">Penthouse</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-slate-900">May 15, 2025</p>
                                    <p class="text-xs text-slate-500">3 Days</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-200/60">
                            <button class="w-full flex items-center justify-center space-x-2 text-violet-600 hover:text-violet-700 transition-colors">
                                <span class="text-sm">View Details</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 5 7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-4 border border-slate-200/60 hover:bg-white/80 transition-all duration-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <img class="w-12 h-12 rounded-full object-cover shadow-md" src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=48&h=48&fit=crop&crop=face" alt="Kelley Jerde">
                                <div>
                                    <p class="font-semibold text-slate-900">Kelley Jerde</p>
                                    <p class="text-xs text-slate-500">Regular Guest</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="bg-green-50 text-green-700 border-green-200 rounded-lg px-2 py-1 text-xs font-medium border">Family</span>
                                <span class="bg-amber-50 text-amber-700 border-amber-200 rounded-lg px-2 py-1 text-xs font-medium border">Pending</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-slate-900">Whispering Palms Resort</p>
                                    <p class="text-xs text-slate-500">Ocean View</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-slate-900">May 18, 2025</p>
                                    <p class="text-xs text-slate-500">5 Days</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-200/60">
                            <button class="w-full flex items-center justify-center space-x-2 text-violet-600 hover:text-violet-700 transition-colors">
                                <span class="text-sm">View Details</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 5 7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6 sm:mt-8">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-slate-600">Showing</span>
                        <select class="px-3 py-1 text-sm bg-white/60 border border-slate-200/60 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500">
                            <option>10</option>
                            <option>25</option>
                            <option>50</option>
                        </select>
                        <span class="text-sm text-slate-600">of 157 entries</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="px-3 py-1 text-sm bg-white/60 border border-slate-200/60 rounded-lg hover:bg-white hover:shadow-md transition-all duration-300">Previous</button>
                        <div class="flex items-center space-x-1">
                            <button class="px-3 py-1 text-sm bg-violet-50 border border-violet-200 text-violet-600 rounded-lg">1</button>
                            <button class="px-3 py-1 text-sm bg-white/60 border border-slate-200/60 rounded-lg hover:bg-white hover:shadow-md transition-all duration-300">2</button>
                            <button class="px-3 py-1 text-sm bg-white/60 border border-slate-200/60 rounded-lg hover:bg-white hover:shadow-md transition-all duration-300">3</button>
                            <span class="px-2 text-slate-400">...</span>
                            <button class="px-3 py-1 text-sm bg-white/60 border border-slate-200/60 rounded-lg hover:bg-white hover:shadow-md transition-all duration-300">16</button>
                        </div>
                        <button class="px-3 py-1 text-sm bg-white/60 border border-slate-200/60 rounded-lg hover:bg-white hover:shadow-md transition-all duration-300">Next</button>
                    </div>
                </div>
            </div>

            <!-- Share & Earn Rewards Section -->
            <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-4 sm:p-6 lg:p-8 border border-slate-200/60 shadow-lg shadow-slate-900/5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Share Travel Link & Earn Reward -->
                    <div class="bg-gradient-to-br from-blue-50/50 to-blue-100/50 rounded-2xl p-4 sm:p-6 border border-blue-200/60 hover:shadow-md transition-all duration-300">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/25 flex-shrink-0">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                </svg>
                            </div>
                            <div class="flex-1 space-y-3 sm:space-y-4">
                                <div>
                                    <h4 class="text-base sm:text-lg font-bold text-slate-900 mb-2">Share Travel Link & Earn Reward</h4>
                                    <p class="text-sm text-slate-600">Invite clients or partner to your network</p>
                                </div>
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
                                    <button onclick="shareLink('travel')" class="bg-gradient-to-r from-violet-600 to-purple-700 hover:from-violet-700 hover:to-purple-800 text-white rounded-xl shadow-lg shadow-violet-500/25 hover:shadow-xl hover:shadow-violet-500/30 transition-all duration-300 px-6 py-3 font-medium flex-1 sm:flex-none">
                                        Share Link
                                    </button>
                                    <button onclick="copyLink('travel', this)" class="bg-white/80 hover:bg-white border border-slate-200 text-slate-700 hover:text-slate-900 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 px-6 py-3 font-medium flex-1 sm:flex-none">
                                        Copy Link
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Share Partner Link & Earn Reward -->
                    <div class="bg-gradient-to-br from-emerald-50/50 to-cyan-50/50 rounded-2xl p-4 sm:p-6 border border-emerald-200/60 hover:shadow-md transition-all duration-300">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-emerald-500 to-cyan-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/25 flex-shrink-0">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                </svg>
                            </div>
                            <div class="flex-1 space-y-3 sm:space-y-4">
                                <div>
                                    <h4 class="text-base sm:text-lg font-bold text-slate-900 mb-2">Share Partner Link & Earn Reward</h4>
                                    <p class="text-sm text-slate-600">Invite clients or partner to your network</p>
                                </div>
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
                                    <button onclick="shareLink('partner')" class="bg-gradient-to-r from-violet-600 to-purple-700 hover:from-violet-700 hover:to-purple-800 text-white rounded-xl shadow-lg shadow-violet-500/25 hover:shadow-xl hover:shadow-violet-500/30 transition-all duration-300 px-6 py-3 font-medium flex-1 sm:flex-none">
                                        Share Link
                                    </button>
                                    <button onclick="copyLink('partner', this)" class="bg-white/80 hover:bg-white border border-slate-200 text-slate-700 hover:text-slate-900 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 px-6 py-3 font-medium flex-1 sm:flex-none">
                                        Copy Link
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<?php
include_once '_footer.php';
?>
