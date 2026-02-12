<script setup>
import { ref } from 'vue';

defineProps({
    items: {
        type: Array,
        required: true
    }
});

const activeIndex = ref(null);

const toggle = (index) => {
    activeIndex.value = activeIndex.value === index ? null : index;
};
</script>

<template>
    <div class="flex flex-col gap-4 text-left">
        <div v-for="(item, index) in items" :key="index"
            class="rounded-2xl border border-neutral-border bg-neutral-dark/50 overflow-hidden transition-all duration-300"
            :class="{ 'border-primary/40 bg-neutral-dark/80 ring-1 ring-primary/20': activeIndex === index }">
            <button @click="toggle(index)"
                class="flex w-full items-center justify-between p-6 text-left focus:outline-none">
                <span class="text-lg font-bold transition-colors duration-300"
                    :class="activeIndex === index ? 'text-primary' : 'text-white'">
                    {{ item.question }}
                </span>
                <span class="material-symbols-outlined text-primary transition-transform duration-500"
                    :class="{ 'rotate-180': activeIndex === index }">
                    expand_more
                </span>
            </button>

            <transition name="accordion" @enter="el => el.style.height = el.scrollHeight + 'px'"
                @before-leave="el => el.style.height = el.scrollHeight + 'px'" @leave="el => el.style.height = '0'">
                <div v-show="activeIndex === index"
                    class="accordion-content overflow-hidden transition-[height] duration-300 ease-in-out">
                    <div class="px-6 pb-6 text-white/60 leading-relaxed border-t border-neutral-border/30 pt-4">
                        {{ item.answer }}
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<style scoped>
.accordion-enter-active,
.accordion-leave-active {
    transition: height 0.3s ease-in-out;
}

.accordion-enter-from,
.accordion-leave-to {
    height: 0 !important;
}
</style>