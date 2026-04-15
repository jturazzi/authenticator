<template>
  <Head :title="t('create.title')" />
  <AppLayout>
    <div class="max-w-lg mx-auto">
      <div class="flex items-center mb-6">
        <Link :href="`/admin/users/${user.id}`" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 mr-4 transition shrink-0">{{ t('common.back') }}</Link>
        <div class="min-w-0">
          <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ t('adminCreate.heading') }}</h1>
          <p class="text-gray-500 dark:text-gray-400 text-sm truncate">{{ t('adminCreate.forUser', { name: user.name, email: user.email }) }}</p>
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
            <label for="issuer" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('form.issuer') }}</label>
            <input v-model="form.issuer" type="text" id="issuer"
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-800 outline-none transition"
                   :placeholder="t('form.placeholderIssuer')">
          </div>

          <div class="mb-6">
            <label for="secret" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('form.secretOptional') }}</label>
            <input v-model="form.secret" type="text" id="secret"
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-gray-900 dark:text-white bg-white dark:bg-gray-700 font-mono focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-800 outline-none transition"
                   :placeholder="t('form.leaveEmpty')">
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

const props = defineProps({
  user: Object,
});

const form = useForm({
  name: '',
  description: '',
  issuer: '',
  secret: '',
});

function submit() {
  form.post(`/admin/users/${props.user.id}/totp`);
}
</script>
