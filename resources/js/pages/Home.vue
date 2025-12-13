<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import InputText from 'primevue/inputtext';
import Panel from 'primevue/panel';

const name = ref('');
const greet = () => {
  alert(`Hello, ${name.value || 'Guest'}!`);
};
</script>

<template>
  <Head title="Home" />
  <div class="p-6 space-y-6">
    <Panel header="Welcome">
      <p>Home page powered by Vue + PrimeVue in Laravel.</p>
    </Panel>

    <Card>
      <template #title>Try PrimeVue</template>
      <template #content>
        <div class="flex items-center gap-3 mb-4">
          <i class="pi pi-user text-xl" />
          <InputText v-model="name" placeholder="Your name" />
        </div>
        <Button label="Greet" icon="pi pi-check" @click="greet" />
      </template>
    </Card>
  </div>
</template>

<style scoped>
</style>