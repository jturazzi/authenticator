<template>
  <Head :title="totp.description || totp.name" />
  <AppLayout>
    <div class="max-w-lg mx-auto">
      <div class="flex items-center mb-6">
        <Link :href="`/admin/users/${totp.user.id}`" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 mr-4 transition shrink-0">{{ t('common.back') }}</Link>
      </div>

      <!-- Hero header with code + circular timer -->
      <div @click="copyCode"
           class="relative overflow-hidden rounded-2xl p-6 sm:p-8 text-center cursor-pointer active:scale-[0.98] transition-all duration-200 select-none mb-6"
           :class="copied
             ? 'bg-green-500 dark:bg-green-600'
             : 'bg-gradient-to-br from-indigo-600 to-indigo-800 dark:from-indigo-700 dark:to-indigo-900'">
        <div class="absolute inset-0 opacity-10">
          <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-white"></div>
          <div class="absolute -bottom-10 -left-10 w-32 h-32 rounded-full bg-white"></div>
        </div>
        <div class="relative">
          <h1 class="text-lg sm:text-xl font-semibold text-white/90 mb-1 truncate">{{ totp.description || totp.name }}</h1>
          <p class="text-white/60 text-sm mb-4 truncate">{{ totp.user.name }}</p>

          <!-- Code -->
          <div class="text-4xl sm:text-5xl font-mono font-bold text-white tracking-[0.3em] mb-3">
            <span v-if="copied" class="flex items-center justify-center gap-2 text-3xl tracking-normal">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              {{ t('common.copied') }}
            </span>
            <span v-else>{{ formatCode(currentCode) }}</span>
          </div>

          <!-- Circular timer -->
          <div class="flex items-center justify-center gap-3">
            <div class="relative w-12 h-12">
              <svg class="w-12 h-12 transform -rotate-90" viewBox="0 0 48 48">
                <circle cx="24" cy="24" r="20" stroke="rgba(255,255,255,0.2)" stroke-width="3" fill="none"/>
                <circle cx="24" cy="24" r="20"
                        :stroke="remaining <= 5 ? '#fca5a5' : '#fff'"
                        stroke-width="3" fill="none"
                        stroke-dasharray="125.6"
                        :stroke-dashoffset="timerOffset"
                        stroke-linecap="round"
                        class="transition-all duration-1000"/>
              </svg>
              <span class="absolute inset-0 flex items-center justify-center text-sm font-bold"
                    :class="remaining <= 5 ? 'text-red-200' : 'text-white'">
                {{ remaining }}
              </span>
            </div>
            <span class="text-white/60 text-xs">{{ t('show.seconds') }}</span>
          </div>
        </div>
      </div>

      <!-- QR Code -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 border border-gray-200 dark:border-gray-700 shadow-sm mb-4 text-center">
        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ t('show.qrCode') }}</h3>
        <div class="bg-white rounded-xl p-3 sm:p-4 inline-block border border-gray-100 [&>svg]:w-40 [&>svg]:h-40 sm:[&>svg]:w-52 sm:[&>svg]:h-52" v-html="qrCode"></div>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-3">{{ t('show.scanWithAdmin') }}</p>
      </div>

      <!-- Details -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="grid grid-cols-3 divide-x divide-gray-100 dark:divide-gray-700">
          <div class="px-4 py-4 text-center">
            <span class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider block mb-1">{{ t('show.algo') }}</span>
            <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ totp.algorithm.toUpperCase() }}</span>
          </div>
          <div class="px-4 py-4 text-center">
            <span class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider block mb-1">{{ t('show.digits') }}</span>
            <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ totp.digits }}</span>
          </div>
          <div class="px-4 py-4 text-center">
            <span class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider block mb-1">{{ t('show.period') }}</span>
            <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ totp.period }}s</span>
          </div>
        </div>
        <div v-if="totp.issuer" class="px-5 py-3 bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-700 flex items-center gap-2">
          <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
          <span class="text-sm text-gray-500 dark:text-gray-400">{{ totp.issuer }}</span>
        </div>
      </div>

      <!-- Edit button -->
      <div class="mt-4">
        <Link :href="`/admin/totp/${totp.id}/edit`"
              class="flex items-center justify-center gap-2 w-full bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium py-3 px-6 rounded-xl border border-gray-200 dark:border-gray-700 transition shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
          {{ t('show.edit') }}
        </Link>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useI18n } from '@/i18n.js';

const { t } = useI18n();

const props = defineProps({
  totp: Object,
  code: String,
  qrCode: String,
});

const now = ref(Math.floor(Date.now() / 1000));
const currentCode = ref(props.code);
const copied = ref(false);
let timer = null;
let copiedTimeout = null;

const remaining = computed(() => {
  const period = props.totp.period || 30;
  return period - (now.value % period);
});

const timerOffset = computed(() => {
  const period = props.totp.period || 30;
  return 125.6 - (remaining.value / period) * 125.6;
});

onMounted(() => {
  timer = setInterval(async () => {
    now.value = Math.floor(Date.now() / 1000);
    const period = props.totp.period || 30;
    if (now.value % period === 0) {
      try {
        const res = await fetch(`/totp/${props.totp.id}/code`);
        const data = await res.json();
        currentCode.value = data.code;
      } catch (e) {}
    }
  }, 1000);
});

onUnmounted(() => {
  if (timer) clearInterval(timer);
});

function formatCode(code) {
  if (!code) return '--- ---';
  return code.substring(0, 3) + ' ' + code.substring(3);
}

function copyCode() {
  if (!currentCode.value) return;
  navigator.clipboard.writeText(currentCode.value).then(() => {
    copied.value = true;
    if (copiedTimeout) clearTimeout(copiedTimeout);
    copiedTimeout = setTimeout(() => { copied.value = false; }, 1500);
  });
}
</script>
