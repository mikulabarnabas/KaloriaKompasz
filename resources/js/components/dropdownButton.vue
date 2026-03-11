<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    label: { type: String },
    icon: { type: String, default: "expand_more" },
    items: { type: Array, default: () => [] },
    iconLabel: { type: Boolean, default: false }
});


const isOpen = ref(false)
const dropdownRef = ref(null)

const closeDropdown = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        isOpen.value = false
    }
}

onMounted(() => document.addEventListener('click', closeDropdown))
onUnmounted(() => document.removeEventListener('click', closeDropdown))
</script>

<template>
    <div ref="dropdownRef" class="font-display relative inline-block text-left" :class="$attrs.class">

        <div class="flex items-center gap-4 cursor-pointer select-none" @click="isOpen = !isOpen">
            <div class="flex items-center gap-2 text-main-text font-semibold hover:text-primary transition-colors">
                <span v-if="iconLabel" class="material-symbols-outlined text-2xl leading-none!">{{ label }}</span>
                <span v-else>{{ label }}</span>
                <span class="material-symbols-outlined transition-transform duration-200"
                    :class="{ 'rotate-180': isOpen }">
                    {{ icon }}
                </span>
            </div>
        </div>

        <transition enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95">

            <div v-if="isOpen" :class="[
                'absolute -right-3 mt-3 w-56 bg-neutral-dark border border-neutral-border rounded-(--radius-lg) shadow-xl z-50 p-2'
            ]">
                <div
                    class="absolute -top-2 right-4 w-4 h-4 bg-neutral-dark border-t border-l border-neutral-border rotate-45 rounded-tl-sm">
                </div>

                <div class="relative z-10 flex flex-col gap-1">
                    <button v-for="item in items" @click="item.function" :class="[
                        'flex items-center gap-3 w-full px-3 py-2.5 text-main-text rounded-default hover:bg-background-dark transition-colors text-sm font-bold text-left',
                        item.class
                    ]">
                        <span class="material-symbols-outlined text-2xl leading-none!">{{ item.icon }}</span>
                        {{ item.label }}
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>