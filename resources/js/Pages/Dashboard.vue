<template>
  <Head :title="t('dashboard.headTitle')" />
  <AppLayout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-3">
      <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ t('dashboard.title') }}</h1>
      <div class="flex space-x-3">
        <Link href="/totp/scan" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          {{ t('dashboard.scan') }}
        </Link>
        <Link href="/totp/create" class="inline-flex items-center bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          {{ t('dashboard.add') }}
        </Link>
      </div>
    </div>

    <div v-if="accounts.length === 0" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 sm:p-16 text-center">
      <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
      </div>
      <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">{{ t('dashboard.noAccounts') }}</h2>
      <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">{{ t('dashboard.noAccountsDesc') }}</p>
      <div class="flex flex-col sm:flex-row justify-center gap-3">
        <Link href="/totp/scan" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm text-center">
          {{ t('dashboard.scanQr') }}
        </Link>
        <Link href="/totp/create" class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm text-center">
          {{ t('dashboard.addManually') }}
        </Link>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
      <div v-for="account in accounts" :key="account.id"
           @click="copyCode(account)"
           class="bg-white dark:bg-gray-800 rounded-xl p-4 sm:p-5 border border-gray-200 dark:border-gray-700 hover:border-indigo-200 dark:hover:border-indigo-700 hover:shadow-md transition-all duration-200 cursor-pointer active:scale-[0.98] select-none"
           :class="copiedId === account.id ? 'border-green-300 bg-green-50 dark:border-green-700 dark:bg-green-900/30' : ''">
        <div class="flex items-center justify-between mb-3">
          <div class="min-w-0">
            <h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ account.description || account.name }}</h3>
          </div>
          <div class="flex items-center space-x-1 shrink-0 ml-2">
            <Link @click.stop :href="`/totp/${account.id}`" class="p-1.5 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/50 transition" :title="t('dashboard.details')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </Link>
            <button v-if="account.locked" @click.stop class="p-1.5 text-amber-400 dark:text-amber-500 cursor-not-allowed" :title="t('dashboard.deleteLocked')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </button>
            <button v-else @click.stop="confirmDelete(account)"
                  class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/50 transition" :title="t('common.delete')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
        </div>

        <div class="flex items-center justify-between">
          <div class="text-2xl sm:text-3xl font-mono font-bold tracking-widest transition-colors"
               :class="copiedId === account.id ? 'text-green-600 dark:text-green-400' : 'text-indigo-600 dark:text-indigo-400'">
            <span v-if="copiedId === account.id" class="flex items-center gap-1">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              {{ t('common.copied') }}
            </span>
            <span v-else>{{ formatCode(codes[account.id]) }}</span>
          </div>
          <div class="relative w-10 h-10">
            <svg class="w-10 h-10 transform -rotate-90" viewBox="0 0 40 40">
              <circle cx="20" cy="20" r="18" class="stroke-gray-200 dark:stroke-gray-600" stroke-width="3" fill="none"/>
              <circle cx="20" cy="20" r="18"
                      :stroke="getTimerColor(account)"
                      stroke-width="3" fill="none"
                      stroke-dasharray="113"
                      :stroke-dashoffset="getTimerOffset(account)"
                      stroke-linecap="round"
                      class="transition-all duration-1000"/>
            </svg>
            <span class="absolute inset-0 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300">
              {{ getRemaining(account) }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useI18n } from '@/i18n.js';

const { t } = useI18n();

const props = defineProps({
  accounts: Array,
  codes: Object,
});

const now = ref(Math.floor(Date.now() / 1000));
const copiedId = ref(null);
let timer = null;
let copiedTimeout = null;

onMounted(() => {
  timer = setInterval(() => {
    const newNow = Math.floor(Date.now() / 1000);
    // Refresh codes when any period boundary is crossed
    const needsRefresh = props.accounts.some(a => {
      const period = a.period || 30;
      return newNow % period === 0;
    });
    now.value = newNow;
    if (needsRefresh) {
      router.reload({ only: ['codes'] });
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

function getRemaining(account) {
  const period = account.period || 30;
  return period - (now.value % period);
}

function getTimerOffset(account) {
  const period = account.period || 30;
  const remaining = period - (now.value % period);
  return 113 - (remaining / period) * 113;
}

function getTimerColor(account) {
  const remaining = getRemaining(account);
  return remaining <= 5 ? '#ef4444' : '#6366f1';
}

function confirmDelete(account) {
  if (confirm(t('dashboard.confirmDelete'))) {
    router.delete(`/totp/${account.id}`);
  }
}

function copyCode(account) {
  const code = props.codes[account.id];
  if (!code) return;
  navigator.clipboard.writeText(code).then(() => {
    copiedId.value = account.id;
    if (copiedTimeout) clearTimeout(copiedTimeout);
    copiedTimeout = setTimeout(() => { copiedId.value = null; }, 1500);
  });
}
</script>
