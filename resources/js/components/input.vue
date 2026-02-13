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
        <label v-if="label" class="block text-xs text-white/60 mb-2">
            {{ label }}
        </label>
        
        <input 
            v-model="model"
            :type="type"
            :placeholder="placeholder"
            :autocomplete="autocomplete"
            class="w-full rounded-xl bg-white/5 border px-4 py-3 text-white placeholder-white/30 transition-all focus:outline-none focus:ring-1"
            :class="[
                error 
                ? 'border-red-400 focus:border-red-400 focus:ring-red-400/40' 
                : 'border-white/10 focus:border-primary focus:ring-primary/40'
            ]"
        >
        
        <transition name="fade">
            <p v-if="error" class="text-xs text-red-400 mt-1">
                {{ error }}
            </p>
        </transition>
    </div>
</template>