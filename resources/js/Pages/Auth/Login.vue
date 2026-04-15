<template>
  <Head :title="t('login.title')" />
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <div class="flex-1 flex items-center justify-center px-4">
      <div class="bg-white rounded-2xl shadow-lg p-8 max-w-md w-full border border-gray-100">
        <div class="text-center mb-8">
          <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
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
