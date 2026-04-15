<template>
  <Head :title="t('nav.admin')" />
  <AppLayout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-3">
      <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ t('admin.title') }}</h1>
      <Link href="/admin/info" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ t('info.title') }}
      </Link>
    </div>

    <!-- Desktop table -->
    <div class="hidden sm:block bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
          <tr>
            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ t('admin.user') }}</th>
            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ t('admin.email') }}</th>
            <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ t('admin.totpAccounts') }}</th>
            <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ t('admin.role') }}</th>
            <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ t('admin.createdAt') }}</th>
            <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ t('admin.lastLogin') }}</th>
            <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ t('admin.actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
          <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
            <td class="px-6 py-4">
              <div class="flex items-center space-x-3">
                <img v-if="user.avatar" :src="user.avatar" alt="" class="w-8 h-8 rounded-full">
                <div v-else class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-sm font-semibold">
                  {{ user.name.charAt(0).toUpperCase() }}
                </div>
                <span class="font-medium text-gray-900 dark:text-white">{{ user.name }}</span>
              </div>
            </td>
            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-sm">{{ user.email }}</td>
            <td class="px-6 py-4 text-center">
              <span class="bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 text-xs font-semibold px-2.5 py-1 rounded-full">
                {{ user.totp_accounts_count }}
              </span>
            </td>
            <td class="px-6 py-4 text-center">
              <span v-if="user.is_admin" class="bg-red-50 dark:bg-red-900/50 text-red-700 dark:text-red-400 text-xs font-semibold px-2.5 py-1 rounded-full">Admin</span>
              <span v-else class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs font-semibold px-2.5 py-1 rounded-full">{{ t('admin.user') }}</span>
            </td>
            <td class="px-6 py-4 text-center text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ formatDate(user.created_at) }}</td>
            <td class="px-6 py-4 text-center text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ formatDate(user.last_login_at) }}</td>
            <td class="px-6 py-4 text-right space-x-2">
              <Link :href="`/admin/users/${user.id}`" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium transition">
                {{ t('admin.viewAccounts') }}
              </Link>
              <Link v-if="user.id !== $page.props.auth.user.id"
                    :href="`/admin/users/${user.id}/toggle-admin`"
                    method="patch" as="button"
                    @click.prevent="toggleAdmin(user)"
                    class="text-amber-600 hover:text-amber-800 dark:text-amber-400 dark:hover:text-amber-300 text-sm font-medium transition">
                {{ user.is_admin ? t('admin.removeAdmin') : t('admin.makeAdmin') }}
              </Link>
              <button v-if="user.id !== $page.props.auth.user.id && user.totp_accounts_count === 0"
                      @click="confirmDeleteUser(user)"
                      class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium transition">
                {{ t('common.delete') }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mobile cards -->
    <div class="sm:hidden space-y-3">
      <div v-for="user in users.data" :key="user.id"
           class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center space-x-3 min-w-0">
            <img v-if="user.avatar" :src="user.avatar" alt="" class="w-10 h-10 rounded-full shrink-0">
            <div v-else class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-sm font-semibold shrink-0">
              {{ user.name.charAt(0).toUpperCase() }}
            </div>
            <div class="min-w-0">
              <span class="font-medium text-gray-900 dark:text-white block truncate">{{ user.name }}</span>
              <span class="text-gray-500 dark:text-gray-400 text-xs block truncate">{{ user.email }}</span>
            </div>
          </div>
          <div class="shrink-0 ml-2">
            <span v-if="user.is_admin" class="bg-red-50 dark:bg-red-900/50 text-red-700 dark:text-red-400 text-xs font-semibold px-2 py-0.5 rounded-full">Admin</span>
            <span v-else class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs font-semibold px-2 py-0.5 rounded-full">User</span>
          </div>
        </div>
        <div class="flex items-center justify-between">
          <span class="bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 text-xs font-semibold px-2.5 py-1 rounded-full">
            {{ user.totp_accounts_count }} {{ user.totp_accounts_count !== 1 ? t('admin.accounts') : t('admin.account') }}
          </span>
          <div class="flex items-center gap-3">
            <Link :href="`/admin/users/${user.id}`" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium transition">
              {{ t('admin.view') }}
            </Link>
            <button v-if="user.id !== $page.props.auth.user.id"
                    @click="toggleAdmin(user)"
                    class="text-amber-600 hover:text-amber-800 dark:text-amber-400 dark:hover:text-amber-300 text-sm font-medium transition">
              {{ user.is_admin ? t('admin.removeAdmin') : t('admin.makeAdmin') }}
            </button>
            <button v-if="user.id !== $page.props.auth.user.id && user.totp_accounts_count === 0"
                    @click="confirmDeleteUser(user)"
                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium transition">
              {{ t('common.delete') }}
            </button>
          </div>
        </div>
        <div class="mt-3 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
          <span>{{ t('admin.createdAt') }}: {{ formatDate(user.created_at) }}</span>
          <span>{{ t('admin.lastLogin') }}: {{ formatDate(user.last_login_at) }}</span>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useI18n } from '@/i18n.js';

const { t } = useI18n();

defineProps({
  users: Object,
});

function formatDate(dateStr) {
  if (!dateStr) return t('admin.never');
  const d = new Date(dateStr);
  return d.toLocaleDateString(undefined, { day: '2-digit', month: '2-digit', year: 'numeric' }) + ' ' + d.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });
}

function toggleAdmin(user) {
  if (confirm(t('admin.confirmRoleChange', { name: user.name }))) {
    router.patch(`/admin/users/${user.id}/toggle-admin`);
  }
}

function confirmDeleteUser(user) {
  if (confirm(t('admin.confirmDeleteUser', { name: user.name }))) {
    router.delete(`/admin/users/${user.id}`);
  }
}
</script>
