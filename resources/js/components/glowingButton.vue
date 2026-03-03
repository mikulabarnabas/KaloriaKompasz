<script setup>
defineProps({
    icon: {
        type: String,
        default: ''
    },
    type: {
        type: String,
        default: 'button'
    },
    loading: {
        type: Boolean,
        default: false
    }
})
</script>

<template>
    <button
        :type="type"
        :disabled="loading"
        class="group relative flex items-center justify-center gap-2 overflow-hidden rounded-xl bg-primary px-8 py-4 text-lg font-bold text-background-dark transition-all active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed hover:shadow-[0_0_30px_rgba(13,242,89,0.4)]"
    >
        <svg v-if="loading" class="h-5 w-5 animate-spin text-background-dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>

        <template v-else>
            <slot />
            <span 
                v-if="icon" 
                class="material-symbols-outlined transition-transform duration-300 group-hover:translate-x-1"
            >
                {{ icon }}
            </span>
        </template>
        
        <div class="absolute inset-0 bg-white/10 opacity-0 transition-opacity group-hover:opacity-100"></div>
    </button>
</template>