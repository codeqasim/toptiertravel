
<!-- Header Section -->
<div class="text-center mb-8">
    <div class="mb-6">
        <img class="h-16 w-16 mx-auto rounded-2xl shadow-lg" src="../uploads/global/favicon.png" alt="favicon">
    </div>
    <h1 class="text-3xl font-bold text-slate-900 mb-2">Welcome Back</h1>
    <p class="text-slate-600">Sign in to your agent account</p>
</div>

<!-- Login Form -->
<form name="form" action="login.php" method="post" onsubmit="submission()" class="space-y-6">
    <!-- Email Field -->
    <div class="space-y-2">
        <label for="email" class="block text-sm font-medium text-slate-700">Email Address</label>
        <div class="relative">
            <input 
                type="email" 
                id="email" 
                name="email"
                placeholder="Enter your email"
                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300 bg-white/80 backdrop-blur-sm"
                required
            >
            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Password Field -->
    <div class="space-y-2">
        <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
        <div class="relative">
            <input 
                type="password" 
                id="password" 
                name="password"
                placeholder="Enter your password"
                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300 bg-white/80 backdrop-blur-sm"
                required
            >
            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Hidden Language Selection -->
    <select class="hidden" name="user_language">
        <?php foreach($languages as $lang){
        if ($lang->status == 1){
        ?>
        <option selected value="<?=strtolower($lang->country_id)?>_<?=strtolower($lang->type)?>">
            <?=$lang->name?>
        </option>
        <?php } } ?>
    </select>

    <script>
        $("[name='user_language']").val("us_ltr")
    </script>

    <!-- Remember Me & Forgot Password -->
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <input 
                id="remember-me" 
                name="remember-me" 
                type="checkbox" 
                checked
                class="h-4 w-4 text-violet-600 focus:ring-violet-500 border-slate-300 rounded transition-colors"
            >
            <label for="remember-me" class="ml-2 text-sm text-slate-600">
                Remember me
            </label>
        </div>
        <div>
            <a href="login-forget-password.php" class="text-sm font-medium text-violet-600 hover:text-violet-500 transition-colors">
                Forgot password?
            </a>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="space-y-3">
        <button 
            id="submit" 
            type="submit"
            class="login_button w-full bg-gradient-to-r from-violet-600 to-purple-600 text-white py-3 px-4 rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transition-all duration-300 btn-hover focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2"
        >
            Sign In
        </button>
        
        <!-- Loading Button (Hidden by default) -->
        <button 
            class="loading_button hidden w-full bg-gradient-to-r from-violet-600 to-purple-600 text-white py-3 px-4 rounded-xl font-semibold text-lg shadow-lg opacity-75 cursor-not-allowed" 
            type="button" 
            disabled
        >
            <div class="flex items-center justify-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Signing in...
            </div>
        </button>
    </div>

    <!-- CSRF Token -->
    <input type="hidden" name="form_token" value="<?=$_SESSION["form_token"]?>">
</form>

<!-- Mobile Responsive Message -->
<div class="lg:hidden mt-8 p-4 bg-violet-50 rounded-xl border border-violet-200">
    <div class="flex items-center">
        <svg class="w-5 h-5 text-violet-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-sm text-violet-700">Please login only if you have an agent account</p>
    </div>
</div>