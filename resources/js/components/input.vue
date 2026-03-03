<script setup>
const model = defineModel()

defineProps({
    label: String,
    type: {
        type: String,
        default: 'text'
    },
    placeholder: String,
    error: String,
    autocomplete: String
})
</script>

<template>
    <div class="w-full">
        <label v-if="label" class="block text-[10px] font-black text-primary uppercase tracking-[0.2em] mb-2 ml-1">
            {{ label }}
        </label>

        <input v-model="model" :type="type" :placeholder="placeholder" :autocomplete="autocomplete"
            class="w-full rounded-xl bg-neutral-dark/40 border px-4 py-3 text-main-text placeholder-secondary-text/30 transition-all focus:outline-none focus:ring-2"
            :class="[
                error
                    ? 'border-red-500/50 focus:border-red-500 focus:ring-red-500/20'
                    : 'border-neutral-border focus:border-primary focus:ring-primary/20 hover:border-neutral-border/80'
            ]">

        <transition enter-active-class="transition duration-200 ease-out"
            enter-from-class="transform -translate-y-1 opacity-0" enter-to-class="transform translate-y-0 opacity-100"
            leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100"
            leave-to-class="opacity-0">
            <p v-if="error" class="text-[10px] font-bold text-red-400 mt-1.5 ml-1 uppercase tracking-wider">
                {{ error }}
            </p>
        </transition>
    </div>
</template>

<style scoped>
/* Ensuring inputs don't have that weird browser default background in dark mode */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus {
    -webkit-text-fill-color: #ffffff;
    -webkit-box-shadow: 0 0 0px 1000px #0a0f0d inset;
    transition: background-color 5000s ease-in-out 0s;
}
</style>