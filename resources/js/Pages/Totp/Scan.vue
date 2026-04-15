<template>
  <Head :title="t('scan.title')" />
  <AppLayout>
    <div class="max-w-lg mx-auto">
      <div class="flex items-center mb-6">
        <Link href="/dashboard" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 mr-4 transition shrink-0">{{ t('common.back') }}</Link>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ t('scan.title') }}</h1>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl p-4 sm:p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
        <!-- Scanner -->
        <div v-if="!scanned" id="scanner-container">
          <div class="bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden aspect-square relative border border-gray-200 dark:border-gray-600">
            <video ref="videoEl" class="w-full h-full object-cover" playsinline></video>
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
              <div class="w-36 h-36 sm:w-48 sm:h-48 border-2 border-indigo-400 rounded-xl opacity-60"></div>
            </div>
          </div>
          <div class="mt-3 flex justify-center space-x-3">
            <button v-if="!scanning" @click="startScanner"
                    class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              {{ t('scan.startCamera') }}
            </button>
            <button v-else @click="stopScanner"
                    class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
              {{ t('scan.stop') }}
            </button>
          </div>
        </div>

        <!-- Form after scan -->
        <form v-else @submit.prevent="submit">
          <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg p-3 mb-4">
            <p class="text-green-700 dark:text-green-400 text-sm font-medium flex items-center">
              <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
              {{ t('scan.detected') }}
            </p>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ scanLabel }}</p>
          </div>
          <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('scan.accountLabel') }}</label>
            <input v-model="form.description" ref="descriptionInput" type="text" id="description" required
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-800 outline-none transition"
                   :placeholder="t('scan.placeholderDesc')">
            <p v-if="form.errors.description" class="text-red-600 text-sm mt-1">{{ form.errors.description }}</p>
          </div>
          <button type="submit" :disabled="form.processing"
                  class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-semibold py-3 px-6 rounded-lg transition shadow-sm">
            {{ t('scan.submit') }}
          </button>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, nextTick, onUnmounted } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import jsQR from 'jsqr';
import { useI18n } from '@/i18n.js';

const { t } = useI18n();

const videoEl = ref(null);
const descriptionInput = ref(null);
const scanning = ref(false);
const scanned = ref(false);
const scanLabel = ref('');
let videoStream = null;
let scanInterval = null;

const form = useForm({
  otpauth_uri: '',
  description: '',
});

function playScanSound() {
  const ctx = new (window.AudioContext || window.webkitAudioContext)();
  const osc = ctx.createOscillator();
  const gain = ctx.createGain();
  osc.connect(gain);
  gain.connect(ctx.destination);
  osc.type = 'sine';
  osc.frequency.setValueAtTime(1200, ctx.currentTime);
  osc.frequency.setValueAtTime(1600, ctx.currentTime + 0.08);
  gain.gain.setValueAtTime(0.3, ctx.currentTime);
  gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.2);
  osc.start(ctx.currentTime);
  osc.stop(ctx.currentTime + 0.2);
}

function onQrDetected(data) {
  stopScanner();
  playScanSound();

  form.otpauth_uri = data;

  try {
    const url = new URL(data);
    scanLabel.value = decodeURIComponent(url.pathname.replace(/^\//, ''));
  } catch (e) {
    scanLabel.value = data;
  }

  scanned.value = true;
  nextTick(() => descriptionInput.value?.focus());
}

async function startScanner() {
  try {
    videoStream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: 'environment' },
    });
    videoEl.value.srcObject = videoStream;
    await videoEl.value.play();
    scanning.value = true;

    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d', { willReadFrequently: true });

    scanInterval = setInterval(() => {
      const video = videoEl.value;
      if (video && video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const result = jsQR(imageData.data, imageData.width, imageData.height);
        if (result && result.data.startsWith('otpauth://')) {
          onQrDetected(result.data);
        }
      }
    }, 300);
  } catch (err) {
    alert(t('scan.cameraError') + err.message);
  }
}

function stopScanner() {
  if (videoStream) {
    videoStream.getTracks().forEach((t) => t.stop());
    videoStream = null;
  }
  if (scanInterval) {
    clearInterval(scanInterval);
    scanInterval = null;
  }
  scanning.value = false;
}

function submit() {
  form.post('/totp/scan');
}

onUnmounted(() => stopScanner());
</script>
