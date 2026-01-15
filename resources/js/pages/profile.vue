<script setup>
import { ref } from "vue";

import FloatLabel from "primevue/floatlabel";
import InputText from "primevue/inputtext";
import InputNumber from "primevue/inputnumber";
import Select from "primevue/select";
import DatePicker from "primevue/datepicker";
import Button from "primevue/button";
import Dialog from "primevue/dialog";

import { useForm } from "laravel-precognition-vue";

const showSuccessDialog = ref(false);

/**
 * Adjust endpoint/method to your routes:
 * - Create: POST /user-profile
 * - Update: PUT/PATCH /user-profile
 */
const form = useForm("post", "/profile-save", {
  user_id: null, // optional if you set via auth() on backend
  gender: "",
  date_of_birth: null,
  height_cm: null,
  weight_kg: null,
  activity_level: "",
  diet: "",
});

const genderOptions = [
  { label: "Male", value: "male" },
  { label: "Female", value: "female" },
  { label: "Other", value: "other" },
  { label: "Prefer not to say", value: "prefer_not_to_say" },
];

const activityOptions = [
  { label: "Sedentary", value: "sedentary" },
  { label: "Lightly active", value: "light" },
  { label: "Moderately active", value: "moderate" },
  { label: "Very active", value: "very" },
  { label: "Extra active", value: "extra" },
];

const dietOptions = [
  { label: "Balanced", value: "balanced" },
  { label: "Vegetarian", value: "vegetarian" },
  { label: "Vegan", value: "vegan" },
  { label: "Keto", value: "keto" },
  { label: "Paleo", value: "paleo" },
  { label: "Low-carb", value: "low_carb" },
  { label: "High-protein", value: "high_protein" },
];

const onSubmit = () =>
  form
    .submit()
    .then(() => {
      showSuccessDialog.value = true;
    })
    .catch(() => {
      // precognition errors already in form.errors
    });

function closeSuccessDialog() {
  showSuccessDialog.value = false;
  // keep values or reset—your choice:
  // form.reset();
}
</script>

<template>
  <div
    class="min-h-screen w-full flex items-center justify-center p-4"
  >
    <div class="w-full max-w-md">
      <div class="rounded-2xl shadow-sm ring-1">
        <div class="p-6 sm:p-8">
          <h1 class="text-2xl font-semibold">Profile</h1>
          <p class="mt-1 text-sm">
            Add your profile details to personalize your experience.
          </p>

          <form class="mt-6 space-y-5" @submit.prevent="onSubmit" novalidate>
            <!-- Gender -->
            <div class="space-y-1">
              <FloatLabel variant="on">
                <Select
                  id="gender"
                  v-model="form.gender"
                  class="w-full"
                  :options="genderOptions"
                  optionLabel="label"
                  optionValue="value"
                  @change="form.validate('gender')"
                />
                <label for="gender">Gender</label>
              </FloatLabel>

              <small
                v-if="form.invalid('gender')"
                class="block text-xs"
              >
                {{ form.errors.gender }}
              </small>
            </div>

            <!-- Date of birth -->
            <div class="space-y-1">
              <FloatLabel variant="on">
                <DatePicker
                  id="date_of_birth"
                  v-model="form.date_of_birth"
                  class="w-full"
                  inputClass="w-full"
                  dateFormat="yy-mm-dd"
                  :showIcon="true"
                  :manualInput="false"
                  @update:modelValue="form.validate('date_of_birth')"
                />
                <label for="date_of_birth">Date of birth</label>
              </FloatLabel>

              <small
                v-if="form.invalid('date_of_birth')"
                class="block text-xs"
              >
                {{ form.errors.date_of_birth }}
              </small>
            </div>

            <!-- Height (cm) -->
            <div class="space-y-1">
              <FloatLabel variant="on">
                <InputNumber
                  id="height_cm"
                  v-model="form.height_cm"
                  class="w-full"
                  inputClass="w-full"
                  :min="0"
                  :max="300"
                  :useGrouping="false"
                  @blur="form.validate('height_cm')"
                />
                <label for="height_cm">Height (cm)</label>
              </FloatLabel>

              <small
                v-if="form.invalid('height_cm')"
                class="block text-xs"
              >
                {{ form.errors.height_cm }}
              </small>
            </div>

            <!-- Weight (kg) -->
            <div class="space-y-1">
              <FloatLabel variant="on">
                <InputNumber
                  id="weight_kg"
                  v-model="form.weight_kg"
                  class="w-full"
                  inputClass="w-full"
                  :min="0"
                  :max="500"
                  :maxFractionDigits="1"
                  :useGrouping="false"
                  @blur="form.validate('weight_kg')"
                />
                <label for="weight_kg">Weight (kg)</label>
              </FloatLabel>

              <small
                v-if="form.invalid('weight_kg')"
                class="block text-xs"
              >
                {{ form.errors.weight_kg }}
              </small>
            </div>

            <!-- Activity level -->
            <div class="space-y-1">
              <FloatLabel variant="on">
                <Select
                  id="activity_level"
                  v-model="form.activity_level"
                  class="w-full"
                  :options="activityOptions"
                  optionLabel="label"
                  optionValue="value"
                  @change="form.validate('activity_level')"
                />
                <label for="activity_level">Activity level</label>
              </FloatLabel>

              <small
                v-if="form.invalid('activity_level')"
                class="block text-xs"
              >
                {{ form.errors.activity_level }}
              </small>
            </div>

            <!-- Diet -->
            <div class="space-y-1">
              <FloatLabel variant="on">
                <Select
                  id="diet"
                  v-model="form.diet"
                  class="w-full"
                  :options="dietOptions"
                  optionLabel="label"
                  optionValue="value"
                  @change="form.validate('diet')"
                />
                <label for="diet">Diet</label>
              </FloatLabel>

              <small
                v-if="form.invalid('diet')"
                class="block text-xs"
              >
                {{ form.errors.diet }}
              </small>
            </div>

            <Button
              type="submit"
              label="Save profile"
              class="w-full"
              :disabled="form.processing"
            />
          </form>
        </div>
      </div>

      <p class="mt-4 text-center text-xs">
        You can update these details later.
      </p>
    </div>

    <Dialog
      v-model:visible="showSuccessDialog"
      modal
      :closable="true"
      :draggable="false"
      header="Profile saved"
      class="w-[92vw] max-w-md"
      @hide="closeSuccessDialog"
    >
      <p class="text-slate-700">
        Your profile details have been saved successfully.
      </p>

      <template #footer>
        <div class="flex w-full justify-end gap-2">
          <Button
            label="Close"
            severity="secondary"
            @click="closeSuccessDialog"
          />
        </div>
      </template>
    </Dialog>
  </div>
</template>