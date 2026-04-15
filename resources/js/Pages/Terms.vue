<template>
  <Head :title="t('terms.title')" />
  <AppLayout>
    <div class="max-w-3xl mx-auto">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-6">{{ t('terms.title') }}</h1>

      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 sm:p-8 border border-gray-200 dark:border-gray-700 shadow-sm space-y-6 text-gray-700 dark:text-gray-300 text-sm leading-relaxed">

        <section>
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ t('terms.section1Title') }}</h2>
          <p>{{ t('terms.section1Text') }}</p>
        </section>

        <section>
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ t('terms.section2Title') }}</h2>
          <p>{{ t('terms.section2Text') }}</p>
        </section>

        <section>
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ t('terms.section3Title') }}</h2>
          <p>{{ t('terms.section3Text') }}</p>
        </section>

        <section>
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ t('terms.section4Title') }}</h2>
          <p>{{ t('terms.section4Text') }}</p>
        </section>

        <section>
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ t('terms.section5Title') }}</h2>
          <p>{{ t('terms.section5Text') }}</p>
        </section>

      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useI18n } from '@/i18n.js';

const { t } = useI18n();
</script>
