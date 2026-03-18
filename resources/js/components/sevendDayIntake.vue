<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { getActiveLanguage } from 'laravel-vue-i18n';

const props = defineProps({
    date: String,
    targets: Object
});

const weeklyData = ref([]);
const isLoading = ref(false);

const fetchStats = async () => {
    isLoading.value = true;

    const { data } = await axios.get(`/stats/weekly/${props.date}`);

    const currentLang = getActiveLanguage();
    const locale = currentLang === 'hu' ? 'hu-HU' : 'en-US';

    weeklyData.value = data.map(day => {
        const d = new Date(day.date);
        return {
            ...day,
            label: d.toLocaleDateString(locale, { weekday: 'short' }),
            percent: Math.min((day.calories / props.targets.calories) * 100, 100)
        };
    });

    isLoading.value = false;
};

watch(() => props.date, fetchStats);

onMounted(fetchStats);
</script>

<template>
    <section class="space-y-6">
        <h2 class="text-xs font-black text-main-text uppercase tracking-[0.4em] flex items-center gap-3">
            <span class="w-8 h-px bg-primary"></span>
            {{ $t('statistics.weekly_chart_title') }}
        </h2>

        <div :class="{ 'opacity-50 pointer-events-none': isLoading }"
            class="bg-neutral-dark/20 p-3 sm:p-6 md:p-8 rounded-4xl border border-neutral-border transition-all duration-500 shadow-2xl">

            <div class="flex items-end justify-between h-48 gap-1 md:gap-2">
                <div v-for="day in weeklyData" :key="day.date"
                    class="flex-1 flex flex-col items-center gap-3 group relative">

                    <div class="absolute -top-10 opacity-100 bg-primary text-background-dark font-black px-1 py-0.5 rounded mb-2 z-20 pointer-events-none whitespace-nowrap
                               text-[15px] md:text-[13px]">
                        {{ day.calories }}<span class="hidden md:inline ml-0.5">kcal</span>
                    </div>

                    <div
                        class="w-full max-w-1.5 sm:max-w-2.5 md:max-w-3 bg-neutral-dark rounded-full relative overflow-hidden h-32">
                        <div class="absolute bottom-0 w-full bg-primary transition-all duration-700 rounded-full"
                            :style="{ height: `${day.percent}%` }"></div>
                    </div>

                    <span
                        class="text-[7px] sm:text-[9px] font-black uppercase tracking-tighter opacity-60 group-hover:opacity-100 group-hover:text-primary transition-all">
                        {{ day.label }}
                    </span>
                </div>
            </div>
        </div>
    </section>
</template>