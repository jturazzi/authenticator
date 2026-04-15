<template>
  <Head :title="t('login.title')" />
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <div class="flex-1 flex items-center justify-center px-4">
      <div class="bg-white rounded-2xl shadow-lg p-8 max-w-md w-full border border-gray-100">
        <div class="text-center mb-8">
          <div class="flex justify-center mb-4">
            <svg class="w-24 h-24 rounded-2xl" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
              <rect width="512" height="512" rx="112" fill="#4f46e5"/>
              <path d="M256 80 L400 152 C400 152 412 320 256 444 C100 320 112 152 112 152 Z" fill="none" stroke="#fff" stroke-width="28" stroke-linejoin="round"/>
              <path d="M218 248 L218 212 C218 176 294 176 294 212 L294 248" fill="none" stroke="#fff" stroke-width="22" stroke-linecap="round"/>
              <rect x="194" y="246" width="124" height="96" rx="18" fill="#fff"/>
              <circle cx="256" cy="284" r="15" fill="#4f46e5"/>
              <rect x="250" y="294" width="12" height="24" rx="6" fill="#4f46e5"/>
            </svg>
          </div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ t('login.appName') }}</h1>
          <p class="text-gray-500 text-sm">{{ t('login.subtitle') }}</p>
        </div>

        <div class="flex justify-center mb-6">
          <button @click="toggleLocale" class="text-xs font-bold uppercase text-gray-500 hover:text-indigo-600 px-3 py-1.5 rounded-lg hover:bg-gray-100 transition">
            {{ locale === 'fr' ? 'EN' : 'FR' }}
          </button>
        </div>

        <a :href="microsoftUrl"
           class="flex items-center justify-center w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 space-x-3 shadow-sm">
          <svg class="w-5 h-5" viewBox="0 0 23 23" fill="none">
            <rect width="11" height="11" fill="#f25022"/>
            <rect x="12" width="11" height="11" fill="#7fba00"/>
            <rect y="12" width="11" height="11" fill="#00a4ef"/>
            <rect x="12" y="12" width="11" height="11" fill="#ffb900"/>
          </svg>
          <span>{{ t('login.microsoft') }}</span>
        </a>
      </div>
    </div>
    <footer class="border-t border-gray-200 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-gray-500">
        <div class="flex items-center gap-3">
          <a href="https://github.com/jturazzi/authenticator" target="_blank" rel="noopener noreferrer" class="font-semibold text-gray-700 hover:text-indigo-600 transition">Authenticator <span class="font-normal text-gray-400">v1.0.0</span></a>
          <span class="text-gray-300">|</span>
          <a href="/terms" class="hover:text-indigo-600 transition">{{ t('footer.terms') }}</a>
        </div>
        <span>{{ t('footer.createdBy') }} <a href="https://github.com/jturazzi" target="_blank" rel="noopener noreferrer" class="hover:text-indigo-600 transition">Jérémy TURAZZI</a></span>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from '@/i18n.js';

const { t, locale, setLocale } = useI18n();

defineProps({
  microsoftUrl: String,
});

function toggleLocale() {
  setLocale(locale.value === 'fr' ? 'en' : 'fr');
}
</script>
