<script setup>
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/vue3"
import { trans as t } from 'laravel-vue-i18n';
import { useForm } from "laravel-precognition-vue";
import AppLayout from "@/Layouts/AppLayout.vue"
import Input from "@/Components/input.vue" 


defineOptions({ layout: AppLayout })

const page = usePage()
const showSuccessDialog = ref(false);

const selectedDate = ref(page.props.profile?.date_of_birth ?? '');

const form = useForm("post", "/profile-save", {
  user_id: page.props.profile?.user_id ?? null,
  gender: page.props.profile?.gender ?? "",
  date_of_birth: page.props.profile?.date_of_birth ?? '',
  height_cm: page.props.profile?.height_cm ?? null,
  weight_kg: page.props.profile?.weight_kg ?? null,
  activity_level: page.props.profile?.activity_level ?? 'sedentary',
  weight_goal: page.props.profile?.weight_goal ?? 'maintain',
  target_weight_kg: page.props.profile?.target_weight_kg ?? null,
  goal_period_weeks: page.props.profile?.goal_period_weeks ?? 4,
});

const weightGoalOptions = [
  { label: t('profile.maintain'), value: 'maintain', icon: 'sync' },
  { label: t('profile.lose'), value: 'lose', icon: 'trending_down' },
  { label: t('profile.gain'), value: 'gain', icon: 'trending_up' },
];

const activityOptions = [
  { label: t('profile.sedentary'), value: 'sedentary' },
  { label: t('profile.light'), value: 'light' },
  { label: t('profile.moderate'), value: 'moderate' },
  { label: t('profile.active'), value: 'active' },
  { label: t('profile.very_active'), value: 'very_active' },
];

const genderOptions = [
  { label: "Male", value: "male" },
  { label: "Female", value: "female" },
  { label: "Other", value: "other" },
  { label: "N/A", value: "prefer_not_to_say" },
];

const onSubmit = () => {
  form.date_of_birth = selectedDate.value;
  form.submit().then(() => {
    showSuccessDialog.value = true;
  });
};

function closeSuccessDialog() { showSuccessDialog.value = false; }
</script>

<template>
  <main class="min-h-screen bg-[#050a08] text-[#e0e7e4] p-4 md:p-8 font-sans">
    <div class="max-w-3xl mx-auto space-y-12">
      
      <header class="space-y-2">
        <h1 class="text-5xl md:text-6xl font-black tracking-tighter uppercase leading-none">
          Body <span class="text-[#00ff66]">Profile</span>
        </h1>
        <p class="text-[#88968f] font-bold tracking-[0.2em] uppercase text-[10px] opacity-80">
          {{ $t('profile.subtitle') }}
        </p>
      </header>

      <form @submit.prevent="onSubmit" class="space-y-12" novalidate>
        
        <section class="space-y-8">
          <div class="flex items-center gap-4">
            <div class="h-[2px] w-12 bg-[#00ff66]"></div>
            <h2 class="text-[11px] font-black uppercase tracking-[0.5em] text-[#e0e7e4]">Biometrics</h2>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="relative group bg-[#0a120e] p-6 rounded-[2rem] border border-[#1a241f] hover:border-[#00ff66]/30 transition-all duration-500">
              <label class="text-[10px] font-black text-[#00ff66] uppercase tracking-[0.2em] mb-4 block">Gender Identity</label>
              <div class="relative">
                <select v-model="form.gender" class="w-full bg-transparent border border-[#2a3630] rounded-xl px-4 py-3 text-[#e0e7e4] font-bold uppercase tracking-widest text-xs focus:outline-none focus:border-[#00ff66] appearance-none cursor-pointer transition-colors">
                  <option v-for="opt in genderOptions" :key="opt.value" :value="opt.value" class="bg-[#0a120e]">{{ opt.label }}</option>
                </select>
                <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-[#4a5650] pointer-events-none group-hover:text-[#00ff66] transition-colors">expand_more</span>
              </div>
            </div>

            <div class="bg-[#0a120e] p-6 rounded-[2rem] border border-[#1a241f] hover:border-[#00ff66]/30 transition-all duration-500">
              <label class="text-[10px] font-black text-[#00ff66] uppercase tracking-[0.2em] mb-4 block">Date of Birth</label>
              <input type="date" v-model="selectedDate" class="w-full bg-transparent border border-[#2a3630] rounded-xl px-4 py-3 text-[#e0e7e4] font-black focus:outline-none focus:border-[#00ff66] transition-colors" />
            </div>

            <div class="bg-[#0a120e] p-2 rounded-[2rem] border border-[#1a241f] hover:border-[#00ff66]/30 transition-all duration-500">
              <Input v-model="form.height_cm" type="number" label="Height (cm)" placeholder="180" :error="form.errors.height_cm" />
            </div>

            <div class="bg-[#0a120e] p-2 rounded-[2rem] border border-[#1a241f] hover:border-[#00ff66]/30 transition-all duration-500">
              <Input v-model="form.weight_kg" type="number" label="Current Weight (kg)" placeholder="75.5" :error="form.errors.weight_kg" />
            </div>
          </div>
        </section>

        <section class="space-y-8">
          <div class="flex items-center gap-4">
            <div class="h-[2px] w-12 bg-[#2a3630]"></div>
            <h2 class="text-[11px] font-black uppercase tracking-[0.5em] text-[#e0e7e4]">Objective & Activity</h2>
          </div>

          <div class="bg-[#0a120e]/50 p-8 md:p-10 rounded-[3rem] border border-[#1a241f] space-y-10">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <button 
                v-for="option in weightGoalOptions" :key="option.value"
                type="button"
                @click="form.weight_goal = option.value"
                :class="[
                  'relative flex flex-col items-center gap-4 p-8 rounded-[2rem] border transition-all duration-500 group overflow-hidden',
                  form.weight_goal === option.value 
                    ? 'border-[#00ff66] bg-[#00ff66]/5 shadow-[0_0_30px_rgba(0,255,102,0.1)]' 
                    : 'border-[#1a241f] bg-transparent opacity-40 hover:opacity-100 hover:border-[#2a3630]'
                ]"
              >
                <span class="material-symbols-outlined text-4xl" :class="form.weight_goal === option.value ? 'text-[#00ff66]' : 'text-[#4a5650]'">{{ option.icon }}</span>
                <span class="text-[10px] font-black uppercase tracking-[0.2em]">{{ option.label }}</span>
                <div v-if="form.weight_goal === option.value" class="absolute top-0 right-0 p-2">
                    <div class="w-2 h-2 rounded-full bg-[#00ff66] shadow-[0_0_10px_#00ff66]"></div>
                </div>
              </button>
            </div>

            <div v-if="form.weight_goal !== 'maintain'" class="grid grid-cols-1 md:grid-cols-2 gap-10 pt-8 border-t border-[#1a241f]">
              <div class="bg-[#050a08] p-2 rounded-[2rem] border border-[#1a241f]">
                <Input v-model="form.target_weight_kg" type="number" label="Target Weight (kg)" placeholder="70.0" :error="form.errors.target_weight_kg" />
              </div>
              
              <div class="space-y-6 flex flex-col justify-center">
                <div class="flex justify-between items-end px-2">
                  <label class="text-[10px] font-black text-[#00ff66] uppercase tracking-[0.2em]">Timeline</label>
                  <div class="text-right leading-none">
                    <span class="text-3xl font-black text-[#e0e7e4] tracking-tighter">{{ form.goal_period_weeks }}</span>
                    <span class="text-[10px] font-black uppercase text-[#00ff66] ml-2 tracking-widest">Weeks</span>
                  </div>
                </div>
                <div class="px-2">
                    <input type="range" v-model="form.goal_period_weeks" min="1" max="52" class="industrial-range" />
                </div>
              </div>
            </div>

            <div class="pt-8 border-t border-[#1a241f] space-y-6">
              <label class="text-[10px] font-black text-[#88968f] uppercase tracking-[0.3em] block text-center opacity-60">Daily Activity Intensity</label>
              <div class="flex flex-wrap justify-center gap-3">
                <button 
                  v-for="opt in activityOptions" :key="opt.value"
                  type="button"
                  @click="form.activity_level = opt.value"
                  :class="[
                    'px-6 py-4 rounded-full text-[10px] font-black uppercase tracking-widest border transition-all duration-300',
                    form.activity_level === opt.value 
                    ? 'bg-[#00ff66] text-[#050a08] border-[#00ff66] shadow-[0_10px_20px_rgba(0,255,102,0.2)]' 
                    : 'bg-transparent border-[#1a241f] text-[#4a5650] hover:border-[#2a3630] hover:text-[#e0e7e4]'
                  ]"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>
          </div>
        </section>

        <div class="pt-10">
          <button 
            type="submit" 
            :disabled="form.processing"
            class="w-full group relative py-10 rounded-[2.5rem] bg-[#00ff66] transition-all duration-500 hover:scale-[1.01] active:scale-[0.98] shadow-[0_20px_50px_rgba(0,255,102,0.15)] disabled:opacity-50 overflow-hidden"
          >
            <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity"></div>
            <div class="relative flex items-center justify-center gap-4 text-[#050a08] font-black uppercase tracking-[0.4em] text-sm">
              Update Bio-Data
              <span class="material-symbols-outlined text-2xl group-hover:translate-x-2 transition-transform">arrow_right_alt</span>
            </div>
          </button>
        </div>
      </form>
    </div>

    <transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
        <div v-if="showSuccessDialog" class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-[#050a08]/90 backdrop-blur-md">
            <div class="w-full max-w-sm bg-[#0a120e] border border-[#00ff66]/20 rounded-[3rem] p-10 text-center space-y-8 shadow-2xl">
                <div class="size-24 bg-[#00ff66]/10 rounded-full flex items-center justify-center border border-[#00ff66]/20 mx-auto">
                    <span class="material-symbols-outlined text-5xl text-[#00ff66]">verified</span>
                </div>
                <div class="space-y-2">
                    <h3 class="text-2xl font-black uppercase tracking-tighter text-[#e0e7e4]">System Updated</h3>
                    <p class="text-[#88968f] text-[11px] font-bold uppercase tracking-widest leading-relaxed">Biometric integrity verified and saved.</p>
                </div>
                <button @click="closeSuccessDialog" class="w-full bg-[#1a241f] text-[#e0e7e4] hover:bg-[#2a3630] transition-colors text-[10px] font-black uppercase tracking-[0.3em] py-5 rounded-2xl">
                    Acknowledge
                </button>
            </div>
        </div>
    </transition>
  </main>
</template>

<style>
/* Global resets for specific input types without using @apply */
input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1) hue-rotate(90deg) brightness(1.5);
    cursor: pointer;
}

/* Custom CSS for Range Slider to match Industrial Look */
.industrial-range {
    width: 100%;
    background: transparent;
}

.industrial-range:focus {
    outline: none;
}

.industrial-range::-webkit-slider-runnable-track {
    width: 100%;
    height: 4px;
    cursor: pointer;
    background: #1a241f;
    border-radius: 2px;
}

.industrial-range::-webkit-slider-thumb {
    height: 24px;
    width: 24px;
    border-radius: 50%;
    background: #00ff66;
    cursor: pointer;
    -webkit-appearance: none;
    margin-top: -10px;
    border: 4px solid #050a08;
    box-shadow: 0 0 15px rgba(0, 255, 102, 0.4);
    transition: all 0.2s ease;
}

.industrial-range:active::-webkit-slider-thumb {
    transform: scale(1.2);
    box-shadow: 0 0 25px rgba(0, 255, 102, 0.6);
}

/* Scrollbar styling for the whole page */
::-webkit-scrollbar {
    width: 6px;
}
::-webkit-scrollbar-track {
    background: #050a08;
}
::-webkit-scrollbar-thumb {
    background: #1a241f;
    border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
    background: #00ff66;
}
</style>