<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: [Date, String],
        required: true
    }
});

const emit = defineEmits(['update:modelValue']);

const currentDate = computed(() => new Date(props.modelValue));

const formattedDate = computed(() => {
    return currentDate.value.toISOString().slice(0, 10);
});

const changeDate = (days) => {
    const newDate = new Date(currentDate.value);
    newDate.setDate(newDate.getDate() + days);
    emit('update:modelValue', newDate);
};

const handleInput = (e) => {
    emit('update:modelValue', new Date(e.target.value));
};
</script>

<template>
    <div
        class="flex items-center justify-between w-full sm:w-fit bg-neutral-dark/40 backdrop-blur-md border border-neutral-border p-1.5 rounded-2xl shadow-inner">

        <button @click="changeDate(-1)"
            class="shrink-0 flex items-center justify-center h-10 hover:bg-primary/10 rounded-xl transition-all active:scale-90 group">
            <span class="material-symbols-outlined text-secondary-text group-hover:text-primary transition-colors">
                chevron_left
            </span>
        </button>

        <div class="flex-1 flex flex-col items-center relative group px-2 py-1">
            <div class="flex flex-col items-center justify-center cursor-pointer">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[14px]">calendar_today</span>
                    <h2 class="font-black text-sm tracking-[0.15em] text-main-text uppercase whitespace-nowrap">
                        {{ formattedDate }}
                    </h2>
                </div>
            </div>

            <input type="date" :value="formattedDate" @input="handleInput"
                class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10" />
        </div>

        <button @click="changeDate(1)"
            class="shrink-0 flex items-center justify-center h-10 hover:bg-primary/10 rounded-xl transition-all active:scale-90 group">
            <span class="material-symbols-outlined text-secondary-text group-hover:text-primary transition-colors">
                chevron_right
            </span>
        </button>
    </div>
</template>

<style scoped>
input[type="date"]::-webkit-calendar-picker-indicator {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
    cursor: pointer;
}
</style>