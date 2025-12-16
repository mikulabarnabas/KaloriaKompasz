<script setup>
import { ref } from 'vue'

// PrimeVue komponensek
import Menubar from 'primevue/menubar'
import Badge from 'primevue/badge'
import InputText from 'primevue/inputtext'
import Avatar from 'primevue/avatar'
import Ripple from 'primevue/ripple'

// Menüpontok definiálása
const items = ref([
    { label: 'Home', icon: 'pi pi-home' },
    {
        label: 'Projects',
        icon: 'pi pi-search',
        badge: 3,
        items: [
            { label: 'Core', icon: 'pi pi-bolt', shortcut: '⌘+S' },
            { label: 'Blocks', icon: 'pi pi-server', shortcut: '⌘+B' },
            { separator: true },
            { label: 'UI Kit', icon: 'pi pi-pencil', shortcut: '⌘+U' }
        ]
    },
    { label: 'About', icon: 'pi pi-info-circle' }
])
</script>

<template>
  <Menubar :model="items" class="shadow-md bg-white">
    
    <!-- LOGO bal oldalon -->
    <template #start>
      <span class="text-2xl font-bold text-primary ml-2">FitTracker</span>
    </template>

    <!-- Menü item slot -->
    <template #item="{ item, props, hasSubmenu, root }">
      <a
        v-ripple
        class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100"
        v-bind="props.action"
      >
        <i v-if="item.icon" :class="item.icon"></i>
        <span>{{ item.label }}</span>
        <Badge v-if="item.badge" :value="item.badge" class="ml-auto" />
        <span v-if="item.shortcut" class="ml-auto border border-gray-300 rounded bg-gray-100 text-xs px-1">{{ item.shortcut }}</span>
        <i v-if="hasSubmenu" :class="['pi', root ? 'pi-angle-down' : 'pi-angle-right', 'ml-auto']"></i>
      </a>
    </template>

    <!-- JOBB OLDAL: kereső + avatar -->
    <template #end>
      <div class="flex items-center gap-2">
        <InputText placeholder="Search" class="w-32 sm:w-auto" />
        <Avatar image="https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png" shape="circle" />
      </div>
    </template>

  </Menubar>
</template>

<script>
export default {
  directives: {
    ripple: Ripple
  }
}
</script>

<style scoped></style>