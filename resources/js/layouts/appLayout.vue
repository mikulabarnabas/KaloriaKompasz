<script setup>
import Navbar from "@/Components/navbar.vue"
import Footer from "@/Components/footer.vue"
</script>

<template>
<div class="min-h-screen bg-background-dark text-white font-display">
  <Navbar/>
  <main>
    <slot/>
  </main>
  <Footer/>
</div>
</template>
