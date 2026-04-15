<template>
  <Head :title="t('info.title')" />
  <AppLayout>
    <div class="max-w-2xl mx-auto">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ t('info.title') }}</h1>
        <Link href="/admin" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 text-sm transition">{{ t('common.back') }}</Link>
      </div>

      <!-- Application -->
      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ t('info.application') }}</h2>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
          <div class="px-5 py-3 flex justify-between items-center" v-for="item in appItems" :key="item.label">
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ item.label }}</span>
            <span class="text-sm font-medium text-gray-900 dark:text-white font-mono">{{ item.value }}</span>
          </div>
        </div>
      </div>

      <!-- Environment -->
      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ t('info.environment') }}</h2>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
          <div class="px-5 py-3 flex justify-between items-center" v-for="item in envItems" :key="item.label">
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ item.label }}</span>
            <span class="text-sm font-medium text-gray-900 dark:text-white font-mono">{{ item.value }}</span>
          </div>
        </div>
      </div>

      <!-- Statistics -->
      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ t('info.statistics') }}</h2>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
          <div class="px-5 py-3 flex justify-between items-center" v-for="item in statItems" :key="item.label">
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ item.label }}</span>
            <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 font-mono">{{ item.value }}</span>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useI18n } from '@/i18n.js';
import { computed } from 'vue';

const { t } = useI18n();

const props = defineProps({
  appVersion: String,
  phpVersion: String,
  laravelVersion: String,
  serverSoftware: String,
  database: String,
  cacheDriver: String,
  sessionDriver: String,
  timezone: String,
  usersCount: Number,
  totpCount: Number,
});

const appItems = computed(() => [
  { label: t('info.appVersion'), value: `v${props.appVersion}` },
  { label: t('info.phpVersion'), value: props.phpVersion },
  { label: t('info.laravelVersion'), value: props.laravelVersion },
  { label: t('info.server'), value: props.serverSoftware },
]);

const envItems = computed(() => [
  { label: t('info.database'), value: props.database },
  { label: t('info.cache'), value: props.cacheDriver },
  { label: t('info.session'), value: props.sessionDriver },
  { label: t('info.timezone'), value: props.timezone },
]);

const statItems = computed(() => [
  { label: t('info.usersCount'), value: props.usersCount },
  { label: t('info.totpCount'), value: props.totpCount },
]);
</script>
