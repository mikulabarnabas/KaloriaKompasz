<script setup>
import { ref, onMounted, watch } from 'vue';
import { registerPlugin, Capacitor } from '@capacitor/core';
import axios from 'axios';

const props = defineProps({
    date: {
        type: String,
        required: true
    }
});

const emit = defineEmits(['synced']);

const HealthConnect = registerPlugin('HealthConnectBridge');
const SamsungHealth = registerPlugin('SamsungHealthCustom');

const isAndroid = ref(Capacitor.getPlatform() === 'android');
var hcStatus = false;
var shStatus = false;
const hasPermissions = ref(false);
const steps = ref(0);

const checkAndSync = async () => {
    if (!isAndroid.value) return;
    try {
        hcStatus = await HealthConnect.checkHealthPermissions();

        if (!hcStatus.granted) {
            shStatus = await SamsungHealth.checkSamsungPermissions();
        }

        hasPermissions.value = hcStatus.granted || shStatus.granted;

        hasPermissions.value = true;
        await syncData();
        return;
    } catch (e) {
        console.error("Hiba az ellenőrzésnél", e);
    }
};

const syncData = async () => {
    let resultSteps = 0;
    try {
        const hcResult = await HealthConnect.getSteps({ date: props.date });
        resultSteps = hcResult.steps || 0;

        if (resultSteps === 0 && shStatus) {
            const shResult = await SamsungHealth.getSteps({ date: props.date });
            resultSteps = shResult.steps || 0;
        }

        steps.value = resultSteps;

        if (steps.value > 0) {
            await axios.post('/wdiary/sync-steps', { steps: steps.value, date: props.date });
            emit('synced');
        }
    } catch (e) {
        console.warn("Szinkron hiba", e);
    }
};

onMounted(checkAndSync);

const requestGoogle = async () => {
    await HealthConnect.requestHealthPermissions();
    await checkAndSync();
};

const requestSamsung = async () => {
    await SamsungHealth.requestSamsungPermissions();
    await checkAndSync();
};

watch(() => props.date, () => {
    if (hasPermissions.value) {
        syncData();
    }
});
</script>

<template>
    <div v-if="isAndroid && !hasPermissions" class="max-w-4xl mx-auto mb-8">
        <div class="bg-neutral-dark/50 border border-neutral-border p-6 rounded-3xl shadow-sm">

            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary text-3xl">directions_walk</span>
                    <div>
                        <h3 class="text-main-text font-black uppercase tracking-widest text-xs">
                            {{ 'Lépésszám szinkronizálása' }}
                        </h3>
                        <p class="text-secondary-text text-[10px] font-medium uppercase tracking-tighter">
                            {{ 'Válassz forrást az adatokhoz' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <button @click="requestGoogle"
                    class="flex items-center justify-center gap-2 py-3 px-4 border border-main-text text-main-text rounded-2xl hover:bg-main-text/5 transition-all active:scale-95 text-xs font-bold uppercase tracking-widest">
                    <span class="material-symbols-outlined text-sm">favorite</span>
                    Google Health
                </button>

                <button @click="requestSamsung"
                    class="flex items-center justify-center gap-2 py-3 px-4 border border-main-text text-main-text rounded-2xl hover:bg-main-text/5 transition-all active:scale-95 text-xs font-bold uppercase tracking-widest">
                    <span class="material-symbols-outlined text-sm">favorite</span>
                    Samsung Health
                </button>
            </div>

        </div>
    </div>
</template>