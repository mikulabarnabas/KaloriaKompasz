<script setup>
import { computed, ref } from "vue";

import { usePage } from "@inertiajs/vue3"
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

import { useForm } from "laravel-precognition-vue";

const showSuccessDialog = ref(false);

import AppLayout from "@/Layouts/AppLayout.vue"

defineOptions({
  layout: AppLayout,
})

const page = usePage()

const selectedDate = ref(new Date());
const formattedDate = computed(() =>
  selectedDate.value.toISOString().slice(0, 10)
)

const form = useForm("post", "/profile-save", {
  user_id: page.props.profile?.user_id ?? null,
  gender: page.props.profile?.gender ?? "",
  date_of_birth: null,
  height_cm: page.props.profile?.height_cm ?? null,
  weight_kg: page.props.profile?.weight_kg ?? null,
  activity_level: page.props.profile?.activity_level ?? "",
  weight_goal: page.props.profile?.weight_goal ?? 'maintain',
  target_weight_kg: page.props.profile?.target_weight_kg ?? null,
  goal_period_weeks: page.props.profile?.goal_period_weeks ?? 4,
  activity_level: page.props.profile?.activity_level ?? 'sedentary',
});

const weightGoalOptions = [
  { label: t('profile.maintain'), value: 'maintain' },
  { label: t('profile.lose'), value: 'lose' },
  { label: t('profile.gain'), value: 'gain' },
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
  { label: "Prefer not to say", value: "prefer_not_to_say" },
];

const onSubmit = () => {
  console.log("Selected date (local):", selectedDate.value)
  console.log("Formatted date (YYYY-MM-DD):", formattedDate.value)

  form.date_of_birth = formattedDate.value; // <-- set the correct date

  form.submit().then(() => {
    showSuccessDialog.value = true;
  });
};


function closeSuccessDialog() { showSuccessDialog.value = false; }
</script>

<template>
  <div class="w-full max-w-md">
    <div class="rounded-2xl shadow-sm ring-1">
      <div class="p-6 sm:p-8">
        <h1 class="text-2xl font-semibold">{{ $t('profile.title') }}</h1>
        <p class="mt-1 text-sm">
          {{ $t('profile.subtitle') }}
        </p>

        <form class="mt-6 space-y-5" @submit.prevent="onSubmit" novalidate>
          <!-- Gender -->
          <div class="space-y-1">
            <FloatLabel variant="on">
              <Select id="gender" v-model="form.gender" class="w-full" :options="genderOptions" optionLabel="label"
                optionValue="value" @change="form.validate('gender')" />
              <label for="gender">{{ $t('profile.gender_label') }}</label>
            </FloatLabel>

            <small v-if="form.invalid('gender')" class="block text-xs">
              {{ form.errors.gender }}
            </small>
          </div>

          <!-- Date of birth -->
          <div class="space-y-1">
            <FloatLabel variant="on">
              <DatePicker id="date_of_birth" v-model="selectedDate" class="w-full" inputClass="w-full"
                :maxDate="new Date()" dateFormat="yy-mm-dd" :showIcon="true" :manualInput="false"
                @update:modelValue="form.validate('date_of_birth')" />
              <label for="date_of_birth">{{ $t('profile.date_of_birth_label') }}</label>
            </FloatLabel>

            <small v-if="form.invalid('date_of_birth')" class="block text-xs">
              {{ form.errors.date_of_birth }}
            </small>
          </div>

          <!-- Height (cm) -->
          <div class="space-y-1">
            <FloatLabel variant="on">
              <InputNumber id="height_cm" v-model="form.height_cm" class="w-full" inputClass="w-full" :min="0"
                :max="300" :useGrouping="false" @blur="form.validate('height_cm')" />
              <label for="height_cm">{{ $t('profile.height_label') }}</label>
            </FloatLabel>

            <small v-if="form.invalid('height_cm')" class="block text-xs">
              {{ form.errors.height_cm }}
            </small>
          </div>

          <!-- Weight (kg) -->
          <div class="space-y-1">
            <FloatLabel variant="on">
              <InputNumber id="weight_kg" v-model="form.weight_kg" class="w-full" inputClass="w-full" :min="0"
                :max="500" :maxFractionDigits="1" :useGrouping="false" @blur="form.validate('weight_kg')" />
              <label for="weight_kg">{{ $t('profile.weight_label') }}</label>
            </FloatLabel>

            <small v-if="form.invalid('weight_kg')" class="block text-xs">
              {{ form.errors.weight_kg }}
            </small>
          </div>

          <!-- Weight Goal -->
          <div class="space-y-1">
            <FloatLabel variant="on">
              <Select v-model="form.weight_goal" :options="weightGoalOptions" class="w-full" optionLabel="label"
                optionValue="value" />
              <label>{{ $t('profile.weight_goal_label') }}</label>
            </FloatLabel>
          </div>

          <!-- Target Weight -->
          <div v-if="form.weight_goal !== 'maintain'" class="space-y-1">
            <FloatLabel variant="on">
              <InputNumber v-model="form.target_weight_kg" class="w-full" :min="0" :max="500" :useGrouping="false" />
              <label>{{ $t('profile.target_weight_label') }}</label>
            </FloatLabel>
          </div>

          <!-- Goal Period (weeks) -->
          <div v-if="form.weight_goal !== 'maintain'" class="space-y-1">
            <label class="block text-xs font-medium">{{ $t('profile.goal_period_label') }}</label>
            <Slider v-model="form.goal_period_weeks" :min="1" :max="52" />
            <div class="text-xs mt-1">{{ form.goal_period_weeks }} {{ $t('profile.weeks') }}</div>
          </div>

          <!-- Activity Level -->
          <div class="space-y-1">
            <FloatLabel variant="on">
              <Select v-model="form.activity_level" :options="activityOptions" class="w-full" optionLabel="label"
                optionValue="value" />
              <label>{{ $t('profile.activity_level_label') }}</label>
            </FloatLabel>
          </div>


          <Button type="submit" :label="$t('profile.save_button')" class="w-full" :disabled="form.processing" />
        </form>
      </div>
    </div>

    <p class="mt-4 text-center text-xs">
      {{ $t('profile.update_later') }}
    </p>
  </div>

  <Dialog v-model:visible="showSuccessDialog" modal :closable="true" :draggable="false"
    :header="$t('profile.success_dialog_title')" class="w-[92vw] max-w-md" @hide="closeSuccessDialog">
    <p class="">
      {{ $t('profile.success_dialog_message') }}
    </p>

    <template #footer>
      <div class="flex w-full justify-end gap-2">
        <Button :label="$t('profile.close_button')" severity="secondary" @click="closeSuccessDialog" />
      </div>
    </template>
  </Dialog>
</template>
