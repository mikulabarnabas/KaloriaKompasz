<script setup>
import { computed } from "vue";
import Button from "primevue/button";
import DatePicker from "primevue/datepicker";

const props = defineProps({
  modelValue: {
    type: Date,
    required: true
  }
});

const emit = defineEmits(["update:modelValue"]);

const localDate = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val)
});

const shiftDate = (days) => {
  const dt = new Date(localDate.value);
  dt.setDate(dt.getDate() + days);
  localDate.value = dt;
};
</script>

<template>
  <div class="flex items-center gap-2">
    <Button icon="pi pi-arrow-left"
            severity="secondary"
            type="button"
            @click="shiftDate(-1)" />

    <DatePicker v-model="localDate" type="date" />

    <Button icon="pi pi-arrow-right"
            severity="secondary"
            type="button"
            @click="shiftDate(1)" />
  </div>
</template>
