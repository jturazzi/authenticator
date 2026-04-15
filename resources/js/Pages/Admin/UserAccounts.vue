<template>
  <Head :title="t('userAccounts.title', { name: user.name })" />
  <AppLayout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-3">
      <div class="flex items-center min-w-0">
        <Link href="/admin" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 mr-4 transition shrink-0">{{ t('common.back') }}</Link>
        <div class="min-w-0">
          <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white truncate">{{ t('userAccounts.title', { name: user.name }) }}</h1>
          <p class="text-gray-500 dark:text-gray-400 text-sm truncate">{{ user.email }}</p>
        </div>
      </div>
      <Link :href="`/admin/users/${user.id}/totp/create`"
            class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm shrink-0">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        {{ t('userAccounts.addTotp') }}
      </Link>
    </div>

    <div v-if="accounts.length === 0" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-12 text-center">
      <p class="text-gray-500 dark:text-gray-400">{{ t('userAccounts.noAccounts') }}</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
      <div v-for="account in accounts" :key="account.id"
           @click="copyCode(account)"
           class="bg-white dark:bg-gray-800 rounded-xl p-4 sm:p-5 border border-gray-200 dark:border-gray-700 hover:border-indigo-200 dark:hover:border-indigo-700 hover:shadow-md transition-all cursor-pointer active:scale-[0.98] select-none"
           :class="{ 'border-green-300 bg-green-50 dark:border-green-700 dark:bg-green-900/30': copiedId === account.id }">
        <div class="flex items-center justify-between mb-3">
          <div class="min-w-0">
            <h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ account.description || account.name }}</h3>
          </div>
          <div class="flex items-center space-x-1 shrink-0 ml-2">
            <button @click.stop="toggleLock(account)"
                    class="p-1.5 rounded-lg transition"
                    :class="account.locked ? 'text-amber-500 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 hover:bg-amber-50 dark:hover:bg-amber-900/50' : 'text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/50'"
                    :title="account.locked ? t('userAccounts.unlock') : t('userAccounts.lock')">
              <svg v-if="account.locked" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
            </button>
            <Link @click.stop :href="`/admin/totp/${account.id}`" class="p-1.5 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/50 transition">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </Link>
            <button @click.stop="confirmDelete(account)"
                    class="p-1.5 rounded-lg transition"
                    :class="account.locked ? 'text-gray-300 dark:text-gray-600 cursor-not-allowed' : 'text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/50'"
                    :disabled="account.locked"
                    :title="account.locked ? t('userAccounts.deleteLocked') : t('common.delete')">
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
          <div class="relative w-10 h-10 shrink-0">
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
  user: Object,
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
  return getRemaining(account) <= 5 ? '#ef4444' : '#6366f1';
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

function confirmDelete(account) {
  if (account.locked) return;
  if (confirm(t('dashboard.confirmDelete'))) {
    router.delete(`/admin/totp/${account.id}`);
  }
}

function toggleLock(account) {
  router.patch(`/admin/totp/${account.id}/toggle-lock`);
}
</script>
