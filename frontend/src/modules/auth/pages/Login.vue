<script setup lang="ts">
import { ref } from 'vue'

const email = ref('kalkidan@gmail.com')
const password = ref('password')
const rememberMe = ref(false)
const showPassword = ref(false)

const handleLogin = () => {
  console.log('Logging in with:', email.value, password.value)
  // Integration with backend will happen here
}
</script>

<template>
  <div class="min-h-screen bg-[#f8fafc] flex items-center justify-center p-4 sm:p-8 font-sans">
    
    <!-- Main Card Container -->
    <div class="flex flex-col md:flex-row w-full max-w-5xl bg-white rounded-2xl shadow-xl overflow-hidden">
      
      <!-- Left Side (Branding & Image) -->
      <div class="hidden md:flex flex-col w-1/2 bg-[#283593] text-white p-10 relative overflow-hidden">
        
        <!-- Background Image with Gradient Overlay -->
        <div class="absolute inset-0 z-0 bg-[#1a237e]">
          <img 
            src="../../../assets/images/bac.png" 
            alt="Wollo University" 
            class="w-[180%] max-w-none h-auto absolute bottom-0 left-1/2 -translate-x-1/2 opacity-80"
          />
          <div class="absolute inset-0 bg-gradient-to-b from-[#283593]/80 via-[#283593]/20 to-[#283593]/80"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 flex flex-col h-full">
          <!-- Logo & Title -->
          <div class="flex items-center gap-4 mb-12">
            <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-lg p-0.5">
              <img src="../../../assets/images/logo.png" alt="Wollo University Logo" class="w-full h-full object-contain rounded-full" />
            </div>
            <div>
              <h2 class="text-xl font-bold tracking-wide">Wollo University</h2>
              <p class="text-sm text-blue-200 font-medium">Online Examination System</p>
            </div>
          </div>

          <!-- Hero Text -->
          <div class="mt-4">
            <h1 class="text-3xl font-extrabold leading-tight mb-5">
              Excellence in Education,<br/>
              Innovation in Assessment
            </h1>
            <div class="w-12 h-1 bg-blue-500 mb-5 rounded-full"></div>
            <p class="text-blue-100 text-sm leading-relaxed max-w-sm">
              A secure, reliable and user-friendly platform for managing examinations and evaluating academic excellence.
            </p>
          </div>

          <!-- Glassmorphism Badge at Bottom -->
          <div class="mt-auto pt-10">
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-4 flex items-center gap-4">
              <div class="bg-white/10 p-2 rounded-full">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
              </div>
              <div>
                <h4 class="font-semibold text-white text-sm">Secure & Protected</h4>
                <p class="text-xs text-blue-200">Your data is safe with enterprise grade security</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Side (Login Form) -->
      <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
        
        <div class="mb-8">
          <h2 class="text-2xl font-bold text-slate-900 mb-1">Welcome Back 👋</h2>
          <p class="text-sm text-slate-500">Sign in to continue to your account</p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-5">
          
          <!-- Email Input -->
          <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Email Address</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <input 
                id="email" 
                type="email" 
                v-model="email"
                placeholder="Enter your email address" 
                class="block w-full pl-9 pr-3 py-2.5 bg-[#f4f7fb] border border-slate-200 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#5138ed] focus:bg-white transition-all"
                required
              />
            </div>
          </div>

          <!-- Password Input -->
          <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">Password</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
              </div>
              <input 
                id="password" 
                :type="showPassword ? 'text' : 'password'" 
                v-model="password"
                placeholder="Enter your password" 
                class="block w-full pl-9 pr-10 py-2.5 bg-[#f4f7fb] border border-slate-200 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#5138ed] focus:bg-white transition-all font-medium tracking-widest"
                required
              />
              <button 
                type="button" 
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
              >
                <svg v-if="!showPassword" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
              </button>
            </div>
            <div class="mt-2 text-right">
              <a href="#" class="text-xs font-semibold text-[#5138ed] hover:text-[#3f2cb8]">Forgot Password?</a>
            </div>
          </div>

          <!-- Remember Me -->
          <div class="flex items-center pb-2">
            <input 
              id="remember-me" 
              type="checkbox" 
              v-model="rememberMe"
              class="h-3.5 w-3.5 text-[#5138ed] focus:ring-[#5138ed] border-slate-300 rounded cursor-pointer"
            />
            <label for="remember-me" class="ml-2 block text-xs text-slate-600 cursor-pointer">
              Remember me
            </label>
          </div>

          <!-- Sign In Button -->
          <div>
            <button 
              type="submit" 
              class="w-full flex justify-center items-center gap-2 py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-[#5138ed] hover:bg-[#3f2cb8] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#5138ed] transition-colors"
            >
              Sign In
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
              </svg>
            </button>
          </div>
        </form>

        <!-- Divider -->
        <div class="mt-8 relative">
          <div class="absolute inset-0 flex items-center" aria-hidden="true">
            <div class="w-full border-t border-slate-200"></div>
          </div>
          <div class="relative flex justify-center">
            <span class="px-2 bg-white text-[11px] text-slate-400 uppercase tracking-wider font-medium">or continue with</span>
          </div>
        </div>

        <!-- Social Login -->
        <div class="mt-6 grid grid-cols-2 gap-3">
          <button class="flex justify-center items-center gap-2 py-2 px-3 border border-slate-200 rounded-lg shadow-sm bg-white text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
            <svg class="h-4 w-4" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
            Google
          </button>
          <button class="flex justify-center items-center gap-2 py-2 px-3 border border-slate-200 rounded-lg shadow-sm bg-white text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
            <svg class="h-4 w-4" viewBox="0 0 21 21" xmlns="http://www.w3.org/2000/svg"><path d="M10 0H0v10h10V0z" fill="#f25022"/><path d="M21 0H11v10h10V0z" fill="#7fba00"/><path d="M10 11H0v10h10V-11z" fill="#00a4ef"/><path d="M21 11H11v10h10V-11z" fill="#ffb900"/></svg>
            Microsoft
          </button>
        </div>

        <div class="mt-8 text-center text-xs text-slate-500">
          Don't have an account? 
          <a href="#" class="font-semibold text-[#5138ed] hover:text-[#3f2cb8]">Contact your administrator</a>
        </div>

      </div>
    </div>
  </div>
</template>
