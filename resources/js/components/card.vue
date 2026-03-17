<script setup>
defineProps({
    title: String,
    content: String,
    icon: String
});

defineEmits(['click']);
</script>

<template>
    <div @click="$emit('click')"
        class="group relative flex flex-col gap-6 rounded-3xl border border-neutral-border bg-neutral-dark p-8 hover:border-primary/50 transition cursor-pointer active:scale-[0.98] shadow-sm hover:shadow-xl">
        <div
            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary group-hover:bg-primary group-hover:text-background-dark transition-all duration-300">
            <span class="material-symbols-outlined text-3xl font-bold">{{ icon }}</span>
        </div>

        <div>
            <h3 class="text-2xl font-bold text-main-text transition-colors group-hover:text-primary">
                {{ title }}
            </h3>
            <p class="mt-3 text-secondary-text leading-relaxed">
                {{ content }}
            </p>
        </div>
    </div>
</template>