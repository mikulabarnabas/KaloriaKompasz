<script setup>
import { computed } from 'vue';

const props = defineProps({
    // This matches the v-model from the parent
    modelValue: {
        type: Array, // Changed from [Object] to Array based on your .find usage
        required: true
    },
    // You likely need these as props too, or define them locally
    workoutDiary: {
        type: Array,
        default: () => []
    },
    targets: {
        type: Object,
        default: () => ({ calories: 2000 })
    }
});

// Helper function to ensure we are working with numbers
const num = (val) => Number(val) || 0;

const weeklyData = computed(() => {
    const days = [];
    const now = new Date();

    for (let i = 6; i >= 0; i--) {
        const d = new Date();
        d.setDate(now.getDate() - i);
        const dateStr = d.toISOString().slice(0, 10);

        // Use props.modelValue instead of foodDiary
        const foodEntry = props.modelValue.find(e => e.date?.slice(0, 10) === dateStr);
        const dayCals = (foodEntry?.foods || []).reduce((acc, f) => acc + num(f.pivot?.calorie), 0);

        // Use props.workoutDiary
        const workoutEntry = props.workoutDiary.find(e => e.date?.slice(0, 10) === dateStr);
        const dayBurned = (workoutEntry?.exercises || []).reduce((acc, e) => {
            return acc + (num(e.pivot?.amount) * num(e.calories_per_unit));
        }, 0);

        days.push({
            label: d.toLocaleDateString('hu-HU', { weekday: 'short' }),
            date: dateStr,
            netCalories: dayCals,
            burned: dayBurned,
            // Access targets from props
            percent: Math.min((dayCals / props.targets.calories) * 100, 100) || 0
        });
    }
    return days;
});
</script>

<template>
    <section class="space-y-6">
        <h2 class="text-xs font-black text-main-text uppercase tracking-[0.4em] flex items-center gap-3">
            <span class="w-8 h-px bg-primary"></span>
            7 day Calorie Intake
        </h2>

        <div
            class="bg-neutral-dark/20 p-8 rounded-[2.5rem] border border-neutral-border hover:border-primary/30 transition-all duration-500 shadow-2xl">
            <div class="flex items-end justify-between h-48 gap-2">
                <div v-for="day in weeklyData" :key="day.date"
                    class="flex-1 flex flex-col items-center gap-3 group relative">
                    <div
                        class="absolute -top-12 opacity-0 group-hover:opacity-100 transition-opacity bg-primary text-background-dark text-[10px] font-black px-2 py-1 rounded mb-2 z-20 pointer-events-none whitespace-nowrap">
                        {{ day.netCalories }} kcal
                    </div>

                    <div class="w-full max-w-3 bg-neutral-dark rounded-full relative overflow-hidden h-32">
                        <div class="absolute bottom-0 w-full bg-primary group-hover:bg-primary group-hover:shadow-[0_0_15px_rgba(0,255,102,0.5)] transition-all duration-700 rounded-full"
                            :style="{ height: `${day.percent}%` }"></div>
                    </div>

                    <span
                        class="text-[9px] font-black uppercase tracking-tighter opacity-40 group-hover:opacity-100 group-hover:text-primary transition-all">
                        {{ day.label }}
                    </span>
                </div>
            </div>
        </div>
    </section>
</template>