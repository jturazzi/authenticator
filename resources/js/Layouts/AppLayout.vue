<template>
  <div class="min-h-screen flex flex-col" :class="{ dark: isDark }">
    <div class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors">
    <nav class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center space-x-6">
            <Link href="/dashboard" class="text-xl font-bold text-indigo-600 dark:text-indigo-400 flex items-center space-x-2">
              <svg class="w-12 h-12 rounded-xl" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                <rect width="512" height="512" rx="112" fill="#4f46e5"/>
                <path d="M256 80 L400 152 C400 152 412 320 256 444 C100 320 112 152 112 152 Z" fill="none" stroke="#fff" stroke-width="28" stroke-linejoin="round"/>
                <path d="M218 248 L218 212 C218 176 294 176 294 212 L294 248" fill="none" stroke="#fff" stroke-width="22" stroke-linecap="round"/>
                <rect x="194" y="246" width="124" height="96" rx="18" fill="#fff"/>
                <circle cx="256" cy="284" r="15" fill="#4f46e5"/>
                <rect x="250" y="294" width="12" height="24" rx="6" fill="#4f46e5"/>
              </svg>
              <span class="hidden sm:inline">Authenticator</span>
            </Link>
            <template v-if="$page.props.auth?.user">
              <Link href="/dashboard" class="hidden sm:inline text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                {{ t('nav.myAccounts') }}
              </Link>
              <Link v-if="$page.props.auth.user.is_admin" href="/admin" class="hidden sm:inline text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                {{ t('nav.admin') }}
              </Link>
            </template>
          </div>
          <div class="flex items-center gap-2">
            <!-- Language toggle -->
            <button @click="toggleLocale" class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition text-xs font-bold uppercase">
              {{ locale === 'fr' ? 'EN' : 'FR' }}
            </button>
            <!-- Dark mode toggle -->
            <button @click="toggleDark" class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition" :title="t('nav.changeTheme')">
              <svg v-if="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            </button>
            <!-- Desktop user info -->
            <div v-if="$page.props.auth?.user" class="hidden sm:flex items-center space-x-4">
              <div class="flex items-center space-x-2">
                <img v-if="$page.props.auth.user.avatar" :src="$page.props.auth.user.avatar" alt="" class="w-8 h-8 rounded-full ring-2 ring-gray-100 dark:ring-gray-800">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $page.props.auth.user.name }}</span>
                <span v-if="$page.props.auth.user.is_admin" class="bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 text-xs font-semibold px-2 py-0.5 rounded-full">{{ t('admin.admin') }}</span>
              </div>
              <Link href="/logout" method="post" as="button" class="text-sm text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition">
                {{ t('nav.logout') }}
              </Link>
            </div>
            <!-- Mobile hamburger -->
            <button v-if="$page.props.auth?.user" @click="mobileMenuOpen = !mobileMenuOpen" class="sm:hidden p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
              <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
              <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
        </div>
      </div>
      <!-- Mobile menu -->
      <div v-if="mobileMenuOpen && $page.props.auth?.user" class="sm:hidden border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
        <div class="px-4 py-3 space-y-2">
          <div class="flex items-center space-x-2 pb-2 border-b border-gray-100 dark:border-gray-800">
            <img v-if="$page.props.auth.user.avatar" :src="$page.props.auth.user.avatar" alt="" class="w-8 h-8 rounded-full ring-2 ring-gray-100 dark:ring-gray-800">
            <div>
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300 block">{{ $page.props.auth.user.name }}</span>
              <span v-if="$page.props.auth.user.is_admin" class="bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 text-xs font-semibold px-2 py-0.5 rounded-full">{{ t('admin.admin') }}</span>
            </div>
          </div>
          <Link href="/dashboard" class="block text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white py-2 transition">
            {{ t('nav.myAccounts') }}
          </Link>
          <Link v-if="$page.props.auth.user.is_admin" href="/admin" class="block text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white py-2 transition">
            {{ t('nav.admin') }}
          </Link>
          <Link href="/logout" method="post" as="button" class="block w-full text-left text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 py-2 transition">
            {{ t('nav.logout') }}
          </Link>
        </div>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex-1 w-full">
      <div v-if="$page.props.flash?.success" class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg text-sm flex items-center space-x-2">
        <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <span>{{ flashMessage($page.props.flash.success) }}</span>
      </div>
      <div v-if="$page.props.flash?.error" class="mb-6 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 px-4 py-3 rounded-lg text-sm flex items-center space-x-2">
        <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
        <span>{{ flashMessage($page.props.flash.error) }}</span>
      </div>
      <slot />
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 mt-auto">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-gray-500 dark:text-gray-400">
        <div class="flex items-center gap-3">
          <a href="https://github.com/jturazzi/authenticator" target="_blank" rel="noopener noreferrer" class="font-semibold text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Authenticator <span class="font-normal text-gray-400 dark:text-gray-500">v1.0.0</span></a>
          <span class="text-gray-300 dark:text-gray-600">|</span>
          <Link href="/terms" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">{{ t('footer.terms') }}</Link>
        </div>
        <span>{{ t('footer.createdBy') }} <a href="https://github.com/jturazzi" target="_blank" rel="noopener noreferrer" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Jérémy TURAZZI</a></span>
      </div>
    </footer>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from '@/i18n.js';

const { t, locale, setLocale } = useI18n();
const mobileMenuOpen = ref(false);
const isDark = ref(false);

function flashMessage(msg) {
  if (!msg) return '';
  const [key, ...paramParts] = msg.split('|');
  const params = {};
  for (const part of paramParts) {
    const [k, v] = part.split(':');
    if (k && v !== undefined) {
      const tv = t(v);
      params[k] = tv !== v ? tv : v;
    }
  }
  const translated = t(key, params);
  return translated === key ? msg : translated;
}

onMounted(() => {
  isDark.value = localStorage.getItem('theme') === 'dark' ||
    (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
});

function toggleDark() {
  isDark.value = !isDark.value;
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
}

function toggleLocale() {
  setLocale(locale.value === 'fr' ? 'en' : 'fr');
}
</script>
