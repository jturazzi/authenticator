<template>
  <Head :title="totp.name" />
  <AppLayout>
    <div class="max-w-lg mx-auto">
      <div class="flex items-center mb-6">
        <Link href="/dashboard" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 mr-4 transition shrink-0">{{ t('common.back') }}</Link>
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
          <h1 class="text-lg sm:text-xl font-semibold text-white/90 mb-1 truncate">{{ totp.name }}</h1>
          <div class="mb-4"></div>

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
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-3">{{ t('show.scanWith') }}</p>
      </div>

      <!-- Details -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div v-if="totp.description" class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
          <span class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ t('show.description') }}</span>
          <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ totp.description }}</p>
        </div>
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
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
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
