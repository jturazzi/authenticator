<template>
  <Head :title="t('show.edit')" />
  <AppLayout>
    <div class="max-w-lg mx-auto">
      <div class="flex items-center mb-6">
        <Link :href="`/admin/totp/${totp.id}`" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 mr-4 transition shrink-0">{{ t('common.back') }}</Link>
        <div class="min-w-0">
          <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ t('edit.heading') }}</h1>
          <p class="text-gray-500 dark:text-gray-400 text-sm truncate">{{ totp.user.name }} ({{ totp.user.email }})</p>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl p-4 sm:p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
        <form @submit.prevent="submit">
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('form.accountName') }}</label>
            <input v-model="form.name" type="text" id="name" required
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-800 outline-none transition"
                   :placeholder="t('form.placeholderName')">
          </div>

          <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('form.description') }}</label>
            <input v-model="form.description" type="text" id="description"
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-800 outline-none transition"
                   :placeholder="t('form.placeholderDesc')">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ t('form.descriptionHelp') }}</p>
          </div>

          <div class="mb-4">
            <label for="secret" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('form.secretRequired') }}</label>
            <input v-model="form.secret" type="text" id="secret" required
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-gray-900 dark:text-white bg-white dark:bg-gray-700 font-mono focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-800 outline-none transition">
          </div>

          <div class="grid grid-cols-3 gap-3 mb-6">
            <div>
              <label for="digits" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('form.digits') }}</label>
              <select v-model="form.digits" id="digits"
                      class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-800 outline-none transition">
                <option :value="6">6</option>
                <option :value="8">8</option>
              </select>
            </div>
            <div>
              <label for="period" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('form.period') }}</label>
              <input v-model.number="form.period" type="number" id="period" min="10" max="120"
                     class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-800 outline-none transition">
            </div>
            <div>
              <label for="algorithm" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('form.algorithm') }}</label>
              <select v-model="form.algorithm" id="algorithm"
                      class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-800 outline-none transition">
                <option value="sha1">SHA-1</option>
                <option value="sha256">SHA-256</option>
                <option value="sha512">SHA-512</option>
              </select>
            </div>
          </div>

          <button type="submit" :disabled="form.processing"
                  class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-semibold py-3 px-6 rounded-lg transition shadow-sm">
            {{ t('edit.submit') }}
          </button>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useI18n } from '@/i18n.js';

const { t } = useI18n();

const props = defineProps({
  totp: Object,
});

const form = useForm({
  name: props.totp.name,
  description: props.totp.description || '',
  secret: props.totp.secret,
  digits: props.totp.digits,
  period: props.totp.period,
  algorithm: props.totp.algorithm,
});

function submit() {
  form.put(`/admin/totp/${props.totp.id}`);
}
</script>
