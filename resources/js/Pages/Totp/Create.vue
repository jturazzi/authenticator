<template>
  <Head :title="t('create.title')" />
  <AppLayout>
    <div class="max-w-lg mx-auto">
      <div class="flex items-center mb-6">
        <Link href="/dashboard" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 mr-4 transition shrink-0">{{ t('common.back') }}</Link>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ t('create.heading') }}</h1>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl p-4 sm:p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
        <form @submit.prevent="submit">
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('form.accountName') }}</label>
            <input v-model="form.name" type="text" id="name" required
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-800 outline-none transition"
                   :placeholder="t('form.placeholderName')">
            <p v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</p>
          </div>

          <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('form.description') }}</label>
            <input v-model="form.description" type="text" id="description"
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-800 outline-none transition"
                   :placeholder="t('form.placeholderDesc')">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ t('form.descriptionHelp') }}</p>
          </div>

          <div class="mb-6">
            <label for="secret" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('form.secretOptional') }}</label>
            <input v-model="form.secret" type="text" id="secret"
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-gray-900 dark:text-white bg-white dark:bg-gray-700 font-mono focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-800 outline-none transition"
                   :placeholder="t('form.leaveEmpty')">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ t('form.pasteSecret') }}</p>
          </div>

          <button type="submit" :disabled="form.processing"
                  class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-semibold py-3 px-6 rounded-lg transition shadow-sm">
            {{ t('create.submit') }}
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

const form = useForm({
  name: '',
  description: '',
  secret: '',
});

function submit() {
  form.post('/totp');
}
</script>
