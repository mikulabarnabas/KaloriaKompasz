<script setup>
defineProps({
  label: { type: String, required: true },
  icon: { type: String, default: 'cloud_upload' },
  type: { type: String, default: 'button' },
  hideLabelOnMobile: { type: Boolean, default: false },
  disabled: Boolean,
  loading: Boolean
});
</script>

<template>
  <button 
    :type="type" 
    :disabled="disabled || loading" 
    class="relative w-full h-14 rounded-xl flex items-center justify-center transition-all duration-100 
           bg-primary text-black font-black uppercase tracking-widest text-[13px]
           hover:brightness-105 active:scale-[0.98] active:translate-y-px
           disabled:opacity-40 disabled:cursor-not-allowed disabled:transform-none
           shadow-sm hover:shadow-md border-b-2 border-black/10 active:border-b-0
           cursor-pointer" 
  >
    <div
      class="absolute inset-0 bg-white/10 opacity-0 hover:opacity-100 transition-opacity duration-100 pointer-events-none">
    </div>

    <div class="relative flex items-center justify-center gap-3">
      <span :class="hideLabelOnMobile ? 'hidden md:flex' : ''">{{ label }}</span>

      <div class="relative w-5 h-5 flex items-center justify-center">
        <Transition mode="out-in" enter-active-class="transition duration-100 ease-out"
          enter-from-class="opacity-0 scale-75 rotate-45" enter-to-class="opacity-100 scale-100 rotate-0"
          leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100 scale-100 rotate-0"
          leave-to-class="opacity-0 scale-75 -rotate-45">
          <span v-if="loading" key="loading"
            class="material-symbols-outlined animate-spin text-[20px] leading-none absolute">
            progress_activity
          </span>
          <span v-else key="icon" class="material-symbols-outlined text-[20px] leading-none absolute">
            {{ icon }}
          </span>
        </Transition>
      </div>
    </div>
  </button>
</template>
