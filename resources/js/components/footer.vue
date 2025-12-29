<script setup>
const year = new Date().getFullYear();

const version = "v0.1.0";
</script>

<template>
  <footer class="mx-auto w-full px-4 pb-8 pt-10">
    <div
      class="grid grid-cols-1 gap-8 rounded-2xl border p-6 sm:grid-cols-2 sm:p-8"
    >
      <!-- LEFT -->
      <div class="space-y-3">
        <div class="text-xl font-semibold">KalóriaKompasz</div>

        <p class="text-sm leading-relaxed">
          Egyszerű ételnapló és kalóriaszámláló alkalmazás a tudatosabb
          mindennapokhoz.
        </p>

        <div class="flex flex-wrap items-center gap-x-3 gap-y-2 text-xs">
          <span>© {{ year }}</span>
          <span class="opacity-60">•</span>
          <span>{{ version }}</span>
        </div>
      </div>

      <!-- RIGHT -->
      <div class="space-y-3">
        <div class="text-sm font-semibold">Információ</div>

        <ul class="space-y-2 text-sm">
          <li class="flex items-center gap-2">
            <i class="pi pi-info-circle"></i>
            <span>Verzió: {{ version }}</span>
          </li>

          <li class="flex items-center gap-2">
            <i class="pi pi-shield"></i>
            <span>Adatvédelem</span>
          </li>

          <li class="flex items-center gap-2">
            <i class="pi pi-envelope"></i>
            <span>Kapcsolat:</span>
            <a
              href="mailto:support@kaloriakompasz.hu"
              class="underline underline-offset-4"
            >
              support@kaloriakompasz.hu
            </a>
          </li>

          <li class="flex items-center gap-2">
            <i class="pi pi-check-circle"></i>
            <span>Rendszer: aktív</span>
          </li>
        </ul>
      </div>
    </div>

    <div class="mt-6 text-center text-xs opacity-70">
      Készítette: Mikula Barnabás és Bóta Gergely
    </div>
  </footer>
</template>

<style scoped></style>