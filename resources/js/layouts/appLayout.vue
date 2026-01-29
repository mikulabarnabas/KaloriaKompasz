<script setup>
import Navbar from "@/components/navbar.vue"
import Footer from "@/components/footer.vue"
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <Navbar />

    <main class="flex-1 flex items-center justify-center p-4">
      <slot />
    </main>

    <Footer />
  </div>
</template>