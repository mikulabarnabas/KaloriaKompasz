<script setup>
import { ref, computed } from "vue";
import { usePage, router } from "@inertiajs/vue3"
import { trans as t } from 'laravel-vue-i18n';
import { useForm } from "laravel-precognition-vue";
import AppLayout from "@/Layouts/appLayout.vue"
import Input from "@/Components/input.vue"
import Button from "@/Components/button.vue"

defineOptions({ layout: AppLayout })

const page = usePage()
const showSuccessDialog = ref(false);

const rawDate = page.props.profile?.date_of_birth ?? '';
const selectedDate = ref(rawDate ? rawDate.substring(0, 10) : '');

const form = useForm("post", "/profile/save", {
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

const weightGoalOptions = computed(() => [
  { label: t('profile.maintain'), value: 'maintain', icon: 'sync' },
  { label: t('profile.lose'), value: 'lose', icon: 'trending_down' },
  { label: t('profile.gain'), value: 'gain', icon: 'trending_up' },
]);

const activityOptions = computed(() => [
  { label: t('profile.sedentary'), value: 'sedentary' },
  { label: t('profile.light'), value: 'light' },
  { label: t('profile.moderate'), value: 'moderate' },
  { label: t('profile.active'), value: 'active' },
  { label: t('profile.very_active'), value: 'very_active' },
]);

const genderOptions = computed(() => [
  { label: t('profile.male'), value: "male" },
  { label: t('profile.female'), value: "female" },
  { label: t('profile.other'), value: "other" },
  { label: t('profile.na'), value: "prefer_not_to_say" },
]);

const onSubmit = () => {
  form.date_of_birth = selectedDate.value;
  form.submit().then(() => {
    showSuccessDialog.value = true;
  });
};

function closeSuccessDialog()
{
    showSuccessDialog.value = false;
    router.get('/stats');
}
</script>

<template>
  <main class="min-h-screen bg-background-dark text-main-text p-4 md:p-8 font-sans">
    <div class="max-w-4xl mx-auto space-y-12">

      <header class="space-y-2 animate-fly-in">
        <h1 class="text-4xl md:text-5xl font-black tracking-tighter uppercase leading-none">
          {{ $t('profile.title_part1') ?? 'Body' }} <span class="text-primary">{{ $t('profile.title_part2') ?? 'Profile'
            }}</span>
        </h1>
        <p class="text-secondary-text font-bold tracking-[0.2em] uppercase text-[10px] opacity-60">
          {{ $t('profile.subtitle') }}
        </p>
      </header>

      <form @submit.prevent="onSubmit" class="space-y-12" novalidate>

        <section class="space-y-8 animate-fly-in" style="animation-delay: 100ms">
          <div class="flex items-center gap-4">
            <div class="h-0.5 w-12 bg-primary"></div>
            <h2 class="text-[11px] font-black uppercase tracking-[0.5em] text-main-text">
              {{ $t('profile.biometrics_title') }}
            </h2>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="w-full">
              <label v-if="$t('profile.gender_label')"
                class="block text-[10px] font-black text-primary uppercase tracking-[0.2em] mb-2 ml-1">
                {{ $t('profile.gender_label') }}
              </label>

              <div class="relative group">
                <select v-model="form.gender"
                  class="w-full appearance-none rounded-xl bg-neutral-dark/40 border px-4 py-3 text-main-text font-bold uppercase tracking-widest text-xs transition-all focus:outline-none focus:ring-2 cursor-pointer"
                  :class="[
                    form.errors.gender
                      ? 'border-red-500/50 focus:border-red-500 focus:ring-red-500/20'
                      : 'border-neutral-border focus:border-primary focus:ring-primary/20 hover:border-neutral-border/80'
                  ]">
                  <option v-for="opt in genderOptions" :key="opt.value" :value="opt.value"
                    class="bg-neutral-dark text-main-text">
                    {{ opt.label }}
                  </option>
                </select>

                <span
                  class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-secondary-text pointer-events-none group-hover:text-primary transition-colors">
                  expand_more
                </span>
              </div>

              <transition enter-active-class="transition duration-200 ease-out"
                enter-from-class="transform -translate-y-1 opacity-0"
                enter-to-class="transform translate-y-0 opacity-100"
                leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100"
                leave-to-class="opacity-0">
                <p v-if="form.errors.gender"
                  class="text-[10px] font-bold text-red-400 mt-1.5 ml-1 uppercase tracking-wider">
                  {{ form.errors.gender }}
                </p>
              </transition>
            </div>

            <Input v-model="selectedDate" type="date" :label="$t('profile.date_of_birth_label')"
              :error="form.errors.date_of_birth" class="date-input-custom" />
            <Input v-model="form.height_cm" type="number" :label="$t('profile.height_label')" placeholder="180"
              :error="form.errors.height_cm" />
            <Input v-model="form.weight_kg" type="number" :label="$t('profile.weight_label')" placeholder="75.5"
              :error="form.errors.weight_kg" />
          </div>
        </section>

        <section class="space-y-8 animate-fly-in" style="animation-delay: 200ms">
          <div class="flex items-center gap-4">
            <div class="h-0.5 w-12 bg-neutral-border"></div>
            <h2 class="text-[11px] font-black uppercase tracking-[0.5em] text-main-text">
              {{ $t('profile.objective_title') }}
            </h2>
          </div>

          <div class="bg-neutral-dark/20 p-8 md:p-10 rounded-[3rem] border border-neutral-border shadow-2xl space-y-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <button v-for="option in weightGoalOptions" :key="option.value" type="button"
                @click="form.weight_goal = option.value" :class="[
                  'relative flex flex-col items-center gap-4 p-8 rounded-4xl border transition-all duration-500 group overflow-hidden',
                  form.weight_goal === option.value
                    ? 'border-primary bg-primary/5 shadow-[0_0_30px_rgba(13,242,89,0.1)]'
                    : 'border-neutral-border bg-transparent opacity-40 hover:opacity-100 hover:border-primary/30'
                ]">
                <span class="material-symbols-outlined text-4xl"
                  :class="form.weight_goal === option.value ? 'text-primary' : 'text-secondary-text'">{{ option.icon
                  }}</span>
                <span class="text-[10px] font-black uppercase tracking-[0.2em]">{{ option.label }}</span>
              </button>
            </div>

            <div v-if="form.weight_goal !== 'maintain'"
              class="grid grid-cols-1 md:grid-cols-2 gap-10 pt-8 border-t border-neutral-border/50">
              <Input v-model="form.target_weight_kg" type="number" :label="$t('profile.target_weight_label')"
                placeholder="70.0" :error="form.errors.target_weight_kg" />

              <div class="space-y-6 flex flex-col justify-center">
                <div class="flex justify-between items-end px-2">
                  <label class="text-[10px] font-black text-primary uppercase tracking-[0.2em]">
                    {{ $t('profile.timeline_label') }}
                  </label>
                  <div class="text-right leading-none">
                    <span class="text-3xl font-black text-main-text tracking-tighter">{{ form.goal_period_weeks
                      }}</span>
                    <span class="text-[10px] font-black uppercase text-primary ml-2 tracking-widest">
                      {{ $t('profile.weeks') }}
                    </span>
                  </div>
                </div>
                <div class="px-2">
                  <input type="range" v-model="form.goal_period_weeks" min="1" max="52" class="industrial-range" />
                </div>
              </div>
            </div>

            <div class="pt-8 border-t border-neutral-border/50 space-y-6">
              <label
                class="text-[10px] font-black text-secondary-text uppercase tracking-[0.3em] block text-center opacity-60">
                {{ $t('profile.activity_intensity') }}
              </label>
              <div class="flex flex-wrap justify-center gap-3">
                <button v-for="opt in activityOptions" :key="opt.value" type="button"
                  @click="form.activity_level = opt.value" :class="[
                    'px-6 py-4 rounded-full text-[10px] font-black uppercase tracking-widest border transition-all duration-300',
                    form.activity_level === opt.value
                      ? 'bg-primary text-black border-primary shadow-[0_10px_20px_rgba(13,242,89,0.2)]'
                      : 'bg-transparent border-neutral-border text-secondary-text hover:border-primary/50 hover:text-main-text'
                  ]">
                  {{ opt.label }}
                </button>
              </div>
            </div>
          </div>
        </section>

        <div class="pt-10 animate-fly-in" style="animation-delay: 300ms">
          <Button type="submit" :label="$t('profile.save_button')" icon="save" :loading="form.processing"
            class="h-20! rounded-[2.5rem]! text-base!" />
        </div>
      </form>
    </div>

    <transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
      <div v-if="showSuccessDialog"
        class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-background-dark/90 backdrop-blur-md">
        <div
          class="w-full max-w-sm bg-neutral-dark border border-primary/20 rounded-[3rem] p-10 text-center space-y-8 shadow-2xl">
          <div
            class="size-24 bg-primary/10 rounded-full flex items-center justify-center border border-primary/20 mx-auto">
            <span class="material-symbols-outlined text-5xl text-primary">verified</span>
          </div>
          <div class="space-y-2">
            <h3 class="text-2xl font-black uppercase tracking-tighter text-main-text">
              {{ $t('profile.success_dialog_title') }}
            </h3>
            <p class="text-secondary-text text-[11px] font-bold uppercase tracking-widest leading-relaxed">
              {{ $t('profile.success_dialog_message') }}
            </p>
          </div>

          <Button :label="$t('profile.acknowledge')" icon="check_circle" @click="closeSuccessDialog" class="h-14" />
        </div>
      </div>
    </transition>
  </main>
</template>
