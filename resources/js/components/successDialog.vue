<script setup>
defineProps({
    show: {
        type: Boolean,
        default: false
    },
    title: String,
    message: String,
    buttonText: String
});

const emit = defineEmits(['close']);
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="show" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div 
                class="bg-neutral-dark p-8 rounded-[2rem] max-w-sm w-full text-center border border-white/10 shadow-2xl transform transition-all animate-fly-in"
            >
                <div class="w-16 h-16 bg-primary/20 text-primary rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h3 class="text-main-text text-xl font-bold mb-2">{{ title }}</h3>
                
                <p class="text-main-text text-sm mb-8 leading-relaxed">
                    {{ message }}
                </p>

                <button 
                    class="bg-primary text-black w-full py-4 rounded-2xl font-black uppercase tracking-widest text-sm hover:brightness-110 active:scale-[0.98] transition-all"
                    @click="emit('close')"
                >
                    {{ buttonText }}
                </button>
            </div>
        </div>
    </Transition>
</template>