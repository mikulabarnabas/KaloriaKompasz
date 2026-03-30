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
           bg-primary text-black font-black uppercase text-[13px]
           hover:brightness-105 active:scale-[0.98] active:translate-y-px
           disabled:opacity-40" 
  >
    <div class="relative flex items-center justify-center h-fit gap-1">
      
      <span 
        :class="[
          hideLabelOnMobile ? 'hidden md:inline-block' : 'inline-block',
          'leading-none'
        ]"
      >
        {{ label }}
      </span>

      <div class="flex items-center justify-center w-5 h-5 shrink-0">
          <span v-if="loading" key="loading"
            class="material-symbols-outlined animate-spin text-[20px] leading-none">
            progress_activity
          </span>
          <span v-else key="icon" class="material-symbols-outlined text-[20px] leading-none">
            {{ icon }}
          </span>
      </div>
    </div>
  </button>
</template>